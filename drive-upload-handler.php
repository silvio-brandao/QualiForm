<?php

// --- DEBUG LOGGING SETUP ---
if (!function_exists('qualiform_debug_log')) {
    function qualiform_debug_log($message) {
        $log_file = __DIR__ . '/qualiform_debug.log';
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    }
}

qualiform_debug_log("Script loaded.");

// Tenta carregar o autoloader se existir (Composer standard)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    qualiform_debug_log("Autoloader loaded.");
}

require_once __DIR__ . '/qualiform-secrets.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

function udf_handle_upload() {
    qualiform_debug_log("udf_handle_upload called.");

    try {
        // Recebe os dados do formulário em JSON
        if (!isset($_POST['json_data'])) {
            qualiform_debug_log('json_data não enviado');
            // Não para o processamento
        }
        $json_data = isset($_POST['json_data']) ? wp_unslash($_POST['json_data']) : '';
        $form_data = $json_data ? json_decode($json_data, true) : [];

        if (!$form_data || !isset($form_data['name'])) {
            qualiform_debug_log('JSON inválido ou campo name ausente');
            // Não para o processamento
        }

        if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
            qualiform_debug_log('Arquivo não enviado ou inválido');
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
        
        // 1. Corpo do E-mail (HTML)
        $nomeCliente = isset($form_data['name']) ? $form_data['name'] : 'Cliente';
        
        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                h2 { color: #ff7200; margin-bottom: 20px; }
                p { margin-bottom: 15px; }
                hr { border: 0; border-top: 1px solid #eee; margin: 30px 0; }
                ul { margin-bottom: 20px; padding-left: 20px; }
                li { margin-bottom: 8px; }
                .highlight { color: #ff7200; font-weight: bold; }
            </style>
        </head>
        <body>
            <h2>Olá, ' . esc_html($nomeCliente) . '.</h2>
            <p>Confirmamos o recebimento do seu <strong>Relato Técnico</strong>.</p>
            
            <p>Na Kopp, a qualidade de nossos produtos e a segurança de seus pacientes são prioridades absolutas. Tratamos cada ocorrência relatada com o máximo rigor, seriedade e transparência.</p>
            
            <p>Seu relato já foi registrado em nosso sistema e será encaminhado imediatamente para o nosso setor de <strong>Garantia da Qualidade</strong> para uma análise técnica detalhada. Este processo é fundamental para assegurarmos a excelência contínua de nossas soluções.</p>
            
            <hr>
            <p><strong>Seu Protocolo de Atendimento: <span class="highlight">' . esc_html($protocolo) . '</span></strong></p>
            
            <p>Para prosseguirmos com a análise, por favor verifique os anexos deste e-mail:</p>
            <ul>
                <li><strong>Relatorio_' . esc_html($protocolo) . '.pdf:</strong> O comprovante oficial do seu registro, contendo todos os dados informados.</li>
                <li><strong>Instruções (Imagem):</strong> Um guia visual importante sobre como preparar e enviar a peça física para nossa análise (caso aplicável).</li>
            </ul>
            
            <p>Agradecemos sua colaboração para mantermos nossos altos padrões de qualidade.</p>
            <p style="color: #777; font-size: 0.9em; margin-top: 30px;">Atenciosamente,<br>Equipe de Qualidade Kopp Implantes</p>
        </body>
        </html>';

        $headers = ['Content-Type: text/html; charset=UTF-8']; 

        // 2. Gerar Relatório HTML (Estilo A4)
        
        // Tenta carregar a imagem como Base64 para embutir diretamente no HTML
        // Isso evita problemas de permissão ou path resolution no Dompdf
        $logoPath = plugin_dir_path(__FILE__) . 'img/kopp_logo.png';
        $logoSrc = ''; // Inicialmente vazio ou fallback

        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            if ($logoData !== false) {
                $base64 = base64_encode($logoData);
                // Assume PNG baseado no nome do arquivo
                $logoSrc = 'data:image/png;base64,' . $base64;
                qualiform_debug_log("Logo loaded and encoded successfully. Base64 length: " . strlen($base64));
            } else {
                qualiform_debug_log("Failed to read logo file content: " . $logoPath);
            }
        } else {
            qualiform_debug_log("Logo file not found at: " . $logoPath);
            // Tenta URL pública como último recurso, se remote enabled funcionar
            $logoSrc = 'https://koppimplantes.com/wp-content/uploads/2021/06/logo-kopp.png';
        }

        $dataEnvio = date('d/m/Y H:i');
        
        $htmlReport = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Relatório Técnico - ' . $protocolo . '</title>
            <style>
                @page { margin: 2cm; }
                body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; color: #333; }
                .header { text-align: center; border-bottom: 2px solid #ff7200; padding-bottom: 20px; margin-bottom: 30px; }
                .header img { max-height: 70px; margin-bottom: 15px; }
                .header h1 { margin: 0; color: #ff7200; font-size: 24px; text-transform: uppercase; }
                .meta { text-align: right; color: #777; margin-bottom: 30px; font-size: 11px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
                .section { margin-bottom: 15px; }
                .info-row { margin-bottom: 8px; border-bottom: 1px dotted #eee; padding-bottom: 4px; }
                .label { font-weight: bold; color: #555; display: inline-block; width: 35%; vertical-align: top; }
                .value { display: inline-block; width: 60%; color: #000; font-weight: 500; }
                .footer { margin-top: 50px; border-top: 1px solid #ccc; padding-top: 15px; text-align: center; font-size: 10px; color: #999; position: fixed; bottom: 0; left: 0; right: 0; }
            </style>
        </head>
        <body>
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
        </body>
        </html>';

        // 3. Gerar PDF e Salvar arquivo temporário
        $upload_dir = wp_upload_dir(); // Usa diretório de upload do WP para garantir permissões
        $temp_dir = $upload_dir['basedir'] . '/qualiform_temp';
        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        $reportFilename = 'Relatorio_' . $protocolo . '.pdf';
        $reportPath = $temp_dir . '/' . $reportFilename;
        
        // Geração PDF (Modern Dompdf 2.x/3.x)
        try {
            qualiform_debug_log("Starting PDF generation (Modern Dompdf)...");
            
            if (class_exists('Dompdf\Dompdf')) {
                $options = new Options();
                $options->set('isRemoteEnabled', true);
                
                $dompdf = new Dompdf($options);
                $dompdf->loadHtml($htmlReport);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                file_put_contents($reportPath, $dompdf->output());
                qualiform_debug_log("PDF generated successfully.");
            } else {
                throw new Exception("Classe Dompdf\Dompdf não encontrada. Verifique a instalação do plugin.");
            }
            
        } catch (Exception $e) {
            qualiform_debug_log('Erro ao gerar PDF: ' . $e->getMessage());
            // Fallback para HTML
            $reportFilename = 'Relatorio_' . $protocolo . '.html';
            $reportPath = $temp_dir . '/' . $reportFilename;
            file_put_contents($reportPath, $htmlReport);
            
            $body = str_replace('.pdf', '.html', $body);
            $body = str_replace('(formato PDF)', '(formato HTML)', $body);
        }

        // 4. Definir anexos
        $attachments = [];
        
        // Anexo 1: Relatório PDF (ou HTML fallback)
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
            qualiform_debug_log('WP Mail: E-mail enviado com sucesso para a fila.');
        } else {
            qualiform_debug_log('WP Mail: FALHA AO ENVIAR O E-MAIL.');
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
                qualiform_debug_log('Token do Google não encontrado');
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
            qualiform_debug_log('Erro no upload do Google Drive: ' . $e->getMessage());
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
            qualiform_debug_log('Erro ao obter token Forlogic: ' . print_r($loginResponse, true));
            wp_send_json_error(['message' => 'Erro ao autenticar na API Qualiex.']);
            exit;
        }

        $loginData = json_decode(wp_remote_retrieve_body($loginResponse), true);
        $accessToken = isset($loginData['access_token']) ? $loginData['access_token'] : null;

        if (!$accessToken) {
            qualiform_debug_log('Token de acesso não retornado pelo login Forlogic: ' . print_r($loginData, true));
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
            qualiform_debug_log('Erro na API Qualiex: ' . print_r($response, true));
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
        qualiform_debug_log('Erro no udf_handle_upload: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Erro interno ao processar o formulário.']);
    }
}
