<?php

// $Id: $

/**
 *
 * @return string
 */
function docebo_mycourses_display() {
  $res = '';

  $data = docebo_get_mycourses_data();
  $xml = new SimpleXMLElement($data);
  $nodes = $xml->xpath('//course_info');
  $auth = doceboGetAuth(doceboGetDrupalUsername());

  $res = theme('course_list', array('nodes' => $nodes, 'auth' => $auth));

  return $res; //var_export($nodes, true).var_export($data, true).$GLOBALS['user']->uid;
}
