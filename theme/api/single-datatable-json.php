<?php
require_once __DIR__ . '/../_init.php';

$fn = new \Wildfire\Admin\Functions;
$api = new \Wildfire\Api;

if (isset($_POST['id']))
	$or = $fn->getDatatableRowArray($dash->getObject($_POST['id']));
else
	$or = [];

$api->method('post');
$api->json($or)->send();