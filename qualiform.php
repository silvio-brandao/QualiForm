<?php
/**
 * Plugin Name: QualiForm
 * Description: An integrated form.
 * Version: 1.2
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

// Shortcode para exibir o formulário
function udf_register_shortcode() {
    ob_start(); ?>
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="udf_handle_upload">

        <label>Protocolo:</label><br>
        <input type="text" name="protocolo" required><br><br>

        <label>Arquivo:</label><br>
        <input type="file" name="arquivo" required><br><br>

        <button type="submit">Enviar</button>
    </form>
    <?php return ob_get_clean();
}
add_shortcode('upload_drive_form', 'udf_register_shortcode');

// Lida com o envio
add_action('admin_post_udf_handle_upload', 'udf_handle_upload');
add_action('admin_post_nopriv_udf_handle_upload', 'udf_handle_upload');

// Handler separado
require_once plugin_dir_path(__FILE__) . 'drive-upload-handler.php';
