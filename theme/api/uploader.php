<?php
require_once __DIR__ . '/../_init.php';
//https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/UploadHandler.php
require_once __DIR__ . '/../assets/plugins/blueimp-jquery-file-upload/UploadHandler.php';

$upload_paths = $dash->get_uploader_path();
$image_versions = [
	'' => array(
		'strip' => true,
		'auto_orient' => true,
	),
	'xl' => array(
		'max_width' => 2100,
		'max_height' => 2100,
	),
	'lg' => array(
		'max_width' => 1400,
		'max_height' => 1400,
	),
	'md' => array(
		'max_width' => 700,
		'max_height' => 700,
	),
	'sm' => array(
		'max_width' => 350,
		'max_height' => 350,
	),
	'xs' => array(
		'max_width' => 100,
		'max_height' => 100,
	),
];

$uploader_param = [
	'script_url' => "/admin/uploader",
	'upload_dir' => "{$upload_paths['upload_dir']}/",
	'upload_url' => "{$upload_paths['upload_url']}/",
	'image_versions' => $image_versions
];

if (defined('UPLOAD_FILE_TYPES')) { // upload whitelisted files only if defined
	$uploader_param['inline_file_types'] = UPLOAD_FILE_TYPES;
	$uploader_param['accept_file_types'] = UPLOAD_FILE_TYPES;
}

$upload_handler = new UploadHandler($uploader_param);
