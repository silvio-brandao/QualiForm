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

        $protocolo = isset($form_data['protocolo']) ? sanitize_text_field($form_data['protocolo']) : 'Protocolo-' . date('YmdHis');

        // --- DESCRIÇÃO AMIGÁVEL PARA A API ---
        $friendlyDescription = ""; 

        // Loop em todos os dados do formulário
        foreach ($form_data as $key => $value) {
            
            // Pula campos de controle (como 'action')
            // E pula campos que o usuário deixou em branco (como arrays vazios ou strings vazias)
            if ($key === 'action' || empty($value)) {
                continue;
            }

            // AQUI ESTÁ A LÓGICA CORRETA
            // 1. Verificamos se o valor é um array (ex: [item1, item2])
            if (is_array($value)) {
                // 2. Se for, usamos implode() para juntar TODOS os itens com ", "
                $valueString = implode(', ', $value);
            } else {
                // 3. Se não for, é um valor simples, então só o usamos
                $valueString = $value;
            }
            
            // (Bônus) Isso limpa a chave, trocando "Exposicao[]" por "Exposicao"
            $cleanKey = str_replace('[]', '', $key);

            // 4. Adicionamos a linha formatada com HTML
            $friendlyDescription .= '<strong>' . ucfirst($cleanKey) . ':</strong> ' . $valueString . '<br>';
        }

        $to = $form_data['email'];
        $subject = 'Envio de Reclamação ' . $protocolo;
        
        // 1. Corpo do E-mail (Simples e Instrucional)
        $nomeCliente = isset($form_data['name']) ? $form_data['name'] : 'Cliente';
        
        $body = "Olá, " . $nomeCliente . "!\n\n";
        $body .= "Recebemos o seu relato técnico com sucesso. O número do seu protocolo é: " . $protocolo . "\n\n";
        $body .= "--------------------------------------------------\n";
        $body .= "IMPORTANTE - VEJA OS ANEXOS:\n";
        $body .= "--------------------------------------------------\n\n";
        $body .= "1. ARQUIVO 'Relatorio_" . $protocolo . ".html':\n";
        $body .= "   - Este é o comprovante oficial do seu envio.\n";
        $body .= "   - COMO ABRIR: Clique duas vezes no arquivo para abri-lo em seu navegador de internet (Google Chrome, Edge, Safari, etc).\n";
        $body .= "   - COMO SALVAR: Com o arquivo aberto no navegador, pressione 'Ctrl + P' (ou vá em Imprimir) e escolha a opção 'Salvar como PDF'.\n\n";
        $body .= "2. ARQUIVO 'email.jpg':\n";
        $body .= "   - Contém as instruções visuais de como preparar e enviar o produto físico para análise.\n\n";
        $body .= "Atenciosamente,\nEquipe Kopp Implantes";

        $headers = []; 

        // 2. Gerar Relatório HTML (Estilo A4)
        
        // Converter logo local para Base64 para garantir que apareça offline/sem bloqueios
        $logoPath = plugin_dir_path(__FILE__) . 'img/kopp_logo.png';
        $logoBase64 = '';
        $logoSrc = 'https://koppimplantes.com/wp-content/uploads/2021/06/logo-kopp.png'; // Fallback

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = base64_encode($logoData);
            $logoSrc = 'data:image/png;base64,' . $logoBase64;
        }

        $dataEnvio = date('d/m/Y H:i');
        
        $htmlReport = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Relatório Técnico - ' . $protocolo . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; margin: 0; padding: 20px; background: #f0f0f0; }
                .a4-page { width: 100%; max-width: 21cm; margin: 0 auto; border: 1px solid #ccc; padding: 40px; box-sizing: border-box; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .header { text-align: center; border-bottom: 2px solid #ff7200; padding-bottom: 20px; margin-bottom: 30px; }
                .header img { max-height: 70px; margin-bottom: 15px; }
                .header h1 { margin: 0; color: #ff7200; font-size: 24px; text-transform: uppercase; }
                .meta { text-align: right; color: #777; margin-bottom: 30px; font-size: 11px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
                .section { margin-bottom: 15px; }
                .info-row { margin-bottom: 8px; border-bottom: 1px dotted #eee; padding-bottom: 4px; }
                .label { font-weight: bold; color: #555; display: inline-block; width: 35%; vertical-align: top; }
                .value { display: inline-block; width: 60%; color: #000; font-weight: 500; }
                .footer { margin-top: 50px; border-top: 1px solid #ccc; padding-top: 15px; text-align: center; font-size: 10px; color: #999; }
                .print-hint { text-align: center; background: #fff3cd; color: #856404; padding: 10px; margin-bottom: 20px; border: 1px solid #ffeeba; border-radius: 4px; font-size: 13px; }
                @media print {
                    body { padding: 0; background: #fff; }
                    .a4-page { border: none; width: 100%; max-width: none; padding: 0; box-shadow: none; margin: 0; }
                    .print-hint { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-hint">
                <strong>DICA:</strong> Pressione <code>Ctrl + P</code> (ou Cmd + P no Mac) para Salvar como PDF ou Imprimir este documento.
            </div>
            <div class="a4-page">
                <div class="header">
                    <img src="' . $logoSrc . '" alt="Kopp Implantes">
                    <h1>Relatório de Ocorrência</h1>
                    <div style="font-size: 16px; margin-top: 10px; color: #333;">Protocolo: <strong>' . $protocolo . '</strong></div>
                </div>

                <div class="meta">
                    Data do Envio: ' . $dataEnvio . '<br>
                    Gerado automaticamente pelo sistema QualiForm
                </div>

                <h3 style="color: #333; border-left: 4px solid #ff7200; padding-left: 10px; margin-bottom: 20px;">Dados do Relatório</h3>
                
                ' . $friendlyDescription . '

                <div class="footer">
                    Kopp Implantes - Sistema de Gestão da Qualidade<br>
                    Este documento é um comprovante oficial de envio de relato técnico.
                </div>
            </div>
        </body>
        </html>';

        // 3. Salvar arquivo HTML temporário
        $upload_dir = wp_upload_dir(); // Usa diretório de upload do WP para garantir permissões
        $temp_dir = $upload_dir['basedir'] . '/qualiform_temp';
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        $reportFilename = 'Relatorio_' . $protocolo . '.html';
        $reportPath = $temp_dir . '/' . $reportFilename;
        file_put_contents($reportPath, $htmlReport);

        // 4. Definir anexos
        $attachments = [];
        
        // Anexo 1: Relatório HTML
        if (file_exists($reportPath)) {
            $attachments[] = $reportPath;
        }

        // Anexo 2: Imagem de Instruções (email.jpg na raiz do plugin)
        $instructionImgPath = plugin_dir_path(__FILE__) . 'instruções.jpg';
        if (file_exists($instructionImgPath)) {
            $attachments[] = $instructionImgPath;
        }

        // 5. Enviar E-mail
        // $headers foi esvaziado para texto simples, ou pode ser array()
        $foi_enviado = wp_mail($to, $subject, $body, $headers, $attachments);

        // Logue o resultado
        if ($foi_enviado) {
            error_log('WP Mail: E-mail enviado com sucesso para a fila.');
        } else {
            error_log('WP Mail: FALHA AO ENVIAR O E-MAIL.');
        }

        // 6. Limpeza
        if (file_exists($reportPath)) {
            unlink($reportPath);
        }
        // Não deletamos a imagem de instruções original!

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
                // $protocolo variable is now defined at the top of the function

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
        $categoryId = 'L8yHAAD4';

        $occurrenceData = [
            "companyId"   => (string)$companyId,
            "name"        => "Reclamação - " . $protocolo,
            "description" => $friendlyDescription,
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
