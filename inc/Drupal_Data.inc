<?php

class Drupal_Data implements ground_IStorage {

  private function process_filters($query, $entity_query) {
    if (!isset($query->filters) || !is_array($query->filters))
      return;

    foreach ($query->filters as $filter) {
      $path = explode($filter->path, '.');
      $info = field_info_field($path[0]);
      if ($info) {
        if ($info['module'] == 'taxonomy') {
          if (isset($path[1]) && $path[1] == 'name') {
            $value = taxonomy_get_term_by_name($filter->value);
            if (count($value) == 0)
              throw new Exception("Could not find tag $filter->value.");

            $entity_query->fieldCondition($path[0], 'tid', $value[1]->tid);
          } else {
            $entity_query->fieldCondition($path[0], 'tid', $filter->value);
          }
        } else {
          $entity_query->fieldCondition($path[0], $filter->value);
        }
      } else {
        $entity_query->propertyCondition($path[0], $filter->value);
      }

    }
  }

  private function process_sorts($query, $entity_query) {
    if (!isset($query->sorts) || !is_array($query->sorts))
      return;

    foreach ($query->sorts as $sort) {
      $dir = isset($sort->dir) ? $sort->dir : 'DESC';
        $entity_query->propertyOrderBy($sort->path, $dir);
    }
  }

  function run_query($query, $response) {
    $response->objects = array();
    $entity_query = new EntityFieldQuery();
    $entity_query
      ->entityCondition('entity_type', 'node')
      ->entityCondition('bundle', $query->trellis->name)
      ->propertyCondition('status', 1);
//      ->propertyOrderBy('created', 'DESC')
//      ->fieldCondition('field_foo', 'tid', array('1', '2'))
//      ->range(0,10);

    $this->process_filters($query, $entity_query);
    $this->process_sorts($query, $entity_query);

    if (isset($query->range)) {
      $start = isset($query->range->start) ? $query->range->start : 0;
      $length = isset($query->range->length) ? $query->range->length : 0;
      $entity_query->range($start, $length);
    }

    $result = $entity_query->execute();

    if (isset($result['node'])) {
      $nids = array_keys($result['node']);
      $nodes = entity_load('node', $nids);
      foreach ($nodes as $node) {
        $response->objects[] = $this->post_process($node, $query);;
      }
    }
  }

  function run_update($update, $response) {

  }

  function post_process($node, $query) {
    if (isset($query->expansions) && in_array('children', $query->expansions)) {
      $sql = 'SELECT link_path FROM `menu_links` where plid = :plid';
    }

    if (isset($node->nodehierarchy_menu_links) && count($node->nodehierarchy_menu_links) > 0) {
      $tree = $node->nodehierarchy_menu_links[0];
      $sql = 'SELECT link_path FROM `menu_links` where plid = :plid';
      $rows = db_query($sql, array('plid' => $tree['mlid']))->fetchAll();
      foreach ($rows as $key => $row) {
        $rows[$key] = (int)preg_replace('/[^\d]+/', '', $row->link_path);
      }
      if (isset($query->expansions) && in_array('children', $query->expansions)) {
        $children = entity_load('node', $rows);
        $rows = array();
        foreach ($children as $child) {
          $rows[] = $child;
        }
      }
      $node->children = $rows;
    }

    if (isset($query->properties)) {
      $result = new stdClass();
      $keys = isset($query->expansions)
        ? array_merge($query->properties, $query->expansions)
        : $query->properties;

      $result->nid = $node->nid;
      foreach ($keys as $key) {
        $result->{$key} = $node->{$key};
      }

      return $result;
    }

    return $node;
  }

}