<?php
/**
 * Plugin Name: API integrated form
 * Description: Plugin that sends form data via API and email
 * Version: 1.0
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

// Shortcode: [formulario_api]
function quali_form() {
    ob_start();
    ?>
    <form method="post" action="" id="api_form">
        <input type="text" name="name" placeholder="Seu nome" required><br><br>
        <input type="email" name="email" placeholder="Seu email" required><br><br>
        <button type="submit" name="send_form">Enviar</button>
    </form>

    <?php if (isset($_POST['send_form'])): ?>
        <div style="margin-top: 20px;">
            <?php echo send_api_form(); ?>
        </div>
    <?php endif;

    return ob_get_clean();
}
add_shortcode('qualiform', 'quali_form');

// Envio para a API externa
function send_api_form() {
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);

    $url = 'https://suaapi.com/endpoint'; 
    $body = [
        'name'  => $name,
        'email' => $email,
    ];

    $args = [
        'headers' => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer <token>' 
        ],
        'body' => json_encode($body),
        'timeout' => 15,
    ];

    $response = wp_remote_post($url, $args);

    if (is_wp_error($response)) {
        return '<p style="color:red;">Erro ao enviar: ' . $response->get_error_message() . '</p>';
    } else {
        $status = wp_remote_retrieve_response_code($response);
        if ($status === 200 || $status === 201) {
            return '<p style="color:green;">Formulário enviado com sucesso!</p>';
        } else {
            return '<p style="color:red;">Erro ao enviar. Código: ' . esc_html($status) . '</p>';
        }
    }
}
