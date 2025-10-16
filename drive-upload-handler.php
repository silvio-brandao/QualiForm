<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/qualiform-secrets.php'; 

function udf_handle_upload() {
    try {
        // Recebe os dados do formulário em JSON
        if (!isset($_POST['json_data'])) {
            error_log('json_data não enviado');
            // Não para o processamento
        }
        $json_data = isset($_POST['json_data']) ? wp_unslash($_POST['json_data']) : '';
        $form_data = $json_data ? json_decode($json_data, true) : [];

        if (!$form_data || !isset($form_data['name'])) {
            error_log('JSON inválido ou campo name ausente');
            // Não para o processamento
        }

        if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            error_log('Arquivo não enviado ou inválido');
            // Não para o processamento
        }

        // --- ENVIO POR EMAIL ---
        $to = 'silviobrandao99@gmail.com';
        $subject = 'Novo envio do formulário QualiForm';
        $body = "Novo envio do formulário QualiForm:\n\n";
        foreach ($form_data as $key => $value) {
            if (is_array($value)) {
                $body .= ucfirst($key) . ': ' . implode(', ', $value) . "\n";
            } else {
                $body .= ucfirst($key) . ': ' . $value . "\n";
            }
        }
        wp_mail($to, $subject, $body);

        // --- GOOGLE DRIVE ---
        $uploadedFile = null;
        $driveError = null;
        try {
            $name = isset($form_data['name']) ? sanitize_text_field($form_data['name']) : '';
            $fileTmp = $_FILES['file']['tmp_name'] ?? '';
            $nomeOriginal = isset($_FILES['file']['name']) ? sanitize_file_name($_FILES['file']['name']) : '';
            $mimeType = $fileTmp ? mime_content_type($fileTmp) : '';

            $client = new Google_Client();
            $client->setClientId(GOOGLE_CLIENT_ID);
            $client->setClientSecret(GOOGLE_CLIENT_SECRET);
            $client->setAccessType('offline');
            $client->setPrompt('consent');
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
                error_log('Token do Google não encontrado');
                $driveError = 'Token do Google não encontrado.';
            }

            if (!$driveError && $fileTmp && $nomeOriginal) {
                $service = new Google_Service_Drive($client);

                $parentFolderId = QUALIFORM_DRIVE_FOLDER_ID; 
                $protocolo = isset($form_data['protocolo']) ? sanitize_text_field($form_data['protocolo']) : 'Protocolo-' . date('YmdHis');

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
            }
        } catch (Exception $e) {
            error_log('Erro no upload do Google Drive: ' . $e->getMessage());
            $driveError = $e->getMessage();
        }

        // --- API QUALIEX: Gerar token dinâmico ---
        $loginUrl = 'https://api.forlogic.net/users/login';
        $loginBody = [
            'email' => QUALIFORM_FORLOGIC_EMAIL,
            'password' => QUALIFORM_FORLOGIC_PASSWORD
        ];
        $loginHeaders = [
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => QUALIFORM_FORLOGIC_SUBSCRIPTION_KEY
        ];

        $loginArgs = [
            'body'    => wp_json_encode($loginBody),
            'headers' => $loginHeaders,
            'timeout' => 15,
            'data_format' => 'body'
        ];

        $loginResponse = wp_remote_post($loginUrl, $loginArgs);

        if (is_wp_error($loginResponse) || wp_remote_retrieve_response_code($loginResponse) >= 400) {
            error_log('Erro ao obter token Forlogic: ' . print_r($loginResponse, true));
            wp_send_json_error(['message' => 'Erro ao autenticar na API Qualiex.']);
            exit;
        }

        $loginData = json_decode(wp_remote_retrieve_body($loginResponse), true);
        $accessToken = isset($loginData['access_token']) ? $loginData['access_token'] : null;

        if (!$accessToken) {
            error_log('Token de acesso não retornado pelo login Forlogic: ' . print_r($loginData, true));
            wp_send_json_error(['message' => 'Token de acesso não retornado pela API Qualiex.']);
            exit;
        }

        // --- API QUALIEX: Enviar ocorrência ---
        $companyId  = 'dEtnzDjd';
        $categoryId = 'ugUthKum';

        $occurrenceData = [
            "companyId"   => (string)$companyId,
            "name"        => 'Ocorrencia',
            "description" => 'teste',
            "categoryId"  => (string)$categoryId
        ];

        $apiUrl = 'http://api.forlogic.net/occurrences/v1/ApiProduct/createOccurrence';
        $args = [
            'body'        => wp_json_encode($occurrenceData),
            'headers'     => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => QUALIFORM_FORLOGIC_SUBSCRIPTION_KEY,
                'Un-Alias'     => 'kopp',
                'Authorization' => $accessToken
            ],
            'timeout'     => 15,
            'data_format' => 'body'
        ];

        $response = wp_remote_post($apiUrl, $args);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) >= 400) {
            error_log('Erro na API Qualiex: ' . print_r($response, true));
            wp_send_json_error(['message' => 'Erro ao enviar para a API Qualiex.']);
            exit;
        }

        // Sucesso: retorna JSON (não faz redirect)
        wp_send_json_success([
            'message' => 'Upload realizado com sucesso!',
            'drive_link' => $uploadedFile->webViewLink ?? null,
            'drive_error' => $driveError
        ]);
        exit;
    } catch (Exception $e) {
        error_log('Erro no udf_handle_upload: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Erro interno ao processar o formulário.']);
    }
}
