<?php

class Vineyard {
  public $hub;
  public $ground;
  public $namespace;
  private $operator;

  function __construct() {
    $this->hub = new metahub_Hub();
    $this->operator = new Drupal_Data($this);
    $this->ground = new ground_Ground($this->hub, $this->operator);
    $this->namespace = $this->hub->schema->add_namespace('vineyard');
  }

  function load_schema_from_file($filename) {
    $data = metahub_Utility::load_json($filename);
    $this->hub->load_schema_from_object($data, $this->namespace, true);
    $this->operator->drupal_schema = isset($data->drupal)
      ? $data->drupal
      : new stdClass();
  }

  private function query($source, $result) {
    if (!isset($source->version))
      throw new Exception("Missing version property.  Should be 0.1");

    if ((float)$source->version !== 0.1)
      throw new Exception("Only supported version is currently 0.1, not " . $source->version);

    if (!isset($source->filters))
      $source->filters = null;

    if (!isset($source->range))
      $source->range = null;

    if (!isset($source->sorts))
      $source->sorts = null;

    if (!isset($source->expansions))
      $source->expansions = null;

    if (!isset($source->properties))
      $source->properties = null;

    $this->operator->additional = new stdClass();
    if (isset($source->view)) {
      $this->operator->additional->view = $source->view;
    }

    if (isset($source->view)) {
      $this->operator->additional->view = $source->view;
      if (isset($source->args)) {
        $this->operator->additional->args = $source->args;
      }
    }

    $this->ground->run_query($source, $result, $this->namespace);
  }

  private function update($data, $result) {
    $objects = $data->objects;
//
//    if (!$objects)
//      throw new Exception('Missing object list', 400);
//
//    if (!count($objects))
//      throw new Exception('Object list is empty', 400);

    $result->result = $this->ground->update($data);
    return $objects;
  }

  function service($action) {
    $result = new stdClass();

    try {
      $json = file_get_contents("php://input");
      $data = json_decode(utf8_encode($json));
      if (!$data)
        throw new Exception('Either the request was empty or the JSON was invalid.', 400);

      if ($action == 'query') {
        Vineyard::query($data, $result);
      } else {
        Vineyard::update($data, $result);
      }

    } catch (Exception $ex) {
      // Working around Haxe-PHP goofiness
      if (get_class($ex) == 'HException') {
        $ex = $ex->e;
      }

      if (is_string($ex)) {
        $code = '500';
        drupal_add_http_header('Status', $code . ' Server Error');
        if (user_access('administer permissions')) {
          $result->message = $ex;
        }
      } else {
        $code = $ex->getCode();
        if (!$code)
          $code = '500';

        if ($code == 500 && !user_access('administer permissions')) {
          $result->message = 'Internal Server Error';
          drupal_add_http_header('Status', $code);
        } else {
          $result->message = $ex->getMessage();
          $result->stack_trace = $ex->getTraceAsString();
          if (strlen($result->message) < 32)
            drupal_add_http_header('Status', $code . ' ' . $ex->getMessage());
          else
            drupal_add_http_header('Status', $code . ' ' . substr($result->message, 0, 32) . '...');
        }
      }
    }

    drupal_add_http_header('Content-Type', 'application/json');
    print json_encode($result);
  }
} 