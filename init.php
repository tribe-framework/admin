<?php
$dash = new Wildfire\Core\Dash();
$admin = new Wildfire\Core\Admin();
$theme = new Wildfire\Core\Theme();

$types = $dash->getTypes();
$menus = $dash->getMenus();
$session_user = $dash->getSessionUser();
if (!$session_user['wildfire_dashboard_access']) {
    header('Location: /user/login');
    die();
} else {
    $type = $dash->do_unslugify($_GET['type']);
}
