<?php
include_once ('../../../tribe.init.php');

//https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/UploadHandler.php
include_once(__DIR__.'/plugins/blueimp-jquery-file-upload/UploadHandler.php');

if (defined('UPLOAD_FILE_TYPES'))
	$upload_handler = new UploadHandler(array('script_url'=>__DIR__.'/uploader.php', 'upload_dir'=>ABSOLUTE_PATH.'/uploads/'.date('Y').'/'.date('m-F').'/'.date('d-D').'/', 'upload_url'=>BASE_URL.'/uploads/'.date('Y').'/'.date('m-F').'/'.date('d-D').'/', 'inline_file_types'=>UPLOAD_FILE_TYPES, 'accept_file_types'=>UPLOAD_FILE_TYPES, 'max_height'=>(defined(UPLOAD_MAX_HEIGHT)?UPLOAD_MAX_HEIGHT:1440), 'max_width'=>(defined(UPLOAD_MAX_WIDTH)?UPLOAD_MAX_WIDTH:1440)));
else
	$upload_handler = new UploadHandler(array('script_url'=>__DIR__.'/uploader.php', 'upload_dir'=>ABSOLUTE_PATH.'/uploads/'.date('Y').'/'.date('m-F').'/'.date('d-D').'/', 'upload_url'=>BASE_URL.'/uploads/'.date('Y').'/'.date('m-F').'/'.date('d-D').'/', 'max_height'=>(defined(UPLOAD_MAX_HEIGHT)?UPLOAD_MAX_HEIGHT:1440), 'max_width'=>(defined(UPLOAD_MAX_WIDTH)?UPLOAD_MAX_WIDTH:1440)));
