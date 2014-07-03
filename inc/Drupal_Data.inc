<?php

class Drupal_Data implements ground_IStorage {

  function generate_pager($offset = 0, $limit = 0) {
    if ($offset == 0) {
      if ($limit == 0)
        return '';
      else
        return " LIMIT $limit";
    } else {
      if ($limit == 0) {
        $limit = 18446744073709551615;
      }

      return " LIMIT $offset, $limit";
    }
  }

  private function process_filters($query, $data) {
    if (!isset($query->filters) || !is_array($query->filters))
      return;

    foreach ($query->filters as $filter) {
      if ($filter->path == 'tags') {
        $data->joins[] = "JOIN taxonomy_index ON taxonomy_index.nid = node.nid";
        $data->joins[] = "JOIN taxonomy_term_data ON taxonomy_term_data.tid = taxonomy_index.tid";
        $data->filters[] = 'taxonomy_term_data.name = :tag';
        $data->args['tag'] = $filter->value;
      }
    }
  }

  function run_query($query, $response) {
    $response->objects = array();
    $data = new stdClass();
    $data->filters = array(
      'type = :type'
    );
    $data->joins = array();

    $data->args = array(
      ':type' => $query->trellis->name
    );

    $this->process_filters($query, $data);

    $sql = "SELECT node.nid FROM node "
      . implode("\n", $data->joins)
      . "\nWHERE " . implode(' AND ', $data->filters);

    if (isset($query->pager)) {
      $offset = isset($query->pager->offset) ? $query->pager->offset : 0;
      $limit = isset($query->pager->limit) ? $query->pager->limit : 0;
      $sql .= $this->generate_pager($offset, $limit);
    }

    $rows = db_query($sql, $data->args)->fetchAll();
    $nids = array();
    foreach ($rows as $row) {
      $nids[] = $row->nid;
    }

    $nodes = entity_load('node', $nids);

    foreach ($nodes as $node) {
      $this->post_process($node, $query);
      $response->objects[] = $node;
    }

    $response->sql = $sql;
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
      $rows = db_query($sql, array('plid'=> $tree['mlid']))->fetchAll();
      foreach ($rows as $key => $row) {
        $rows[$key] = (int)preg_replace('/[^\d]+/', '', $row->link_path);
      }
      if (isset($query->expansions) && in_array('children', $query->expansions)) {
        $nodes = entity_load('node', $rows);
        $rows = array();
        foreach ($nodes as $node) {
          $rows[] = $node;
        }
      }
      $node->children = $rows;
    }
  }

}