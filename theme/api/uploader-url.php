<?php
/**
 * This API is intended to handle image URLs uploaded by EditorJS' image
 * @var object $dash
 *
 * @method post
 */

require_once __DIR__ . '/../_init.php';

use \Wildfire\Api;

$api = new Api;

if (!$api->method('post')) {
    $response = [
        'error' => 'Method not supported'
    ];

    $api->json($response)->send(405);
}

$request = $api->body();

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request['url']);

    $tmp_file = '/tmp/' . time();

    $fp = fopen($tmp_file, 'w');

    // ask curl to write contents to a file
    curl_setopt($ch, CURLOPT_FILE, $fp);

    curl_exec($ch);

    // close session and file
    curl_close($ch);
    fclose($fp);

    // get file's mime type
    $type = mime_content_type($tmp_file);

    if (defined('UPLOAD_FILE_TYPES')) {
        $allowed_types = str_replace('/\.(', '', UPLOAD_FILE_TYPES);

        $pattern = "/(?<=\/)({$allowed_types}";

        preg_match($pattern, $type, $match);

        if (!$match) {
            throw new RuntimeException("Filetype {$type} not allowed");
        }
    }

    // fetch file extension from file's mimetype
    preg_match('/(?<=\/).*/i', $type, $ext);

    $file_name = time().".{$ext[0]}";

    $upload_path = $dash->get_uploader_path();
    $file_url = "{$upload_path['upload_url']}/{$file_name}";
    $file_url = str_replace($_ENV['WEB_URL'], '', $file_url); // upload url not tied to domain

    $file_name = "{$upload_path['upload_dir']}/{$file_name}";

    if (!rename($tmp_file, $file_name)) {
        throw new RuntimeException('Failed to move downloaded file');
    }
} catch (RuntimeException $e) {
    $response = [
        'success' => 0,
        'error' => $e->getMessage()
    ];

    $api->json($response)->send();
}

$response = [
    'success' => 1,
    'file' => [
        'url' => $file_url
    ]
];

$api->json($response)->send();
