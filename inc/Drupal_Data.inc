<?php

class Drupal_Data implements ground_IStorage {

  private function process_filters($query, $entity_query) {
    if (!isset($query->filters) || !is_array($query->filters))
      return;

    foreach ($query->filters as $filter) {
      $path = explode('.', $filter->path);
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
          $entity_query->fieldCondition($path[0], 'value', $filter->value);
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
    $entity_type = 'node';
    $entity_query = new EntityFieldQuery();
    $entity_info = entity_get_info();
    if (array_key_exists($query->trellis->name, $entity_info)) {
      $entity_type = $query->trellis->name;
    }

    $entity_query
      ->entityCondition('entity_type', $entity_type);

    if ($entity_type == 'node') {
      $entity_query->propertyCondition('status', 1)
        ->entityCondition('bundle', $query->trellis->name);
    }

    $this->process_filters($query, $entity_query);
    $this->process_sorts($query, $entity_query);

    if (isset($query->range)) {
      $start = isset($query->range->start) ? $query->range->start : 0;
      $length = isset($query->range->length) ? $query->range->length : 0;
      $entity_query->range($start, $length);
    }

    $result = $entity_query->execute();

    if (isset($result[$entity_type])) {
      $nids = array_keys($result[$entity_type]);
      $entities = entity_load($entity_type, $nids);
      foreach ($entities as $entity) {
        $response->objects[] = $this->post_process($entity, $query);;
      }
    }
  }

  function run_update($update, $response) {

  }

  function post_process($entity, $query) {
//    if (isset($query->expansions) && in_array('children', $query->expansions)) {
//      $sql = 'SELECT link_path FROM `menu_links` where plid = :plid';
//    }

    $this->node_hierarchy($entity, $query);

    $this->get_links($entity, $query);

    if (isset($query->properties)) {
      $result = new stdClass();
      $keys = isset($query->expansions)
        ? array_merge($query->properties, $query->expansions)
        : $query->properties;

      $result->nid = $entity->nid;
      foreach ($keys as $key) {
        $result->{$key} = $entity->{$key};
      }

      return $result;
    }

    return $entity;
  }

  function node_hierarchy($entity, $query) {
    if (isset($entity->nodehierarchy_menu_links) && count($entity->nodehierarchy_menu_links) > 0) {
      $tree = $entity->nodehierarchy_menu_links[0];
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
      $entity->children = $rows;
    }
  }

  function get_links($entity, $query) {
    $properties = $query->trellis->get_all_properties();
    foreach ($properties->h as $property) {
      if (isset($query->properties) && !in_array($property->name, $query->properties))
        continue;

      if ($property->type == 3) { // 3: Reference
        $base_name = $property->other_property->name;
        $field_name = 'field_' . $base_name . '_target_id';
        $sql = <<<SQL
SELECT entity_id
FROM field_data_field_$base_name
WHERE $field_name = :id
SQL;

        $id = $query->trellis->get_identity($entity);
        $result = db_query($sql, array(':id' => $id));
        $entity->{$property->name} = (int)$result->fetchField(0);
      }
    }
  }
}