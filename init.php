<?php
namespace Wildfire;

$dash = new Core\Dash();
$admin = new Core\Admin();
$theme = new Core\Theme();
$auth = new Auth\Auth();

$types = $dash->getTypes();
$menus = $dash->getMenus();
$currentUser = $auth->getCurrentUser();

if (!$currentUser['wildfire_dashboard_access'] && $slug!='uploader') {
    header('Location: /user/login');
    die();
} else if(isset($_GET['type'])) {
    $type = $dash->do_unslugify($_GET['type']);
}
