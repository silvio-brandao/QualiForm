# QUALIEX API Implementation Guide: Occurrence and Attachment

This guide provides instructions on how to implement the QUALIEX API functionality for creating an occurrence and attaching a PDF report to it.

## Prerequisites

Before you begin, you will need the following information from your QUALIEX administrator:

*   **API Subscription Key:** Your `Ocp-Apim-Subscription-Key`.
*   **API User Credentials:** The email and password for the API user.
*   **Company ID (`companyId`):** The ID of your company in QUALIEX.
*   **Category ID (`categoryId`):** The ID of the category for the occurrences you will be creating.
*   **Attachment Module ID (`typeId`):** The module ID for "Occurrences". In this project, it was `3`.

## Implementation Steps

The following steps should be performed in your PHP script that handles the form submission. In this project, the file was `drive-upload-handler.php`.

### Step 1: Store API Credentials

It is recommended to store your API credentials in a separate, non-versioned file for security. In this project, we used a `qualiform-secrets.php` file.

```php
// qualiform-secrets.php
define('QUALIFORM_FORLOGIC_EMAIL', 'your-email@example.com');
define('QUALIFORM_FORLOGIC_PASSWORD', 'your-password');
define('QUALIFORM_FORLOGIC_SUBSCRIPTION_KEY', 'your-subscription-key');
```

### Step 2: Generate the PDF Report

First, generate the PDF report that you want to attach to the occurrence. Make sure the PDF is saved to a temporary location on the server. The path to the saved PDF will be needed in a later step.

In this project, we used `dompdf` to generate the PDF and saved it to a temporary directory.

### Step 3: Create the QUALIEX Occurrence

Make a `POST` request to the QUALIEX API to create the occurrence.

```php
// --- API QUALIEX: Gerar token dinâmico ---
// ... (code to get the $accessToken) ...

// --- API QUALIEX: Enviar ocorrência ---
$companyId  = 'your-company-id';
$categoryId = 'your-category-id';
$protocolo = 'Your-Protocol-Number'; // Or any other name for the occurrence
$friendlyDescription = 'The description of the occurrence.';

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
        'Un-Alias'     => 'kopp', // Your company's alias
        'Authorization' => $accessToken
    ],
    'timeout'     => 15,
    'data_format' => 'body'
];

$response = wp_remote_post($apiUrl, $args);

// Retrieve the occurrence ID from the response
$occurrenceResponseData = json_decode(wp_remote_retrieve_body($response), true);
$occurrenceId = isset($occurrenceResponseData['resultData']['id']) ? $occurrenceResponseData['resultData']['id'] : null;
```

### Step 4: Handle Description Length (4000 Characters Limit)

Before creating the `$occurrenceData` array, check if the length of your description exceeds 4000 characters. If it does, create a simplified description.

```php
$descriptionForQualiex = $friendlyDescription;
if (strlen($friendlyDescription) > 4000) {
    $simplifiedDescription = '<strong>Nome / Razão Social:</strong> ' . $nomeCliente . '<br>';
    // ... (add other important client info) ...
    $simplifiedDescription .= '<br><strong>Relatório excede 4000 caracteres, cheque o anexo da ocorrência</strong>';
    $descriptionForQualiex = $simplifiedDescription;
}

$occurrenceData = [
    "companyId"   => (string)$companyId,
    "name"        => "Reclamação - " . $protocolo,
    "description" => $descriptionForQualiex,
    "categoryId"  => (string)$categoryId
];
```

### Step 5: Attach the PDF to the Occurrence

If the occurrence was created successfully (i.e., you have an `$occurrenceId`) and the PDF file exists, make another `POST` request to upload the PDF.

```php
if ($occurrenceId && file_exists($reportPath)) {
    $reportFilename = basename($reportPath);
    $typeId = 3; // The module ID for "Occurrences"

    $uploadUrl = "https://api.forlogic.net/documents/v1/ApiProduct/Upload/{$typeId}/{$occurrenceId}/{$reportFilename}";

    $fileContent = file_get_contents($reportPath);

    $uploadArgs = [
        'body'    => $fileContent,
        'headers' => [
            'Content-Type' => 'application/pdf',
            'Ocp-Apim-Subscription-Key' => QUALIFORM_FORLOGIC_SUBSCRIPTION_KEY,
            'Un-Alias'     => 'kopp', // Your company's alias
            'Authorization' => $accessToken
        ],
        'timeout'     => 30,
    ];

    $uploadResponse = wp_remote_post($uploadUrl, $uploadArgs);
}
```

### Step 6: File Cleanup

It is important to delete the temporary PDF file from your server after it has been attached to the email and uploaded to QUALIEX. Make sure to call `unlink()` at the end of your script.

```php
// At the end of the script, after all operations using the PDF are done
if (file_exists($reportPath)) {
    unlink($reportPath);
}
```

### Step 7: Logging for Debugging

Use a logging function to record the requests and responses to and from the QUALIEX API. This is extremely helpful for debugging.

```php
function qualiform_debug_log($message) {
    $log_file = __DIR__ . '/qualiform_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Example usage:
qualiform_debug_log('QUALIEX Occurrence Creation Response: ' . wp_remote_retrieve_body($response));
qualiform_debug_log('QUALIEX PDF Upload Response: ' . wp_remote_retrieve_body($uploadResponse));
```

By following these steps, you can replicate the QUALIEX API integration in other plugins.
