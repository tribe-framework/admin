<?php
require_once __DIR__ . '/../_init.php';

$api = new \Wildfire\Api;

if ($_POST['id'])
	$or = $dash->getObject($_POST['id']);
else
	$or = [];

$api->method('post');
$api->json($or)->send();