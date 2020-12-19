<?php
namespace Wildfire\Core;

include_once __DIR__.'/../../../tribe.init.php';

$dash = new Dash();
$admin = new Admin();
$theme = new Theme();

$types = $dash->getTypes();
$menus = $dash->getMenus();
$session_user = $dash->getSessionUser();

if (!$session_user['wildfire_dashboard_access']) {
    header('Location: /user/login');
    die();
} else {
    $type = $dash->do_unslugify($_GET['type']);
}
