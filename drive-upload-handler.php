<?php

require_once __DIR__ . '/vendor/autoload.php';

function udf_handle_upload() {
    if (!isset($_POST['protocolo']) || !isset($_FILES['arquivo'])) {
        wp_die('Envio inválido.');
    }

    $protocolo = sanitize_text_field($_POST['protocolo']);
    $arquivoTmp = $_FILES['arquivo']['tmp_name'];
    $nomeOriginal = sanitize_file_name($_FILES['arquivo']['name']);
    $mimeType = mime_content_type($arquivoTmp);

    // Cria o client
    $client = new Google_Client();
    $client->setAuthConfig(__DIR__ . '/drive-service-account.json');
    $client->addScope(Google_Service_Drive::DRIVE);
    $service = new Google_Service_Drive($client);

    // Pasta raiz no Drive 
    $parentFolderId = '1S_3DjzLQFxHHbGBGKNBOCKOMPr2AxM21'; 

    // Cria uma subpasta com nome do protocolo
    $folderMetadata = new Google_Service_Drive_DriveFile([
        'name' => $protocolo,
        'mimeType' => 'application/vnd.google-apps.folder',
        'parents' => [$parentFolderId]
    ]);
    $folder = $service->files->create($folderMetadata, ['fields' => 'id']);
    $folderId = $folder->id;

    // Upload do arquivo para a pasta criada
    $fileMetadata = new Google_Service_Drive_DriveFile([
        'name' => $nomeOriginal,
        'parents' => [$folderId]
    ]);

    $content = file_get_contents($arquivoTmp);

    $uploadedFile = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => $mimeType,
        'uploadType' => 'multipart',
        'fields' => 'id, webViewLink'
    ]);

    wp_redirect(home_url('?upload=ok&link=' . urlencode($uploadedFile->webViewLink)));
    exit;
}
