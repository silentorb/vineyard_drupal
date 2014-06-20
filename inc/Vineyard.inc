<?php

class Vineyard {
  public $hub;
  public $ground;

  function __construct() {
    $this->hub = new metahub_Hub();
    $this->ground = new ground_Ground($this->hub);
  }

  private function query() {

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
    $result->result = new stdClass();

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

    drupal_add_http_header('Content-Type', 'application/json');
    print json_encode($result);
  }
} 