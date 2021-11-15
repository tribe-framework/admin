<?php
require_once __DIR__ . '/init.php';

$log = null;
$save_activity_log = isset($types['webapp']['display_activity_log']) && $types['webapp']['display_activity_log'];

if ($save_activity_log && ($_POST['function'] != 'do_delete')) {
    $log = $dash->get_content_meta($_POST['id'], 'mysql_access_log');

    $_POST['mysql_access_log'] = $log ? json_decode($log, 1) : [];
}

// calls push_content or do_delete from $dash (based on form request)
${$_POST['class']}->{$_POST['function']}($_POST);

$last_query = [
    'last_error' => ${$_POST['class']}->get_last_error(),
    'last_info' => ${$_POST['class']}->get_last_info(),
    'last_data' => ${$_POST['class']}->get_last_data(),
    'last_redirect' => ${$_POST['class']}->get_last_redirect()
];

if ($save_activity_log && ($_POST['function'] != 'do_delete')) {
    if (!is_numeric($_POST['id'])) {
        $_POST['id'] = $last_query['last_data'][0]['id'] ?? null;
    }

    $res = $dash->logAdminActivity($_POST['id'], $currentUser);
}

echo json_encode($last_query);
