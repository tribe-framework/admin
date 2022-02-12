<?php
require_once __DIR__ . '/../_init.php';

$api = new \Wildfire\Api;

$or=array();

$or['html']=$dash->getAttribute(array('type'=>$_POST['type'], 'slug'=>$_POST['slug']), $_POST['field']);

$api->method('post');
$api->json($or)->send();