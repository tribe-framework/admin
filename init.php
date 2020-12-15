<?php
use Wildfire\Core;
$dash = new Wildfire\Core\Dash();
$session_user = $dash->getSessionUser();
if (!$session_user['wildfire_dashboard_access']) {
	header('Location: /user/login');
	die();
} else {
	$type = $dash->do_unslugify($_GET['type']);
}
