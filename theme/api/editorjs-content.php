<?php
/**
 * this api fetches and saves editorjs forms
 */
use \Wildfire\Api;
use \Wildfire\Core\Dash;
use \Wildfire\Core\MySQL;

$api = new Api;
$sql = new MySQl;
$dash = new Dash;

/**
 * requires id, name & data
 */
if ($api->method('post')) {
    $request = $api->body();

    $data_row = $sql->executeSQL("Select content from data where id = {$request['id']} order by id desc limit 0,1");

    if ($data_row === 0) {
        $api->json(['ok' => false, 'error' => 'no records found with this id'])->send();
    }

    $data_row = json_decode($data_row[0]['content'], 1);

    $data_row[$request['moduleName']] = $request['data'];

    $dash->pushObject($data_row);

    $api->json(['ok' => true, 'msg' => 'saved successfully'])->send();
}

/**
 * requires ?id, ?moduleName
 */
if ($api->method('get')) {
    if (!(isset($_GET['id']) && isset($_GET['moduleName']))) {
        $api->json(['error' => 'id and moduleName are necessary'])->send(400);
    }

    if (isset($_GET['moduleName']) && trim($_GET['moduleName']) && (!isset($_GET['id']) || !trim($_GET['id']))) {
        $api->json(['ok' => false, 'error' => "id invalid, possibly new form request"])->send();
    }

    $response['data'] = $dash->getAttribute($_GET['id'], $_GET['moduleName']) ?? null;
    if (!$response['data']) {
        $api->json(['ok' => false])->send();
    }
    $response['data'] = json_decode($response['data'], 1);
    $response['ok'] = true;

    $api->json($response)->send();
}

$api->json(['error' => 'method not allowed'])->send(405);
