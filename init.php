<?php
use Wildfire\Core\Admin;
use Wildfire\Core\Dash;

$dash = new Dash();
$admin = new Admin();

$session_user = $dash->getSessionUser();
if (!$session_user['wildfire_dashboard_access']) {
	header('Location: /user/login');
	die();
} else {
	$type = $dash->do_unslugify($_GET['type']);
}
