<?php
/**
 * DO NOT MANUALLY MODIFY THIS FILE
 * THIS FILE EXISTS FOR BACKWARD COMPATIBILITY REASONS ONLY, TRY TO USE ".env"
 *
 * add custom variables to ".env" in project root or create a "vars.php"
 * in this directory if you want to add your own variables
 */

date_default_timezone_set($_ENV['DEFAULT_TIMEZONE']);

if (file_exists(__DIR__ . '/vars.php')) {
	include_once __DIR__ . '/vars.php';
}

if (!defined('VAR_NAME')) {
	define(
		'UPLOAD_FILE_TYPES',
		'/\.(zip|svg|png|jpe?g|gif|pdf|doc|docx|xls|xlsx|csv|mov|mp4|vtt|srt)$/i'
	);
}

// set cors headers in PHP server
if ($_ENV['ALLOW_CROSS_ORIGIN'] === 'true') {

	//in dev environment, allowing cross origin * for localhost
	if ($_ENV['ENV'] == 'dev') {
		header("Access-Control-Allow-Origin: *");
	}

	//in prod environment, if cross origin access is required, uncomment the following and modify cross_origin_url
	/*
	if ($_ENV['ENV'] == 'prod') {
		$cross_origin_url = 'domain.tld';
		header("Access-Control-Allow-Origin: $cross_origin_url");
	}
	*/

	unset($cross_origin_url);
}

define('TRIBE_ROOT', dirname(__DIR__, 1));
define('ABSOLUTE_PATH', dirname(__DIR__, 1));
define('ENV', $_ENV['ENV']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_PORT', $_ENV['DB_PORT']);
define('BARE_URL', $_ENV['WEB_BARE_URL']);
define('CONTACT_EMAIL', $_ENV['CONTACT_EMAIL']);
define('WEBSITE_NAME', $_ENV['WEBSITE_NAME']);
define('CONTACT_NAME', $_ENV['CONTACT_NAME']);
define('S3_BKUP_HOST_BASE', ($_ENV['S3_BKUP_HOST_BASE'] ?? ''));
define('S3_BKUP_HOST_BUCKET', ($_ENV['S3_BKUP_HOST_BUCKET'] ?? ''));
define('S3_BKUP_ACCESS_KEY', ($_ENV['S3_BKUP_ACCESS_KEY'] ?? ''));
define('S3_BKUP_SECRET_KEY', ($_ENV['S3_BKUP_SECRET_KEY'] ?? ''));
define('S3_BKUP_FOLDER_NAME', ($_ENV['S3_BKUP_FOLDER_NAME'] ?? ''));
define('BASE_URL', ($_ENV['SSL'] !== 'false' ? 'https' : 'http') . '://' . BARE_URL);
define('THEME_URL', BASE_URL . '/theme');
define('THEME_PATH', ABSOLUTE_PATH . '/theme');
