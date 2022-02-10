<?php
require_once __DIR__ . '/../_init.php';

$log = null;
$save_activity_log = $types['webapp']['display_activity_log'] ?? false;

if ($save_activity_log && ($_POST['function'] != 'do_delete')) {
    if (is_numeric($_POST['id'])) {
        $log = $dash->getAttribute($_POST['id'], 'mysql_activity_log');
        $_POST['mysql_activity_log'] = $log ? json_decode($log, 1) : [];
    } else {
        $_POST['mysql_activity_log'] = array();
    }
}

// calls push_content or do_delete from $dash (based on form request)
${$_POST['class']}->{$_POST['function']}($_POST);

$last_query = [
    'last_error' => ${$_POST['class']}->get_last_error(),
    'last_info' => ${$_POST['class']}->get_last_info(),
    'last_data' => ${$_POST['class']}->get_last_data(),
    'last_redirect' => ${$_POST['class']}->get_last_redirect()
];

$api = new \Wildfire\Api;
$api->json($last_query)->send();
