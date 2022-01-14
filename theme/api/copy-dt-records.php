<?php

use \Wildfire\Api;
use \Wildfire\Core\Dash;

$api = new Api;
$dash = new Dash;
$sql = new \Wildfire\Core\MySQL;

if ($api->method('post')) {
    $_body = $api->body();

    $ids = implode(',', $_body['ids']);

    $query ="SELECT content from data where id in ($ids)";
    $records = $sql->executeSQL($query);
    $time = time();

    foreach ($records as $r) {
        $temp = json_decode($r['content']);
        $temp->title .= "-$time";

        unset($temp->id); // removing id to create a new record

        $dash->pushObject((array) $temp);
    }

    $api->send(200);
}

$api->send(400);
