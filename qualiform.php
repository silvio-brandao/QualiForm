<?php
/**
 * Plugin Name: QualiForm
 * Description: An integrated form.
 * Version: 1.3
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

function udf_register_shortcode() {
    ob_start(); ?>
    <form id="multiStepForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="udf_handle_upload">

        <!-- Step 1 -->
        <div id="step1">
            <label>Nome Completo / Razão Social</label><br>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">CPF/CNPJ</label>
            <input type="text" id="description" name="description" required><br><br>

            <label for="cro">CRO/TPD</label>
            <input type="text" id="cro" name="cro" required><br><br>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">Telefone</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9()\-\s]+"><br><br>

            <button type="button" onclick="nextStep()">Próximo</button>
        </div>

        <!-- Step 2 -->
        <div id="step2" style="display:none;">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" required><br><br>

            <label for="rua">Rua</label>
            <input type="text" id="rua" name="rua" required><br><br>

            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" required><br><br>

            <label for="complemento">Complemento</label>
            <input type="text" id="complemento" name="complemento"><br><br>

            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" required><br><br>

            <label for="municipio">Município</label>
            <input type="text" id="municipio" name="municipio" required><br><br>

            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" required><br><br>

            <label for="pais">País</label>
            <input type="text" id="pais" name="pais" required value="Brasil"><br><br>

            <label>Arquivo:</label><br>
            <input type="file" name="file" required><br><br>

            <button type="button" onclick="prevStep()">Voltar</button>
            <button type="submit">Enviar</button>
        </div>
    </form>
    <script>
    function nextStep() {
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    }
    function prevStep() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step1').style.display = 'block';
    }

    document.getElementById('cep').addEventListener('blur', function() {
        var cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro || '';
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('municipio').value = data.localidade || '';
                        document.getElementById('estado').value = data.uf || '';
                        document.getElementById('pais').value = 'Brasil';
                    }
                });
        }
    });
    </script>
    <?php return ob_get_clean();
}
add_shortcode('upload_drive_form', 'udf_register_shortcode');

add_action('admin_post_udf_handle_upload', 'udf_handle_upload');
add_action('admin_post_nopriv_udf_handle_upload', 'udf_handle_upload');

require_once plugin_dir_path(__FILE__) . 'drive-upload-handler.php';
