<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/qualiform-secrets.php'; 

function udf_handle_upload() {
    if (!isset($_POST['name']) || !isset($_FILES['file'])) {
        wp_die('Envio inválido.');
    }

    $name = sanitize_text_field($_POST['name']);
    $fileTmp = $_FILES['file']['tmp_name'];
    $nomeOriginal = sanitize_file_name($_FILES['file']['name']);
    $mimeType = mime_content_type($fileTmp);

    $client = new Google_Client();
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri('https://koppimplantes.com/oauth-google.php');
    $client->addScope(Google_Service_Drive::DRIVE);

    $tokenPath = __DIR__ . '/google_token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired() && isset($accessToken['refresh_token'])) {
            $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }
    } else {
        wp_die('Token do Google não encontrado. Faça o fluxo OAuth primeiro.');
    }

    $service = new Google_Service_Drive($client);

    $parentFolderId = QUALIFORM_DRIVE_FOLDER_ID; 

    $protocolo = isset($_POST['protocolo']) ? sanitize_text_field($_POST['protocolo']) : 'Protocolo-' . date('YmdHis');

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

    $companyId  = '1';
    $categoryId = '2';

    $occurrenceData = [
        "companyId"   => (string)$companyId,
        "name"        => isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '',
        "description" => isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '',
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
