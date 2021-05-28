<?php
namespace Wildfire\Core;

$dash = new Dash();
$admin = new Admin();
$theme = new Theme();

$types = $dash->getTypes();
$menus = $dash->getMenus();
$currentUser = $auth->getCurrentUser();

if (!$currentUser['wildfire_dashboard_access']) {
    header('Location: /user/login');
    die();
} else {
    $type = $dash->do_unslugify($_GET['type']);
}
