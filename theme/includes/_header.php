<?php
require_once __DIR__ . '/../_init.php';

$app_title = $types['webapp']['headmeta_title'] ?? false;
$html_title = "Wildfire Dashboard ".  ($app_title ? "&raquo; $app_title" : '');

if ($slug === 'list' && isset($_GET['type'])) {
	$html_title = ucwords($types[$_GET['type']]['plural']) . " | $html_title";
}
?>

<!doctype html>
<html lang="<?= $types['webapp']['lang'] ?>">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta
		name="description"
		content="Content management dashboard interface <?= $app_title ? "for $app_title" : '' ?>"
	>

	<title><?= $html_title ?></title>

	<link rel="stylesheet" href="https://use.typekit.net/xkh7dxd.css">
	<link rel="stylesheet" href="/vendor/wildfire/admin/theme/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/vendor/wildfire/admin/theme/assets/css/wildfire.css">
	<link rel="stylesheet" href="/vendor/wildfire/admin/theme/assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/v/bs4/dt-1.13.1/b-2.3.3/b-colvis-2.3.3/b-html5-2.3.3/fh-3.3.1/r-2.4.0/sp-2.1.0/sl-1.5.0/datatables.min.css"/>
    <link rel="stylesheet" href="/vendor/wildfire/admin/theme/assets/plugins/sweetalert2/sweetalert2.min.css">
    <!-- css for typeout -->
    <link rel="stylesheet" type="text/css" href="/vendor/wildfire/admin/theme/assets/plugins/typeout/typeout.css">
	<link rel="stylesheet" href="/vendor/wildfire/admin/theme/assets/css/custom.css">

    <!--    jquery -->
    <script src="/vendor/wildfire/admin/theme/assets/plugins/jquery.min.js"></script>

	<?php
    $vue = $_ENV['ENV'] == 'dev' ?
        "/vendor/wildfire/admin/theme/assets/js/vue/vue.js" :
        '/vendor/wildfire/admin/theme/assets/js/vue/vue.min.js';
    ?>
	<script src="<?= $vue ?>"></script>
</head>

<body>
    <hr class="hr fixed-top" style="margin:0 !important;">

    <?php include_once __DIR__.'/_nav-primary.php'; ?>

    <div class="p-3 container">
