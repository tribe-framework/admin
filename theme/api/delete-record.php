<?php

use \Wildfire\Api;
use \Wildfire\Core\Dash;

$api = new Api;
$dash = new Dash;

if ($api->method('delete')) {
    try {
        $id = (int) $_GET['id'];
        $dash->doDeleteObject($id);
        $api->json(['status' => 'ok'])->send();
    } catch (\Exception $e) {
        $api->json(['error' => 'invalid id'])->send(400);
    }
}
