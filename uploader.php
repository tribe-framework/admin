<?php
include_once 'init.php';

//https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/UploadHandler.php
include_once __DIR__ . '/plugins/blueimp-jquery-file-upload/UploadHandler.php';

$upload_paths = $dash->get_uploader_path();

if (defined('UPLOAD_FILE_TYPES')) {
	$upload_handler = new UploadHandler(array('script_url' => '/admin/uploader', 'upload_dir' => $upload_paths['upload_dir'] . '/', 'upload_url' => $upload_paths['upload_url'] . '/', 'inline_file_types' => UPLOAD_FILE_TYPES, 'accept_file_types' => UPLOAD_FILE_TYPES));
} else {
	$upload_handler = new UploadHandler(array('script_url' => '/admin/uploader', 'upload_dir' => $upload_paths['upload_dir'] . '/', 'upload_url' => $upload_paths['upload_url'] . '/'));
}
