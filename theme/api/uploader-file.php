<?php
/**
 * This API is intended to cater to Editor.JS' image uploader
 * @var object $dash
 */

require_once __DIR__ . '/../_init.php';

use \Wildfire\Api;

$api = new Api;

if ($api->method('post')) {
    try {
         // if $_FILES is empty
        if (empty($_FILES['image'])) {
            throw new RuntimeException('No file uploaded');
        }

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['image']['error']) ||
            is_array($_FILES['image']['error'])
        ) {
            throw new RuntimeException('Invalid Parameters');
        }

        // Check $_FILES['image']['error'] value.
        switch ($_FILES['image']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }

        if (defined('UPLOAD_FILE_TYPES')) {
            preg_match(UPLOAD_FILE_TYPES, $_FILES['image']['name'], $ext);

            if (empty($ext)) {
                throw new RuntimeException('File type not allowed');
            }
        }

        // You should name it uniquely.
        $file_name = time() . "_{$_FILES['image']['name']}";

        $upload_path = $dash->get_uploader_path();
        $file_url = "{$upload_path['upload_url']}/{$file_name}";
        $file_url = str_replace($_ENV['WEB_URL'], '', $file_url); // upload url not tied to domain

        $file_name = "{$upload_path['upload_dir']}/{$file_name}";

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $file_name)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        $response = [
            'success' => 1,
            'file' => [
                'url' => $file_url
            ]
        ];

        $api->json($response)->send();
    } catch (RuntimeException $e) {
        $response = [
            'success' => 0,
            'error' => $e->getMessage()
        ];

        $api->json($response)->send();
    }
}
