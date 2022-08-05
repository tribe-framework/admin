<?php
/**
 * This api route handles 3 different uploads:
 * 1. Video (Stream)
 * 2. Image
 * 3. Generic
 *
 * Videos and Images are then handed over to Cloudflare, Generics are kept locally
 * on the server.
 */

use \Wildfire\Api;
use \Wildfire\Core\Dash;

$api = new Api();

switch ($api->method()) {
    case 'post':
        handleUpload($api);
        break;

    default:
        $api->json(['error' => 'method not allowed'])->send(405);
}

function handleUpload(Api $api) {
    $dash = new Dash;

    try {
        if (empty($_FILES)) {
            $api->json(['ok' => false, 'msg' => 'No files uploaded'])->send();
        }

        $response_data = [];

        // ToDo: handle images and video files and upload them to cloudflare
        foreach($_FILES as $file) {
            // Undefined | Multiple Files | $_FILES Corruption Attack
            // If this request falls under any of them, treat it invalid.
            if (
                !isset($file['error']) ||
                is_array($file['error'])
            ) {
                throw new RuntimeException('Invalid Parameters');
            }

            // Check $_FILES['image']['error'] value.
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;

                case UPLOAD_ERR_NO_FILE: throw new RuntimeException('No file sent.');

                case UPLOAD_ERR_INI_SIZE:

                case UPLOAD_ERR_FORM_SIZE: throw new RuntimeException('Exceeded filesize limit.');

                default: throw new RuntimeException('Unknown errors.');
            }

            // check if whitelisting is enabled
            if (defined('UPLOAD_FILE_TYPES')) {
                preg_match(UPLOAD_FILE_TYPES, $file['name'], $ext);

                if (empty($ext)) {
                    throw new RuntimeException('File type not allowed');
                }
            }

            // You should name it uniquely.
            $file_name = time() . "_{$file['name']}";

            $upload_path = $dash->get_uploader_path();
            $file_url = "{$upload_path['upload_url']}/{$file_name}";
            $file_url = str_replace($_ENV['WEB_URL'], '', $file_url); // upload url not tied to domain

            $file_name_with_path = "{$upload_path['upload_dir']}/{$file_name}";

            if (!move_uploaded_file($file['tmp_name'], $file_name_with_path)) {
                throw new RuntimeException('Failed to move uploaded file.');
            }

            $response_data[] = [
                'original_name' => $file['name'],
                'uploaded_as' => $file_name,
                'url' => $file_url
            ];
        }

        $response = ['ok' => true, 'data' => $response_data];

        $api->json($response)->send();
    } catch (RuntimeException $e) {
        $response = [
            'ok' => false,
            'error' => $e->getMessage()
        ];

        $api->json($response)->send();
    }
}
