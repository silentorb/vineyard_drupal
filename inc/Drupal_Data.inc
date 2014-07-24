<?php

class Drupal_Data implements ground_IStorage {

  private $vineyard;
  public $drupal_schema;

  function __construct($vineyard) {
    $this->vineyard = $vineyard;
  }

  private function process_filters($query, $entity_query, $trellis_map) {
    if (!isset($query->filters) || !is_array($query->filters))
      return;

    $properties = $query->trellis->get_all_properties();

    foreach ($query->filters as $filter) {
      $path = explode('.', $filter->path);

      $mapping = count($path) == 1 && isset($properties->h{$path[0]})
        ? Drupal_Data::get_property_info($trellis_map, $properties->h->{$path[0]})
        : null;

//      if ($mapping && $mapping->type == 'flag') {
//        $entity_query->entityCondition('entity_type', 'flagging')
//          ->entityCondition('bundle', $mapping->source);
//      }

      $info = field_info_field($path[0]);

      if ($info) {
        if ($info['module'] == 'taxonomy') {
          if (isset($path[1]) && $path[1] == 'name') {
            $value = taxonomy_get_term_by_name($filter->value);
            if (count($value) == 0)
              throw new Exception("Could not find tag $filter->value.");

            $entity_query->fieldCondition($path[0], 'tid', $value[1]->tid);
          }
          else {
            $entity_query->fieldCondition($path[0], 'tid', $filter->value);
          }
        }
        else {
          $entity_query->fieldCondition($path[0], 'value', $filter->value);
        }
      }
      else {
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
    $trellis_map = property_exists($this->drupal_schema, $query->trellis->name)
      ? $this->drupal_schema->{$query->trellis->name}
      : null;

    $response->objects = array();
    $entity_type = 'node';
    $entity_info = entity_get_info();
    if (array_key_exists($query->trellis->name, $entity_info)) {
      $entity_type = $query->trellis->name;
    }

//    $nids = get_nids_using_entity_query($query, $entity_type, $trellis_map);
    $nids = $this->get_nids_using_view($query, $entity_type, $trellis_map);

    if (count($nids) > 0) {
      $entities = entity_load($entity_type, $nids);
      foreach ($entities as $entity) {
        $response->objects[] = $this->post_process($entity, $query, $trellis_map);
      }
    }
  }

  function get_nids_using_entity_query($query, $entity_type, $trellis_map) {
    $entity_query = new EntityFieldQuery();
    $entity_query
      ->entityCondition('entity_type', $entity_type);

    if ($entity_type == 'node') {
      $entity_query->propertyCondition('status', 1)
        ->entityCondition('bundle', $query->trellis->name);
    }

    $this->process_filters($query, $entity_query, $trellis_map);
    $this->process_sorts($query, $entity_query);

    if (isset($query->range)) {
      $start = isset($query->range->start) ? $query->range->start : 0;
      $length = isset($query->range->length) ? $query->range->length : 0;
      $entity_query->range($start, $length);
    }

    $result = $entity_query->execute();

    return isset($result[$entity_type])
      ? array_keys($result[$entity_type])
      : array();
  }

  function get_nids_using_view($query, $entity_type, $trellis_map) {
    // Create view
    $view = views_new_view();
    $view->core = 7;

//    $view->base_table = 'node';
    $view2 = views_get_view('articles');
    $view->display['default']->display_options = array(
      'query' => array(
        'type' => 'views_query',
        'options' => array(),
      ),
      'fields' => array(
        'title' => array(
          "id" => "title",
          "table" => "node",
          "field" => "title",
        ),
      ),
      'filters' => array(),
      'row_plugin' => 'fields',
      'access' => array(
        'type' => 'perm'
      ),
      'exposed_form' => array(
        'type' => 'basic'
      )
    );
//        $view->display['default']->display_options = $view2->display['default']->display_options;

//    $view = $view2;
    $view->set_display('default');
//    print_r($view);
//    print "cow";
//    print_r($view2);
//    exit(0);
    // Run view
    $view->pre_execute();
    $view->execute();
//    $result = $view->render();
    $result = array();
    foreach ($view->result as $entity) {
      $result[] = $query->trellis->get_identity($entity);
    }
    return $result;
  }

  function run_update($update, $response) {

  }

  function post_process($entity, $query, $trellis_map) {
    $this->node_hierarchy($entity, $query);
    $this->get_links($entity, $query);

    $properties = $query->trellis->get_all_properties();

    if (isset($query->properties)) {
      $result = new stdClass();
      $keys = isset($query->expansions)
        ? array_merge($query->properties, $query->expansions)
        : $query->properties;

      $result->nid = $entity->nid;
      foreach ($keys as $key) {
        if (isset($properties->{$key})) {
          $result->{$key} = $this->process_property($entity, $query, $properties->{$key}, $trellis_map);
        }
        else {
          $result->{$key} = $entity->{$key};
        }
      }

      return $result;
    }
    else {
      foreach ($properties->h as $property) {
        $entity->{$property->name} = $this->process_property($entity, $query, $property, $trellis_map);
      }
    }

    return $entity;
  }

  static function get_property_info($trellis_map, $property) {
    return $trellis_map
    && isset($trellis_map->properties)
    && property_exists($trellis_map->properties, $property->name)
      ? $trellis_map->properties->{$property->name}
      : null;
  }

  function process_property($entity, $query, $property, $trellis_map) {
    $mapping = Drupal_Data::get_property_info($trellis_map, $property);
    if ($mapping) {
      if ($mapping->type == 'flag') {
        $flag = flag_get_flag($mapping->source);
        return $flag->is_flagged($query->trellis->get_identity($entity));
      }
    }

    return $entity->{$property->name};
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
        $base_name = $property->name;
        $field_name = 'field_' . $base_name . '_target_id';
        $sql = <<<SQL
SELECT entity_id
FROM field_data_field_$base_name
WHERE $field_name = :id
SQL;

        $id = $query->trellis->get_identity($entity);
        $result = db_query($sql, array(':id' => $id));
        $value = (int)$result->fetchField(0);
        if (!$value)
          $value = null;

        $entity->{$property->name} = $value;
      }
    }
  }
}