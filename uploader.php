<?php
require_once __DIR__ . '/init.php';
//https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/UploadHandler.php
include_once __DIR__ . '/plugins/blueimp-jquery-file-upload/UploadHandler.php';

$upload_paths = $dash->get_uploader_path();

if (defined('UPLOAD_FILE_TYPES')) { // upload whitelisted files only if defined
	$upload_handler = new UploadHandler([
		'script_url' => '/admin/uploader',
		'upload_dir' => $upload_paths['upload_dir'] . '/',
		'upload_url' => $upload_paths['upload_url'] . '/',
		'inline_file_types' => UPLOAD_FILE_TYPES,
		'accept_file_types' => UPLOAD_FILE_TYPES,
		'image_versions' => array(
			'' => array(
				'auto_orient' => true,
			),
			'xl' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 2100,
				'max_height' => 2100,
			),
			'lg' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 1400,
				'max_height' => 1400,
			),
			'md' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 700,
				'max_height' => 700,
			),
			'sm' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 350,
				'max_height' => 350,
			),
			'xs' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 100,
				'max_height' => 100,
			),
		),
	]);
} else {
	$upload_handler = new UploadHandler([
		'script_url' => '/admin/uploader',
		'upload_dir' => $upload_paths['upload_dir'] . '/',
		'upload_url' => $upload_paths['upload_url'] . '/',
		'image_versions' => array(
			'' => array(
				'auto_orient' => true,
			),
			'xl' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 2100,
				'max_height' => 2100,
			),
			'lg' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 1400,
				'max_height' => 1400,
			),
			'md' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 700,
				'max_height' => 700,
			),
			'sm' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 350,
				'max_height' => 350,
			),
			'xs' => array(
				'strip' => true,
				'auto_orient' => true,
				'max_width' => 100,
				'max_height' => 100,
			),
		),
	]);
}
