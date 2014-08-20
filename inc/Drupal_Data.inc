<?php

class Drupal_Data implements ground_IStorage {

  private $vineyard;
  public $drupal_schema;

  function __construct($vineyard) {
    $this->vineyard = $vineyard;
  }

  function get_entity_info($trellis_name) {
    $entity_name = $this->get_entity_name($trellis_name);

    $entity_info = entity_get_info();
    $info = null;
    if (array_key_exists($entity_name, $entity_info)) {
      return $entity_info[$entity_name];
    }
    else {
      return $entity_info['node'];
    }
  }

  function get_trellis_map($trellis_name) {
    return property_exists($this->drupal_schema, $trellis_name)
      ? $this->drupal_schema->{$trellis_name}
      : null;
  }

  function get_entity_name($trellis_name) {
    $trellis_map = $this->get_trellis_map($trellis_name);

    return $trellis_map && isset($trellis_map->name) ? $trellis_map->name : $trellis_name;
  }

  function run_query($query, $response) {
    if (isset($this->additional)) {
      if (isset($this->additional->view)) {
        $query->view = $this->additional->view;
        if (isset($this->additional->args)) {
          $query->args = $this->additional->args;
        }
      }
    }

    $entity_name = $this->get_entity_name($query->trellis->name);
    $response->objects = array();
    $entity_type = 'node';
    $entity_info = entity_get_info();
    $info = null;
    if (array_key_exists($entity_name, $entity_info)) {
      $entity_type = $entity_name;
      $info = $entity_info[$entity_name];
    }
    else {
      $info = $entity_info['node'];
    }

//    $nids = get_nids_using_entity_query($query, $entity_type, $trellis_map);
    $nids = $this->get_nids_using_view($query, $entity_name, $response, $info);
    if (isset($query->view)) {
      $response->objects = $nids;
      return;
    }

    if (count($nids) > 0) {
      $entities = entity_load($entity_type, $nids);
      foreach ($entities as $entity) {
        $response->objects[] = $this->post_process($entity, $query);
      }
    }
  }

  function filter_by_view($query, &$options, $info, $entity_name) {
    if ($info['base table'] == 'node') {
      $filter = new stdClass();
      $filter->value = $entity_name;
      $this->add_standard_field_filter("type", $filter, $options, $info);
    }

    if (!isset($query->filters) || !is_array($query->filters))
      return;

//    $properties = $query->trellis->get_all_properties();

    foreach ($query->filters as $filter) {
      $path = explode('.', $filter->path);
      $name = $path[0];

      if (isset($filter->operator) && $filter->operator == 'distance') {
        $options['filters']['field_geofield_distance'] = $this->proximity($path, $filter);
        continue;
      }

      $field_info = field_info_field($name);

      if (!$field_info) {
        $flag = flag_get_flag($name);
        if ($flag) {
          $this->add_flag_filter($name, $filter->value, $options, $info);
          continue;
        }
      }
      else {
        if ($field_info['module'] == 'taxonomy') {
          $this->taxonomy($name, $filter, $options);
        }
      }

      $this->add_standard_field_filter($name, $filter, $options, $info);
    }

  }

  function add_standard_field_filter($name, $filter, &$options, $info) {
//    $filter_name = $name . (count($options['filters']) + 1);
    $value = $filter->value;
    if ($info['base table'] == 'node' && $name == 'type') {
      $value = array();
      $value[$filter->value] = $filter->value;
    }
    $result = array(
      'id' => $name,
      'table' => $info['base table'],
      'field' => $name,
      'value' => is_int($value) ? (string)$value : $value,
      'group_type' => 'group',
      'group' => '1',
    );
    if (isset($filter->operator)) {
      if ($filter->operator == 'like' || $filter->operator == 'LIKE')
        $filter->operator = 'contains';

      $result['operator'] = $filter->operator;
    }

    $options['filters'][$name] = $result;
  }

  function add_flag_filter($name, $value, &$options, $info) {
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
      'is_grouped' => true,
    );

    $options['relationships'][$relationship_name] = array(
      'id' => 'flag_content_rel',
      'table' => $info['base table'],
      'field' => 'flag_content_rel',
      'relationship' => 'none',
      'group_type' => 'group',
      'flag' => $name,
      'user_scope' => 'current',
    );
  }

  function proximity($path, $filter) {
    $value = (object)$filter->value;

    $filter_value = array(
      'unit' => '3959',
      'origin' => array(
        'lat' => $value->lat,
        'lon' => $value->lon,
      ));

    if (isset($value->max))
      $filter_value['distance'] = $value->max;

    return array(
      'id' => 'field_geofield_distance',
      'table' => 'field_data_field_location',
      'field' => 'field_geofield_distance',
      'relationship' => 'none',
      'group_type' => 'group',
      'ui_name' => '',
      'operator' => '<=',
      'value' => $filter_value,
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

  function get_nids_using_view($query, $entity_name, $response, $info) {
    if (isset($_GET['debug-view'])) {
      $view = views_get_view($_GET['debug-view']);
      print var_export($view->display['default']->display_options);
      exit(0);
    }

    $view = null;

    if (isset($query->view)) {
      $view = views_get_view($query->view);
      if (isset($query->args)) {
        $view->set_arguments($query->args);
      }

      $options = &$view->display['default']->display_options;
    }
    else {
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

      $view->display['default']->display_options = &$options;
    }

    $this->filter_by_view($query, $options, $info, $entity_name);

    if (isset($query->range)) {
      $this->add_range($query, $options);
    }

    $this->sort($query, $options, $info);

    module_invoke_all('vineyard_query_view', $view, $query, $info, $entity_name, $this->vineyard);

    $view->set_display('default');

    $view->pre_execute();
    $view->execute();
    $result = array();
    if (user_access('debug vineyard')) {
      $response->sql = (string)$view->build_info['query'];
      if (isset($_GET['print-view']) && $_GET['print-view'] == 'true')
        $response->view = $view->display['default']->display_options;
    }

    if (isset($query->view)) {
      $result = $view->result;
      foreach ($result as $entity) {
        foreach($entity as $x => $property) {
          if (is_array($property)) {
            foreach($property as $key => $item) {
              if(isset($item['rendered'])) {
                unset($entity->{$x}[$key]['rendered']);
              }
              if(isset($item['raw'])) {
                $entity->{$x}[$key] = $item['raw'];
              }
            }
          }
        }
      }
      return $result;
    }

    foreach ($view->result as $entity) {
      $result[] = $query->trellis->get_identity($entity);
    }
    return $result;
  }

  function run_update($update, $response) {

  }

  function post_process($entity, $query) {
    $this->get_links($entity, $query);
    $this->expansions($entity, $query);

    $properties = $query->trellis->get_all_properties();

    if (isset($query->properties)) {
      $result = new stdClass();
      $keys = isset($query->expansions)
        ? array_merge($query->properties, $query->expansions)
        : $query->properties;

      $result->nid = $entity->nid;
      foreach ($keys as $key) {
        if (isset($properties->{$key})) {
          $result->{$key} = $this->process_property($entity, $query, $properties->{$key});
        }
        else {
          $result->{$key} = $entity->{$key};
        }
      }

      return $result;
    }
    else {
      foreach ($properties->h as $property) {
        $entity->{$property->name} = $this->process_property($entity, $query, $property);
      }
    }

    return $entity;
  }

  function get_property_info($property) {
    $trellis_map = $this->get_trellis_map($property->trellis->name);
    return $trellis_map
    && isset($trellis_map->properties)
    && property_exists($trellis_map->properties, $property->name)
      ? $trellis_map->properties->{$property->name}
      : null;
  }

  function process_property($entity, $query, $property) {
    $mapping = $this->get_property_info($property);
    if ($mapping) {
      if ($mapping->type == 'flag') {
        $flag = flag_get_flag($mapping->source);
        return $flag->is_flagged($query->trellis->get_identity($entity));
      }
    }

    return isset($entity->{$property->name}) ? $entity->{$property->name} : null;
  }

  function expansions($entity, $query) {
    if (isset($query->expansions) && is_array($query->expansions)) {
      $properties = $query->trellis->get_all_properties();
      foreach ($query->expansions as $expansion) {
        if ($expansion == 'children'
          && isset($entity->nodehierarchy_menu_links)
          && count($entity->nodehierarchy_menu_links) > 0
        ) {
          $tree = $entity->nodehierarchy_menu_links[0];
          $sql = 'SELECT link_path FROM `menu_links` where plid = :plid';
          $rows = db_query($sql, array('plid' => $tree['mlid']))->fetchAll();
          foreach ($rows as $key => $row) {
            $rows[$key] = (int)preg_replace('/[^\d]+/', '', $row->link_path);
          }
          $children = entity_load('node', $rows);
          $rows = array();
          foreach ($children as $child) {
            $rows[] = $child;
          }
          $entity->children = $rows;
        }
        else {
          $property = $properties->h[$expansion];
          if (!isset($property))
            throw new Exception("There is no property defined for " . $query->trellis->name . "." . $expansion);

          $info = $this->get_entity_info($property->other_trellis->name);
          $entity_name = $info['base table'] == 'node'
            ? 'node'
            : $this->get_entity_name($property->other_trellis->name);
          $id = $entity->{$expansion};
          $result = entity_load($entity_name, array($id));
          $child = $result[$id];
          $entity->{$expansion} = $child;
        }
      }
    }
  }

  private function add_range($query, &$options) {
    $start = isset($query->range) && isset($query->range->start) ? $query->range->start : 0;
    $length = isset($query->length) && isset($query->range->length) ? $query->range->length : 0;
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
      if (isset($sort->operator) && $sort->operator == 'distance') {
        $options['sorts'][$sort->path] = $this->proximity($sort->path, $sort);
        continue;
      }
      else {
        $options['sorts'][$sort->path] = array(
          'id' => $sort->path,
          'table' => $info['base table'],
          'field' => $sort->path,
          'order' => $dir,
        );
      }
    }
  }

  function get_links($entity, $query) {
    $properties = $query->trellis->get_all_properties();
    foreach ($properties->h as $property) {
      if (isset($query->properties) && !in_array($property->name, $query->properties))
        continue;

      if (!isset($property->other_property))
        continue;

      if ($property->other_property->type == 4) { // 3: Reference
        $base_name = $property->other_property->name;
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

    $result = array(
      'id' => $key,
      'table' => 'taxonomy_term_data',
      'field' => $key,
      'relationship' => $relationship_name,
      'value' => $filter->value,
      'exposed' => false,
      'is_grouped' => false,
    );
    if (isset($filter->operator))
      $result->operator = $filter->operator;

    $options['filters'][$path[0]] = $result;
  }
}