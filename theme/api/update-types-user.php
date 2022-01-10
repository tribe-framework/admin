<?php

$api = new \Wildfire\Api;

$json_encode_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK;

$_post = $api->body();
$_types = file_get_contents(TRIBE_ROOT.'/config/types.json');

if (!$_types) {
    $api->send(500);
}

$_types = json_decode($_types, 1);
$_types['user'] = array_replace($_types['user'], $_post);

$_types = json_encode($_types, $json_encode_options);
\file_put_contents(TRIBE_ROOT.'/config/types.json', $_types);

$api->json(['status' => 'ok'])->send();
