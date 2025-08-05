<?php

require_once __DIR__ . '/vendor/autoload.php';

function udf_handle_upload() {
    if (!isset($_POST['name']) || !isset($_FILES['file'])) {
        wp_die('Envio inválido.');
    }

    $name = sanitize_text_field($_POST['name']);
    $fileTmp = $_FILES['file']['tmp_name'];
    $nomeOriginal = sanitize_file_name($_FILES['file']['name']);
    $mimeType = mime_content_type($fileTmp);

    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/drive-service-account.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $service = new Google_Service_Drive($client);

    $parentFolderId = QUALIFORM_DRIVE_FOLDER_ID; 

    $folderMetadata = new Google_Service_Drive_DriveFile([
        'name' => $protocolo,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentFolderId]
    ]);
    $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
    $folderId = $folder->id;

    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $nomeOriginal,
        'parents' => [$folderId]
    ]);

    $content = file_get_contents($fileTmp);

    $uploadedFile = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart',
        'fields' => 'id, webViewLink'
    ]);

    $companyId  = get_option('qualiform_company_id', 1);
    $categoryId = get_option('qualiform_category_id', 1);

    update_option('qualiform_company_id', $companyId + 1);
    update_option('qualiform_category_id', $categoryId + 1);

    $occurrenceData = [
        "companyId"   => (string)$companyId,
        "name"        => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
        "description" => isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '',
        "categoryId"  => (string)$categoryId
    ];

    $apiUrl = 'http://api.forlogic.net/occurrences/v1/ApiProduct/createOccurrence';

    $accessToken = QUALIFORM_FORLOGIC_TOKEN;

    $args = [
        'body'        => wp_json_encode($occurrenceData),
        'headers'     => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken
        ],
        'timeout'     => 15,
        'data_format' => 'body'
    ];

    $response = wp_remote_post($apiUrl, $args);

    wp_redirect(home_url('?upload=ok&link=' . urlencode($uploadedFile->webViewLink)));
    exit;
}
