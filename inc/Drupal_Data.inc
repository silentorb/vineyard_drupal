<?php

class Drupal_Data implements ground_IStorage {

  private $vineyard;
  public $drupal_schema;

  function __construct($vineyard) {
    $this->vineyard = $vineyard;
  }

  /*
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
  */
  function run_query($query, $response) {
    $trellis_map = property_exists($this->drupal_schema, $query->trellis->name)
      ? $this->drupal_schema->{$query->trellis->name}
      : null;

    $name = $trellis_map && isset($trellis_map->name) ? $trellis_map->name : $query->trellis->name;

    $response->objects = array();
    $entity_type = 'node';
    $entity_info = entity_get_info();
    $info = null;
    if (array_key_exists($name, $entity_info)) {
      $entity_type = $name;
      $info = $entity_info[$name];
    } else {
      $info = $entity_info['node'];
    }

//    $nids = get_nids_using_entity_query($query, $entity_type, $trellis_map);
    $nids = $this->get_nids_using_view($query, $trellis_map, $response, $info);

    if (count($nids) > 0) {
      $entities = entity_load($entity_type, $nids);
      foreach ($entities as $entity) {
        $response->objects[] = $this->post_process($entity, $query, $trellis_map);
      }
    }
  }

  /*
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
  */
  function filter_by_view($query, &$options, $info) {
    if (!isset($query->filters) || !is_array($query->filters))
      return;

//    $properties = $query->trellis->get_all_properties();

    foreach ($query->filters as $filter) {
      $path = explode('.', $filter->path);
      $name = $path[0];
      $filter->operator = isset($filter->operator) ? $filter->operator : "=";

      if ($filter->operator == 'distance') {
        $options['filters']['field_geofield_distance'] = $this->proximity($path, $filter);
        continue;
      }

      $field_info = field_info_field($name);

      if (!$field_info) {
        $flag = flag_get_flag($name);
        if ($flag) {
          $this->add_flag_filter($name, $filter->value, $options);
          continue;
        }
      } else {
        if ($field_info['module'] == 'taxonomy') {
          $this->taxonomy($name, $filter, $options);
        }

        $this->add_standard_field_filter($name, $filter, $options, $info);
      }
    }
  }

  function add_standard_field_filter($name, $filter, &$options, $info) {
//    $filter_name = $name . (count($options['filters']) + 1);
    $options['filters'][$name] = array(
      'id' => $name,
      'table' => $info['base table'],
      'field' => $name,
      'relationship' => 'none',
      'group_type' => 'group',
      'operator' => $filter->operator,
      'value' => $filter->value,
      'exposed' => false,
      'is_grouped' => false,
    );
  }

  function add_flag_filter($name, $value, &$options) {
    $relationship_name = 'flag_relationship_' . $name;
    $options['filters']['flagged'] = array(
      'id' => 'flagged',
      'table' => 'flagging',
      'field' => 'flagged',
      'relationship' => $relationship_name,
      'group_type' => 'group',
      'operator' => '=',
      'value' => $value,
      'group' => '1',
      'exposed' => false,
      'is_grouped' => false,
    );

    $options['relationships'][$relationship_name] = array(
      'id' => 'flag_content_rel',
      'table' => 'node',
      'field' => 'flag_content_rel',
      'relationship' => 'none',
      'group_type' => 'group',
      'flag' => $name,
      'user_scope' => 'current',
    );
  }

  function proximity($path, $filter) {
    $value = (object)$filter->value;

    return array(
      'id' => 'field_geofield_distance',
      'table' => 'field_data_field_location',
      'field' => 'field_geofield_distance',
      'relationship' => 'none',
      'group_type' => 'group',
      'ui_name' => '',
      'operator' => '<=',
      'value' => array(
        'distance' => $value->max,
        'unit' => '3959',
        'origin' => array(
          'lat' => $value->lat,
          'lon' => $value->lon,
        ),
      ),
      'group' => '1',
      'is_grouped' => false,
      'group_info' => array(
        'label' => '',
        'description' => '',
        'identifier' => '',
        'optional' => true,
        'widget' => 'select',
        'multiple' => false,
        'remember' => 0,
        'default_group' => 'All',
        'default_group_multiple' =>
          array(),
        'group_items' =>
          array(),
      ),
      'source' => 'manual',
      'geofield_proximity_manual' => array(
        'lat' => 0,
        'lon' => 0,
      ),
      'geofield_proximity_entity_url_entity_type' => 'node',
      'geofield_proximity_entity_url_field' => $path[0],
      'geofield_proximity_entity_url_delta' => 0,
      'geofield_proximity_current_user_field' => $path[0],
      'geofield_proximity_other_geofield' => '',
    );
  }

  function get_nids_using_view($query, $trellis_map, $response, $info) {
    if (isset($_GET['debug-view'])) {
      $view = views_get_view($_GET['debug-view']);
      print var_export($view->display['default']->display_options);
      exit(0);
    }
    // Create view
    $view = views_new_view();
    $view->core = 7;

    $view->base_table = $info['base table'];
    $view->base_field = $info['entity keys']['id'];

    $options = array(
      'query' => array(
        'type' => 'views_query',
        'options' => array(),
      ),
      'filters' => array()
    );

    $options['fields'][$view->base_field] = array(
      'table' => $view->base_table,
      'field' => $view->base_field,
      'id' => $view->base_field,
    );


    $this->filter_by_view($query, $options, $info);

    if (isset($query->range)) {
      $this->add_range($query->range, $options);
    }

    $this->sort($query, $options, $info);

    $view->display['default']->display_options = $options;
//    if (isset($_GET['view'])) {
//      $view2 = views_get_view($_GET['view']);
//      print var_export($view->display['default']->display_options);
//      print('cow');
//      print var_export($view2->display['default']->display_options);
//      exit(0);
//    }
    $view->set_display('default');

    $view->pre_execute();
    $view->execute();
    $result = array();
    if (user_access('debug vineyard'))
      $response->sql = (string)$view->build_info['query'];

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
        } else {
          $result->{$key} = $entity->{$key};
        }
      }

      return $result;
    } else {
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

    return isset($entity->{$property->name}) ? $entity->{$property->name} : null;
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

  private function add_range($range, &$options) {
    $start = isset($range->start) ? $range->start : 0;
    $length = isset($range->length) ? $range->length : 0;
    $options['pager'] = array(
      'type' => 'some',
      'options' => array(
        'items_per_page' => $length,
        'offset' => $start,
      ),
    );
  }

  private function sort($query, &$options, $info) {
    if (!isset($query->sorts) || !is_array($query->sorts))
      return;

    foreach ($query->sorts as $sort) {
      $dir = isset($sort->dir) ? $sort->dir : 'DESC';
      $options['sorts'][$sort->path] = array(
        'id' => $sort->path,
        'table' => $info['base table'],
        'field' => $sort->path,
        'order' => $dir,
      );
    }
  }

  function get_links($entity, $query) {
    $properties = $query->trellis->get_all_properties();
    foreach ($properties->h as $property) {
      if (isset($query->properties) && !in_array($property->name, $query->properties))
        continue;

      if (!isset($property->other_property))
        continue;

      if ($property->other_property->type == 3) { // 3: Reference
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

  function taxonomy($path, $filter, &$options) {
    $relationship_name = 'term_relationship_' . $path[0];
    $options['relationships'][$relationship_name] = array(
      'id' => 'field_tags_tid',
      'table' => 'field_data_field_tags',
      'field' => 'field_tags_tid',
      'relationship' => 'none',
    );

    $key = isset($path[1]) && $path[0] == 'name'
      ? 'name'
      : 'tid';

    $options['filters'][$path[0]] = array(
      'id' => $key,
      'table' => 'taxonomy_term_data',
      'field' => $key,
      'relationship' => $relationship_name,
      'operator' => $filter->operator,
      'value' => $filter->value,
      'exposed' => false,
      'is_grouped' => false,
    );
  }
}