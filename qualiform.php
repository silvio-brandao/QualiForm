<?php
/**
 * Plugin Name: QualiForm
 * Description: An integrated form.
 * Version: 1.4
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

function udf_register_shortcode() {
    ob_start(); ?>
    <form id="multiStepForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="udf_handle_upload">

        <!-- Step 1 -->
        <div id="step1">
            <h3>Identificação do reclamante</h3>
            
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

            <button type="button" onclick="nextStep()">Próximo</button>
        </div>

        <!-- Step 2 -->
        <div id="step2" style="display:none;">
            <label for="categoria">Categoria do Problema</label>
            <select id="categoria" name="categoria" required onchange="toggleOutroCampo()">
                <option value="">Selecione uma categoria</option>
                <option value="Problemas no rótulo: adulteração, inelegível, inadequado">Problemas no rótulo: adulteração, inelegível, inadequado</option>
                <option value="Presença de corpo estranho na embalagem">Presença de corpo estranho na embalagem</option>
                <option value="Rachadura, quebra do produto ou parte dele">Rachadura, quebra do produto ou parte dele</option>
                <option value="Aspecto alterado: cor, manchas, cheiro">Aspecto alterado: cor, manchas, cheiro</option>
                <option value="Quantidade de unidades menor que o informado na embalagem">Quantidade de unidades menor que o informado na embalagem</option>
                <option value="Produto com data de validade vencida">Produto com data de validade vencida</option>
                <option value="Produto de uso único sendo reprocessado">Produto de uso único sendo reprocessado</option>
                <option value="Fornece dados incorretos">Fornece dados incorretos</option>
                <option value="Travamento">Travamento</option>
                <option value="Superaquecimento">Superaquecimento</option>
                <option value="Não desempenha a função requerida">Não desempenha a função requerida</option>
                <option value="Quantidade de unidades menor que a informada na embalagem">Quantidade de unidades menor que a informada na embalagem</option>
                <option value="Alterações visíveis">Alterações visíveis</option>
                <option value="Alterações no rótulo, embalagem ou bula">Alterações no rótulo, embalagem ou bula</option>
                <option value="Alterações de registro">Alterações de registro</option>
                <option value="Cor alterada">Cor alterada</option>
                <option value="Embalagem defeituosa">Embalagem defeituosa</option>
                <option value="Presença de partículas / corpos estranhos">Presença de partículas / corpos estranhos</option>
                <option value="Outros">Outros</option>
            </select><br><br>

            <div id="outroCampoDiv" style="display:none;">
                <label for="categoria_outros">Descreva o problema:</label>
                <input type="text" id="categoria_outros" name="categoria_outros"><br><br>
            </div>

            <button type="button" onclick="prevStep()">Voltar</button>
            <button type="button" onclick="nextStep3()">Próximo</button>
        </div>

        <!-- Step 3 -->
        <div id="step3" style="display:none;">
            <h3>Detalhes Técnicos</h3>
            <label for="referencia">Referência <span title="Formato: XXX.XXX" style="cursor:help;">&#9432;</span></label>
            <input type="text" id="referencia" name="referencia" required pattern="\d{3}\.\d{3}" placeholder="Ex: 105.070" onblur="preencherDescricaoProduto()"><br><br>

            <label for="descricao_produto">Descrição do Produto</label>
            <input type="text" id="descricao_produto" name="descricao_produto" readonly required><br><br>

            <label for="lote">Lote <span title="Símbolo de lote" style="cursor:help;">&#9432;</span></label>
            <input type="text" id="lote" name="lote" required><br><br>

            <!-- Seções dinâmicas por família -->
            <div id="secao_implante" style="display:none;">
                <h4>Relato Técnico | Implante Dentário</h4>
                <label for="relato_implante">Relato:</label>
                <textarea id="relato_implante" name="relato_implante"></textarea><br><br>
            </div>
            <div id="secao_componente" style="display:none;">
                <h4>Relato Técnico | Componente Protético</h4>
                <label for="relato_componente">Relato:</label>
                <textarea id="relato_componente" name="relato_componente"></textarea><br><br>
            </div>
            <div id="secao_instrumental" style="display:none;">
                <h4>Relato Técnico | Instrumental Cirúrgico</h4>
                <label for="relato_instrumental">Relato:</label>
                <textarea id="relato_instrumental" name="relato_instrumental"></textarea><br><br>
            </div>

            <h4>Condições de Uso e Armazenamento</h4>
            <label>O produto foi utilizado em um paciente? <span style="color:red">*</span></label><br>
            <input type="radio" id="utilizado_sim" name="utilizado_paciente" value="Sim" required onchange="togglePacienteInfo()">
            <label for="utilizado_sim">Sim</label>
            <input type="radio" id="utilizado_nao" name="utilizado_paciente" value="Não" required onchange="togglePacienteInfo()">
            <label for="utilizado_nao">Não</label>
            <br><br>

            <div id="pacienteInfo" style="display:none; border:1px solid #eee; padding:10px; margin-bottom:10px;">
                <h4>Informações do Paciente</h4>
                <label for="nome_paciente">Nome do Paciente</label>
                <input type="text" id="nome_paciente" name="nome_paciente"><br>
                <label for="idade_paciente">Idade</label>
                <input type="number" id="idade_paciente" name="idade_paciente" min="0"><br>
                <label>Registro médico: <span style="color:red">*</span></label><br>
                <input type="checkbox" name="registro_medico[]" value="Diabetes mellitus" required> Diabetes mellitus<br>
                <input type="checkbox" name="registro_medico[]" value="Condições endócrinas"> Condições endócrinas<br>
                <input type="checkbox" name="registro_medico[]" value="Resistência imunológica"> Resistência imunológica<br>
                <input type="checkbox" name="registro_medico[]" value="Uso crônico de medicamentos"> Uso crônico de medicamentos<br>
                <input type="checkbox" name="registro_medico[]" value="Uso de corticoide"> Uso de corticoide<br>
                <input type="checkbox" name="registro_medico[]" value="Quimioterapia"> Quimioterapia<br>
                <input type="checkbox" name="registro_medico[]" value="Radioterapia na área da cabeça"> Radioterapia na área da cabeça<br>
                <input type="checkbox" name="registro_medico[]" value="Infecção local"> Infecção local<br>
                <input type="checkbox" name="registro_medico[]" value="Alteração linfática"> Alteração linfática<br>
                <input type="checkbox" name="registro_medico[]" value="Distúrbio circulatório"> Distúrbio circulatório<br>
                <input type="checkbox" name="registro_medico[]" value="Xerostomia"> Xerostomia<br>
                <input type="checkbox" name="registro_medico[]" value="Distúrbios psicológicos"> Distúrbios psicológicos<br>
                <input type="checkbox" name="registro_medico[]" value="Consumo excessivo de drogas"> Consumo excessivo de drogas<br>
                <input type="checkbox" name="registro_medico[]" value="Consumo excessivo de álcool"> Consumo excessivo de álcool<br>
                <input type="checkbox" name="registro_medico[]" value="Deficiência nutricional"> Deficiência nutricional<br>
                <input type="checkbox" name="registro_medico[]" value="Sem diagnóstico relevante"> Sem diagnóstico relevante<br>
                <input type="checkbox" id="registro_medico_outros_check" name="registro_medico[]" value="Outros"> Outros<br>
                <div id="registro_medico_outros_div" style="display:none;">
                    <label for="registro_medico_outros">Descreva:</label>
                    <input type="text" id="registro_medico_outros" name="registro_medico_outros"><br>
                </div>
                <br>

                <label>Gênero: <span style="color:red">*</span></label><br>
                <input type="radio" name="genero_paciente" value="Masculino" required> Masculino
                <input type="radio" name="genero_paciente" value="Feminino" required> Feminino
                <input type="radio" name="genero_paciente" value="Transgênero" required> Transgênero
                <input type="radio" name="genero_paciente" value="Intersexo" required> Intersexo
                <input type="radio" name="genero_paciente" value="Não reportado" required> Não reportado
                <br><br>

                <label>Paciente fumante: <span style="color:red">*</span></label><br>
                <input type="radio" name="fumante_paciente" value="Sim" required> Sim
                <input type="radio" name="fumante_paciente" value="Não" required> Não
                <br><br>

                <label for="alergias">Alergias:</label>
                <input type="text" id="alergias" name="alergias" placeholder="Descreva ou marque 'Não aplicável'">
                <input type="checkbox" id="alergias_na" name="alergias_na" value="Não aplicável" onchange="toggleAlergiasInput()"> Não aplicável
                <br><br>

                <label for="outras_doencas">Outras doenças locais ou sistêmicas relevantes:</label>
                <input type="text" id="outras_doencas" name="outras_doencas" placeholder="Descreva ou marque 'Não aplicável'">
                <input type="checkbox" id="outras_doencas_na" name="outras_doencas_na" value="Não aplicável" onchange="toggleDoencasInput()"> Não aplicável
                <br>
            </div>

            <label>Como o produto foi armazenado? <span style="color:red">*</span></label><br>
            <select id="armazenamento" name="armazenamento" required>
                <option value="">Selecione uma opção</option>
                <option value="Em ambiente limpo, seco e com temperatura controlada (entre 15 °C e 30 °C)">Em ambiente limpo, seco e com temperatura controlada (entre 15 °C e 30 °C)</option>
                <option value="Em ambiente refrigerado (abaixo de 10 °C)">Em ambiente refrigerado (abaixo de 10 °C)</option>
                <option value="Em ambiente limpo, porém sem controle ativo de temperatura">Em ambiente limpo, porém sem controle ativo de temperatura</option>
                <option value="Em ambiente com variações significativas de temperatura ao longo do dia (ex: locais sem climatização expostos ao sol)">Em ambiente com variações significativas de temperatura ao longo do dia (ex: locais sem climatização expostos ao sol)</option>
                <option value="Em local exposto à umidade ou proximidade de lavadoras/pias">Em local exposto à umidade ou proximidade de lavadoras/pias</option>
                <option value="Em local exposto a calor excessivo (acima de 40 °C)">Em local exposto a calor excessivo (acima de 40 °C)</option>
                <option value="Em local provisório, sem controle ambiental adequado">Em local provisório, sem controle ambiental adequado</option>
                <option value="Outros">Outros</option>
            </select><br><br>
            <div id="armazenamento_outros_div" style="display:none;">
                <label for="armazenamento_outros">Descreva:</label>
                <input type="text" id="armazenamento_outros" name="armazenamento_outros"><br><br>
            </div>

            <label>Após o recebimento, o produto foi exposto a alguma das situações abaixo? <span style="color:red">*</span></label><br>
            <input type="checkbox" name="exposicao[]" value="Proximidade ou contato com bisturi elétrico" required> Proximidade ou contato com bisturi elétrico<br>
            <input type="checkbox" name="exposicao[]" value="Contato com agentes químicos (ex: irrigadores, desinfetantes, hemostáticos)"> Contato com agentes químicos (ex: irrigadores, desinfetantes, hemostáticos)<br>
            <input type="checkbox" name="exposicao[]" value="Exposição direta a calor intenso ou chama (ex: cautério, termocautério)"> Exposição direta a calor intenso ou chama (ex: cautério, termocautério)<br>
            <input type="checkbox" name="exposicao[]" value="Presença em ambientes cirúrgicos externos ao consultório (ex: hospital, centro cirúrgico, campanhas e mutirões)"> Presença em ambientes cirúrgicos externos ao consultório (ex: hospital, centro cirúrgico, campanhas e mutirões)<br>
            <input type="checkbox" name="exposicao[]" value="Queda ou impacto acidental durante manuseio ou armazenamento local"> Queda ou impacto acidental durante manuseio ou armazenamento local<br>
            <input type="checkbox" name="exposicao[]" value="Nenhuma das situações mencionadas"> Nenhuma das situações mencionadas<br>
            <input type="checkbox" name="exposicao[]" value="Outros" id="exposicao_outros_check"> Outros<br>
            <div id="exposicao_outros_div" style="display:none;">
                <label for="exposicao_outros">Descreva:</label>
                <input type="text" id="exposicao_outros" name="exposicao_outros"><br><br>
            </div>

            <label>Arquivo:</label><br>
            <input type="file" name="file" required><br><br>

            <button type="button" onclick="prevStep3()">Voltar</button>
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
    function nextStep3() {
        document.getElementById('step2').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
    }
    function prevStep3() {
        document.getElementById('step3').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
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

    function toggleOutroCampo() {
        var categoria = document.getElementById('categoria').value;
        var outroDiv = document.getElementById('outroCampoDiv');
        if (categoria === 'Outros') {
            outroDiv.style.display = 'block';
            document.getElementById('categoria_outros').required = true;
        } else {
            outroDiv.style.display = 'none';
            document.getElementById('categoria_outros').required = false;
        }
    }

    // Simulação de banco de dados de produtos/famílias
    const produtos = {
        "105.070": { descricao: "Implante Dentário - Modelo X", familia: "Implante Dentário" },
        "200.123": { descricao: "Componente Protético - Abutment", familia: "Componente Protético" },
        "300.456": { descricao: "Instrumental Cirúrgico - Broca", familia: "Instrumental Cirúrgico" }
    };

    function preencherDescricaoProduto() {
        var ref = document.getElementById('referencia').value.trim();
        var descField = document.getElementById('descricao_produto');
        var secaoImplante = document.getElementById('secao_implante');
        var secaoComponente = document.getElementById('secao_componente');
        var secaoInstrumental = document.getElementById('secao_instrumental');
        descField.value = '';
        secaoImplante.style.display = 'none';
        secaoComponente.style.display = 'none';
        secaoInstrumental.style.display = 'none';

        if (produtos[ref]) {
            descField.value = produtos[ref].descricao;
            if (produtos[ref].familia === "Implante Dentário") secaoImplante.style.display = 'block';
            if (produtos[ref].familia === "Componente Protético") secaoComponente.style.display = 'block';
            if (produtos[ref].familia === "Instrumental Cirúrgico") secaoInstrumental.style.display = 'block';
        }
    }

    // Exibe informações do paciente se selecionado "Sim"
    function togglePacienteInfo() {
        var sim = document.getElementById('utilizado_sim').checked;
        var pacienteDiv = document.getElementById('pacienteInfo');
        if (sim) {
            pacienteDiv.style.display = 'block';
            document.getElementById('nome_paciente').required = true;
            document.getElementById('idade_paciente').required = true;
            document.getElementById('sexo_paciente').required = true;
        } else {
            pacienteDiv.style.display = 'none';
            document.getElementById('nome_paciente').required = false;
            document.getElementById('idade_paciente').required = false;
            document.getElementById('sexo_paciente').required = false;
        }
    }

    // Exibe campo "Outros" para armazenamento
    document.getElementById('armazenamento').addEventListener('change', function() {
        var outrosDiv = document.getElementById('armazenamento_outros_div');
        if (this.value === 'Outros') {
            outrosDiv.style.display = 'block';
            document.getElementById('armazenamento_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('armazenamento_outros').required = false;
        }
    });

    // Exibe campo "Outros" para exposição
    document.getElementById('exposicao_outros_check').addEventListener('change', function() {
        var outrosDiv = document.getElementById('exposicao_outros_div');
        if (this.checked) {
            outrosDiv.style.display = 'block';
            document.getElementById('exposicao_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('exposicao_outros').required = false;
        }
    });

    // Exibe campo "Outros" para registro médico
    document.getElementById('registro_medico_outros_check').addEventListener('change', function() {
        var outrosDiv = document.getElementById('registro_medico_outros_div');
        if (this.checked) {
            outrosDiv.style.display = 'block';
            document.getElementById('registro_medico_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('registro_medico_outros').required = false;
        }
    });

    // Bloqueia/limpa campo de alergias se "Não aplicável" marcado
    function toggleAlergiasInput() {
        var alergiasInput = document.getElementById('alergias');
        var alergiasNA = document.getElementById('alergias_na');
        if (alergiasNA.checked) {
            alergiasInput.value = '';
            alergiasInput.disabled = true;
        } else {
            alergiasInput.disabled = false;
        }
    }

    // Bloqueia/limpa campo de outras doenças se "Não aplicável" marcado
    function toggleDoencasInput() {
        var doencasInput = document.getElementById('outras_doencas');
        var doencasNA = document.getElementById('outras_doencas_na');
        if (doencasNA.checked) {
            doencasInput.value = '';
            doencasInput.disabled = true;
        } else {
            doencasInput.disabled = false;
        }
    }
    </script>
    <?php return ob_get_clean();
}
add_shortcode('upload_drive_form', 'udf_register_shortcode');

add_action('admin_post_udf_handle_upload', 'udf_handle_upload');
add_action('admin_post_nopriv_udf_handle_upload', 'udf_handle_upload');

require_once plugin_dir_path(__FILE__) . 'drive-upload-handler.php';
