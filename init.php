<?php

if (!(($_SESSION['user_id'] && $_SESSION['wildfire_dashboard_access']) || $userless_install)) {
    header ('Location: /user/login');
    die();
} else {
	$type=$dash->do_unslugify($_GET['type']);
}
