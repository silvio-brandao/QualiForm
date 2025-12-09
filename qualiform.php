<?php
/**
 * Plugin Name: QualiForm
 * Description: An integrated form.
 * Version: 1.94
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

function qualiform_register_shortcode() {
    ob_start(); ?>

    <style>
        /* --- Configurações Gerais --- */
        :root {
            --cor-primaria: #ff7200; /* You can change this color if you want */
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
            --cor-borda: #ced4da;
            --cor-texto: #333;
            --cor-fundo: #f4f7f6;
        }

        /* Reset Básico para o shortcode */
        .udf-form-wrapper * {
            box-sizing: border-box;
            /* margin: 0; */ /* Disabling this to respect some internal margins */
            padding: 0;
        }

        /* --- Estrutura do Formulário --- */
        .udf-form-wrapper { /* Wrapper para não vazar estilos */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #ffffff;
            max-width: 700px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin: 2rem auto; /* Centraliza o form na página */
            color: var(--cor-texto);
        }
        
        .udf-form-wrapper .form-header {
            background-color: var(--cor-primaria);
            color: white;
            padding: 1.5rem 2rem;
        }

        .udf-form-wrapper .form-header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: white;
            border: none;
            padding: 0;
        }

        .udf-form-wrapper .form-body {
            padding: 2rem;
        }
        
        /* Applied to your H3 and H4 tags */
        .udf-form-wrapper h3,
        .udf-form-wrapper h4 {
            color: var(--cor-primaria);
            border-bottom: 2px solid var(--cor-fundo);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .udf-form-wrapper h4 {
            font-size: 1.3rem;
            margin-top: 1rem;
        }
        .udf-form-wrapper h5 {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }

        /* * CRITICAL: Your original JS handles step visibility with inline styles (style="display:none").
         * The feedback form uses an .active class.
         * I've REMOVED the .form-step and .form-step.active rules from the feedback.php CSS 
         * to let your existing JS logic work 100% UNTOUCHED.
        */

        /* --- Grupos de Campos --- */
        .udf-form-wrapper .form-group {
            margin-bottom: 1.25rem;
        }
        
        /* Styles for radio/checkbox groups */
        .udf-form-wrapper .radio-group label,
        .udf-form-wrapper .checkbox-group label {
            font-weight: normal;
            display: inline-block;
            margin-left: 0.5rem;
            margin-right: 1.5rem;
            cursor: pointer;
            margin-bottom: 0.5rem; /* Spacing for stacked radios/checkboxes */
            vertical-align: top; /* Aligns text to top for multi-line labels */
            max-width: calc(100% - 35px); /* Prevents text from dropping below input */
        }
        
        .udf-form-wrapper .radio-group input,
        .udf-form-wrapper .checkbox-group input {
            width: auto;
            margin-right: 0.25rem;
            vertical-align: top; /* Aligns checkbox to top */
        }

        /* Fix: Ensure the main label (question) in radio/checkbox groups is block and bold */
        .udf-form-wrapper .radio-group > label:first-child,
        .udf-form-wrapper .checkbox-group > label:first-child {
            display: block;
            font-weight: 600;
            margin-left: 0;
            margin-bottom: 0.5rem;
            cursor: default;
        }

        /* --- Labels e Inputs --- */
        .udf-form-wrapper label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .udf-form-wrapper input[type="text"],
        .udf-form-wrapper input[type="email"],
        .udf-form-wrapper input[type="tel"],
        .udf-form-wrapper input[type="date"],
        .udf-form-wrapper input[type="number"],
        .udf-form-wrapper select,
        .udf-form-wrapper textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--cor-borda);
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .udf-form-wrapper input[type="file"] {
            width: 100%;
            font-size: 0.9rem;
        }

        .udf-form-wrapper select {
            height: 2.8rem;
            appearance: none; /* Remove default arrow */
            -webkit-appearance: none; /* For Safari */
            -moz-appearance: none; /* For Firefox */
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13.2-5.4H18.6c-5%200-9.3%201.8-13.2%205.4A17.6%2017.6%200%200%200%200%2082.6c0%204.8%201.8%209.3%205.4%2013.2l128%20128c3.9%203.9%208.4%205.4%2013.2%205.4s9.3-1.8%2013.2-5.4l128-128c3.9%203.9%205.4-8.4%205.4-13.2%200-4.8-1.8-9.3-5.4-13.2z%22%2F%3E%3C%2Fsvg%3E'); /* Custom SVG arrow */
            background-repeat: no-repeat;
            background-position: right 0.75rem center; /* Position the arrow */
            background-size: 0.8rem; /* Size of the arrow */
            padding-right: 0.5rem; /* Make space for the arrow */
        }

        .udf-form-wrapper textarea#descricao_ocorrido::placeholder {
            color: #777;
        }

        .udf-form-wrapper input:focus, 
        .udf-form-wrapper select:focus, 
        .udf-form-wrapper textarea:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(0, 90, 155, 0.15);
        }
        
        .udf-form-wrapper .helper-text {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 1rem;
        }

        /* --- Campos Condicionais --- */
        .udf-form-wrapper .conditional-field {
            /* display: none; */ /* Your JS handles this with inline styles */
            padding-left: 1rem;
            border-left: 3px solid var(--cor-primaria);
            margin-top: 1rem;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- Botões de Navegação --- */
        .udf-form-wrapper .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }

        .udf-form-wrapper button,
        .udf-form-wrapper .success-btn {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
        }
        
        .udf-form-wrapper button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .udf-form-wrapper button.primary-btn,
        .udf-form-wrapper a.primary-btn {
            background-color: var(--cor-primaria);
            color: white;
            text-align: center; /* Ensure text is centered */
        }
        
        .udf-form-wrapper button.primary-btn:not(:disabled):hover,
        .udf-form-wrapper a.primary-btn:hover {
            background-color: #e66000; /* Darker orange */
        }
        
        .udf-form-wrapper button.secondary-btn,
        .udf-form-wrapper a.secondary-btn {
            background-color: #6c757d;
            color: white;
            text-align: center; /* Ensure text is centered */
        }
        
        .udf-form-wrapper button.secondary-btn:not(:disabled):hover,
        .udf-form-wrapper a.secondary-btn:hover {
            background-color: #5a6268;
        }
        
        .udf-form-wrapper button:active {
            transform: translateY(1px);
        }
        
        /* --- Help Tooltip (Custom) --- */
        .udf-tooltip-icon {
            display: inline-block;
            background: var(--cor-primaria);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            font-size: 12px;
            font-weight: bold;
            cursor: help;
            margin-left: 6px;
            position: relative;
            vertical-align: middle;
        }

        .udf-tooltip-content {
            visibility: hidden;
            width: 220px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 8px 12px;
            position: absolute;
            z-index: 10;
            bottom: 135%; /* Position above */
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            font-weight: normal;
            font-size: 0.85rem;
            pointer-events: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            line-height: 1.4;
        }

        .udf-tooltip-content::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        .udf-tooltip-icon:hover .udf-tooltip-content {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-5px);
        }

        /* --- STYLES FROM YOUR ORIGINAL QUALIFORM.PHP --- */
        .dente-checkbox-container {
            position: relative;
            display: inline-block;
            max-width: 700px;
            width: 100%;
        }
        .dente-checkbox-img {
            width: 100%;
            display: block;
        }
        .dente-checkbox {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            opacity: 0;
            z-index: 2;
            cursor: pointer;
        }
        .dente-label {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 35%;
            background: rgb(18 157 255 / 15%);
            border: 2px solid #626a70;
            color: #585858;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s, border 0.2s;
            z-index: 1;
            pointer-events: none;
        }
        .dente-checkbox:checked + .dente-label {
            background: #050436ff;
            color: #fff;
            border-color: #626a70;
        }

        .dente-checkbox-fdi {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            opacity: 0;
            z-index: 2;
            cursor: pointer;
        }
        .dente-label-fdi {
            position: absolute;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 35%;
            background: rgb(88 131 161 / 15%);
            border: 2px solid #626a70;
            color: #585858;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s, border 0.2s;
            z-index: 1;
            pointer-events: none;
        }
        .dente-checkbox-fdi:checked + .dente-label-fdi {
            background: #030303ff;
            color: #fff;
            border-color: #626a70;
        }
    </style>

    <div class="udf-form-wrapper">
        <form id="multiStepForm" action="<?php echo esc_url(admin_url('admin-post.php?action=udf_handle_upload', 'https')); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="udf_handle_upload">
            
            <div class="form-header">
                <h1>Formulário de Ocorrência</h1>
            </div>

            <div class="form-body">
                
                <div id="step1" class="form-step">
                    <h3>Passo 1: Identificação do reclamante</h3>

                    <div class="form-group radio-group">
                        <label>Tipo de Pessoa <span style="color:red">*</span></label>
                        <input type="radio" id="pessoa_fisica" name="tipo_pessoa" value="fisica" required onchange="toggleTipoPessoa()" checked> <label for="pessoa_fisica">Pessoa Física</label>
                        <input type="radio" id="pessoa_juridica" name="tipo_pessoa" value="juridica" required onchange="toggleTipoPessoa()"> <label for="pessoa_juridica">Pessoa Jurídica</label>
                    </div>

                    <div id="pessoaFisicaFields" style="display:none;">
                        <div class="form-group">
                            <label for="name_fisica">Nome Completo</label>
                            <input type="text" id="name_fisica" name="name">
                        </div>

                        <div class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf">
                        </div>
                    </div>

                    <div id="pessoaJuridicaFields" style="display:none;">
                        <div class="form-group">
                            <label for="name_juridica">Razão Social</label>
                            <input type="text" id="name_juridica" name="name">
                        </div>

                        <div class="form-group">
                            <label for="cnpj">CNPJ</label>
                            <input type="text" id="cnpj" name="cnpj">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cro">CRO/TPD</label>
                        <input type="text" id="cro" name="cro" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefone</label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9()\s\-]+">
                    </div>

                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" required>
                    </div>

                    <div class="form-group">
                        <label for="rua">Rua</label>
                        <input type="text" id="rua" name="rua" required>
                    </div>

                    <div class="form-group">
                        <label for="numero">Número</label>
                        <input type="text" id="numero" name="numero" required>
                    </div>

                    <div class="form-group">
                        <label for="complemento">Complemento</label>
                        <input type="text" id="complemento" name="complemento">
                    </div>

                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" required>
                    </div>

                    <div class="form-group">
                        <label for="municipio">Município</label>
                        <input type="text" id="municipio" name="municipio" required>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <input type="text" id="estado" name="estado" required>
                    </div>

                    <div class="form-group">
                        <label for="pais">País</label>
                        <input type="text" id="pais" name="pais" required value="Brasil">
                    </div>

                    <div class="button-group">
                        <span></span> <button type="button" class="primary-btn" onclick="nextStep()">Próximo</button>
                    </div>
                </div>

                <div id="step2" class="form-step" style="display:none;">
                    <h3>Passo 2: Categoria do Problema</h3>
                    
                    <div class="form-group">
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
                        </select>
                    </div>

                    <div id="outroCampoDiv" class="conditional-field" style="display:none;">
                        <div class="form-group">
                            <label for="categoria_outros">Descreva o problema:</label>
                            <input type="text" id="categoria_outros" name="categoria_outros">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descricao_ocorrido">Descrição da Ocorrência</label>
                        <textarea id="descricao_ocorrido" name="descricao_ocorrido" rows="4" placeholder="Descreva o ocorrido com o máximo de detalhes, incluindo quando foi identificado e em que contexto o produto foi utilizado ou armazenado." required></textarea>
                    </div>

                    <div class="button-group">
                        <button type="button" class="secondary-btn" onclick="prevStep()">Voltar</button>
                        <button type="button" class="primary-btn" onclick="nextStep3()">Próximo</button>
                    </div>
                </div>

                <div id="step3" class="form-step" style="display:none;">
                    <h3>Passo 3: Detalhes Técnicos</h3>
                    
                    <div class="form-group">
                        <label for="referencia">Referência <span title="Formato: XXX.XXX" style="cursor:help;">&#9432;</span></label>
                        <input type="text" id="referencia" name="referencia" required pattern="\d{3}\.\d{3}" placeholder="Ex: 105.070" onblur="preencherDescricaoProduto()">
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao_produto">Descrição do Produto</label>
                        <input type="text" id="descricao_produto" name="descricao_produto" readonly required style="background-color: #f4f7f6;">
                    </div>

                    <div class="form-group">
                        <label for="lote">Lote <span title="Símbolo de lote" style="cursor:help;">&#9432;</span></label>
                        <input type="text" id="lote" name="lote" required>
                    </div>

                    <div id="secao_implante" class="conditional-field" style="display:none;">
                        <h4>Relato Técnico | Implante Dentário</h4>
                        
                        <h5>Informações do Paciente</h5>

                        <div class="form-group">
                            <label>Selecione a região em que o implante dentário foi instalado: <span style="color:red">*</span></label>
                            <label style="font-weight: bold;">Universal: <span style="color:darkblue; font-weight:normal;">selecione as caixas azuis</span></label>
                            <label style="font-weight: bold;">FDI: <span style="color:gray; font-weight:normal;">selecione as caixas cinzas</span></label>
                        </div>
                        
                        <div class="form-group">
                            <div class="dente-checkbox-container">
                                <input type="checkbox" class="dente-checkbox" id="dente1" name="regiao_implante[]" value="1" style="left:2.5%;top:0%;">
                                <label class="dente-label" for="dente1" style="left:2.5%;top:0%;">1</label>
                                <input type="checkbox" class="dente-checkbox" id="dente2" name="regiao_implante[]" value="2" style="left:11%;top:0%;">
                                <label class="dente-label" for="dente2" style="left:11%;top:0%;">2</label>
                                <input type="checkbox" class="dente-checkbox" id="dente3" name="regiao_implante[]" value="3" style="left:18.8%;top:0%;">
                                <label class="dente-label" for="dente3" style="left:18.8%;top:0%;">3</label>
                                <input type="checkbox" class="dente-checkbox" id="dente4" name="regiao_implante[]" value="4" style="left:25.8%;top:0%;">
                                <label class="dente-label" for="dente4" style="left:25.8%;top:0%;">4</label>
                                <input type="checkbox" class="dente-checkbox" id="dente5" name="regiao_implante[]" value="5" style="left:31%;top:0%;">
                                <label class="dente-label" for="dente5" style="left:31%;top:0%;">5</label>
                                <input type="checkbox" class="dente-checkbox" id="dente6" name="regiao_implante[]" value="6" style="left:36%;top:0%;">
                                <label class="dente-label" for="dente6" style="left:36%;top:0%;">6</label>
                                <input type="checkbox" class="dente-checkbox" id="dente7" name="regiao_implante[]" value="7" style="left:41%;top:0%;">
                                <label class="dente-label" for="dente7" style="left:41%;top:0%;">7</label>
                                <input type="checkbox" class="dente-checkbox" id="dente8" name="regiao_implante[]" value="8" style="left:46%;top:0%;">
                                <label class="dente-label" for="dente8" style="left:46%;top:0%;">8</label>
                                <input type="checkbox" class="dente-checkbox" id="dente9" name="regiao_implante[]" value="9" style="left:51.5%;top:0%;">
                                <label class="dente-label" for="dente9" style="left:51.5%;top:0%;">9</label>
                                <input type="checkbox" class="dente-checkbox" id="dente10" name="regiao_implante[]" value="10" style="left:56.5%;top:0%;">
                                <label class="dente-label" for="dente10" style="left:56.5%;top:0%;">10</label>
                                <input type="checkbox" class="dente-checkbox" id="dente11" name="regiao_implante[]" value="11" style="left:61.5%;top:0%;">
                                <label class="dente-label" for="dente11" style="left:61.5%;top:0%;">11</label>
                                <input type="checkbox" class="dente-checkbox" id="dente12" name="regiao_implante[]" value="12" style="left:66.5%;top:0%;">
                                <label class="dente-label" for="dente12" style="left:66.5%;top:0%;">12</label>
                                <input type="checkbox" class="dente-checkbox" id="dente13" name="regiao_implante[]" value="13" style="left:72%;top:0%;">
                                <label class="dente-label" for="dente13" style="left:72%;top:0%;">13</label>
                                <input type="checkbox" class="dente-checkbox" id="dente14" name="regiao_implante[]" value="14" style="left:79%;top:0%;">
                                <label class="dente-label" for="dente14" style="left:79%;top:0%;">14</label>
                                <input type="checkbox" class="dente-checkbox" id="dente15" name="regiao_implante[]" value="15" style="left:87%;top:0%;">
                                <label class="dente-label" for="dente15" style="left:87%;top:0%;">15</label>
                                <input type="checkbox" class="dente-checkbox" id="dente16" name="regiao_implante[]" value="16" style="left:95%;top:0%;">
                                <label class="dente-label" for="dente16" style="left:95%;top:0%;">16</label>

                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi18" name="regiao_implante[]" value="fdi18" style="left:2.5%;top:10%;">
                                <label class="dente-label-fdi" for="fdi18" style="left:2.5%;top:10%;">18</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi17" name="regiao_implante[]" value="fdi17" style="left:11%;top:10%;">
                                <label class="dente-label-fdi" for="fdi17" style="left:11%;top:10%;">17</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi16" name="regiao_implante[]" value="fdi16" style="left:18.8%;top:10%;">
                                <label class="dente-label-fdi" for="fdi16" style="left:18.8%;top:10%;">16</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi15" name="regiao_implante[]" value="fdi15" style="left:25.8%;top:10%;">
                                <label class="dente-label-fdi" for="fdi15" style="left:25.8%;top:10%;">15</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi14" name="regiao_implante[]" value="fdi14" style="left:31%;top:10%;">
                                <label class="dente-label-fdi" for="fdi14" style="left:31%;top:10%;">14</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi13" name="regiao_implante[]" value="fdi13" style="left:36%;top:10%;">
                                <label class="dente-label-fdi" for="fdi13" style="left:36%;top:10%;">13</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi12" name="regiao_implante[]" value="fdi12" style="left:41%;top:10%;">
                                <label class="dente-label-fdi" for="fdi12" style="left:41%;top:10%;">12</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi11" name="regiao_implante[]" value="fdi11" style="left:46%;top:10%;">
                                <label class="dente-label-fdi" for="fdi11" style="left:46%;top:10%;">11</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi21" name="regiao_implante[]" value="fdi21" style="left:51.5%;top:10%;">
                                <label class="dente-label-fdi" for="fdi21" style="left:51.5%;top:10%;">21</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi22" name="regiao_implante[]" value="fdi22" style="left:56.5%;top:10%;">
                                <label class="dente-label-fdi" for="fdi22" style="left:56.5%;top:10%;">22</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi23" name="regiao_implante[]" value="fdi23" style="left:61.5%;top:10%;">
                                <label class="dente-label-fdi" for="fdi23" style="left:61.5%;top:10%;">23</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi24" name="regiao_implante[]" value="fdi24" style="left:66.5%;top:10%;">
                                <label class="dente-label-fdi" for="fdi24" style="left:66.5%;top:10%;">24</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi25" name="regiao_implante[]" value="fdi25" style="left:72%;top:10%;">
                                <label class="dente-label-fdi" for="fdi25" style="left:72%;top:10%;">25</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi26" name="regiao_implante[]" value="fdi26" style="left:79%;top:10%;">
                                <label class="dente-label-fdi" for="fdi26" style="left:79%;top:10%;">26</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi27" name="regiao_implante[]" value="fdi27" style="left:87%;top:10%;">
                                <label class="dente-label-fdi" for="fdi27" style="left:87%;top:10%;">27</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi28" name="regiao_implante[]" value="fdi28" style="left:95%;top:10%;">
                                <label class="dente-label-fdi" for="fdi28" style="left:95%;top:10%;">28</label>

                                <img decoding="async" src="https://koppimplantes.com/wp-content/uploads/2025/08/arc.png" alt="Arco Dental" class="dente-checkbox-img" style="margin-top: 2.5rem; margin-bottom: 2.5rem;">

                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi48" name="regiao_implante[]" value="fdi48" style="left:2.5%;top:90%;">
                                <label class="dente-label-fdi" for="fdi48" style="left:2.5%;top:90%;">48</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi47" name="regiao_implante[]" value="fdi47" style="left:11%;top:90%;">
                                <label class="dente-label-fdi" for="fdi47" style="left:11%;top:90%;">47</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi46" name="regiao_implante[]" value="fdi46" style="left:18.8%;top:90%;">
                                <label class="dente-label-fdi" for="fdi46" style="left:18.8%;top:90%;">46</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi45" name="regiao_implante[]" value="fdi45" style="left:25.8%;top:90%;">
                                <label class="dente-label-fdi" for="fdi45" style="left:25.8%;top:90%;">45</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi44" name="regiao_implante[]" value="fdi44" style="left:31%;top:90%;">
                                <label class="dente-label-fdi" for="fdi44" style="left:31%;top:90%;">44</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi43" name="regiao_implante[]" value="fdi43" style="left:36%;top:90%;">
                                <label class="dente-label-fdi" for="fdi43" style="left:36%;top:90%;">43</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi42" name="regiao_implante[]" value="fdi42" style="left:41%;top:90%;">
                                <label class="dente-label-fdi" for="fdi42" style="left:41%;top:90%;">42</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi41" name="regiao_implante[]" value="fdi41" style="left:46%;top:90%;">
                                <label class="dente-label-fdi" for="fdi41" style="left:46%;top:90%;">41</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi31" name="regiao_implante[]" value="fdi31" style="left:51.5%;top:90%;">
                                <label class="dente-label-fdi" for="fdi31" style="left:51.5%;top:90%;">31</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi32" name="regiao_implante[]" value="fdi32" style="left:56.5%;top:90%;">
                                <label class="dente-label-fdi" for="fdi32" style="left:56.5%;top:90%;">32</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi33" name="regiao_implante[]" value="fdi33" style="left:61.5%;top:90%;">
                                <label class="dente-label-fdi" for="fdi33" style="left:61.5%;top:90%;">33</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi34" name="regiao_implante[]" value="fdi34" style="left:66.5%;top:90%;">
                                <label class="dente-label-fdi" for="fdi34" style="left:66.5%;top:90%;">34</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi35" name="regiao_implante[]" value="fdi35" style="left:72%;top:90%;">
                                <label class="dente-label-fdi" for="fdi35" style="left:72%;top:90%;">35</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi36" name="regiao_implante[]" value="fdi36" style="left:79%;top:90%;">
                                <label class="dente-label-fdi" for="fdi36" style="left:79%;top:90%;">36</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi37" name="regiao_implante[]" value="fdi37" style="left:87%;top:90%;">
                                <label class="dente-label-fdi" for="fdi37" style="left:87%;top:90%;">37</label>
                                <input type="checkbox" class="dente-checkbox-fdi" id="fdi38" name="regiao_implante[]" value="fdi38" style="left:95%;top:90%;">
                                <label class="dente-label-fdi" for="fdi38" style="left:95%;top:90%;">38</label>

                                <input type="checkbox" class="dente-checkbox" id="dente17" name="regiao_implante[]" value="17" style="left:95%;top:100%;">
                                <label class="dente-label" for="dente17" style="left:95%;top:100%;">17</label>
                                <input type="checkbox" class="dente-checkbox" id="dente18" name="regiao_implante[]" value="18" style="left:87%;top:100%;">
                                <label class="dente-label" for="dente18" style="left:87%;top:100%;">18</label>
                                <input type="checkbox" class="dente-checkbox" id="dente19" name="regiao_implante[]" value="19" style="left:79%;top:100%;">
                                <label class="dente-label" for="dente19" style="left:79%;top:100%;">19</label>
                                <input type="checkbox" class="dente-checkbox" id="dente20" name="regiao_implante[]" value="20" style="left:72%;top:100%;">
                                <label class="dente-label" for="dente20" style="left:72%;top:100%;">20</label>
                                <input type="checkbox" class="dente-checkbox" id="dente21" name="regiao_implante[]" value="21" style="left:66.5%;top:100%;">
                                <label class="dente-label" for="dente21" style="left:66.5%;top:100%;">21</label>
                                <input type="checkbox" class="dente-checkbox" id="dente22" name="regiao_implante[]" value="22" style="left:61.5%;top:100%;">
                                <label class="dente-label" for="dente22" style="left:61.5%;top:100%;">22</label>
                                <input type="checkbox" class="dente-checkbox" id="dente23" name="regiao_implante[]" value="23" style="left:56.5%;top:100%;">
                                <label class="dente-label" for="dente23" style="left:56.5%;top:100%;">23</label>
                                <input type="checkbox" class="dente-checkbox" id="dente24" name="regiao_implante[]" value="24" style="left:51.5%;top:100%;">
                                <label class="dente-label" for="dente24" style="left:51.5%;top:100%;">24</label>
                                <input type="checkbox" class="dente-checkbox" id="dente25" name="regiao_implante[]" value="25" style="left:46%;top:100%;">
                                <label class="dente-label" for="dente25" style="left:46%;top:100%;">25</label>
                                <input type="checkbox" class="dente-checkbox" id="dente26" name="regiao_implante[]" value="26" style="left:41%;top:100%;">
                                <label class="dente-label" for="dente26" style="left:41%;top:100%;">26</label>
                                <input type="checkbox" class="dente-checkbox" id="dente27" name="regiao_implante[]" value="27" style="left:36%;top:100%;">
                                <label class="dente-label" for="dente27" style="left:36%;top:100%;">27</label>
                                <input type="checkbox" class="dente-checkbox" id="dente28" name="regiao_implante[]" value="28" style="left:31%;top:100%;">
                                <label class="dente-label" for="dente28" style="left:31%;top:100%;">28</label>
                                <input type="checkbox" class="dente-checkbox" id="dente29" name="regiao_implante[]" value="29" style="left:25.8%;top:100%;">
                                <label class="dente-label" for="dente29" style="left:25.8%;top:100%;">29</label>
                                <input type="checkbox" class="dente-checkbox" id="dente30" name="regiao_implante[]" value="30" style="left:18.8%;top:100%;">
                                <label class="dente-label" for="dente30" style="left:18.8%;top:100%;">30</label>
                                <input type="checkbox" class="dente-checkbox" id="dente31" name="regiao_implante[]" value="31" style="left:11%;top:100%;">
                                <label class="dente-label" for="dente31" style="left:11%;top:100%;">31</label>
                                <input type="checkbox" class="dente-checkbox" id="dente32" name="regiao_implante[]" value="32" style="left:2.5%;top:100%;">
                                <label class="dente-label" for="dente32" style="left:2.5%;top:100%;">32</label>
                            </div>
                        </div>

                        <div class="form-group radio-group">
                            <label>Informe a posição do implante: <span style="color:red">*</span></label>
                            <input type="radio" id="pos_implante_ada" name="posicao_implante" value="ADA" required> <label for="pos_implante_ada">ADA</label>
                            <input type="radio" id="pos_implante_fdi" name="posicao_implante" value="FDI" required> <label for="pos_implante_fdi">FDI</label>
                        </div>

                        <h5>Informações do Evento</h5>
                        <div class="form-group">
                            <label for="data_instalacao_implante">Data de instalação do implante: <span style="color:red">*</span></label>
                            <input type="date" id="data_instalacao_implante" name="data_instalacao_implante" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Data da remoção do implante:</label>
                            <input type="date" id="data_remocao_implante" name="data_remocao_implante">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="remocao_na" name="remocao_na" value="Não aplicável" onchange="toggleRemocaoInput()">
                                <label for="remocao_na" style="font-weight: normal; margin-bottom: 0;">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="condicao_higiene">Qual era a condição de higiene peri-implantar no momento da reavaliação clínica do implante? <span style="color:red">*</span></label>
                            <select name="condicao_higiene" id="condicao_higiene" required>
                                <option value="">Selecione</option>
                                <option value="Excelente">Excelente — sem presença de placa visível ou sinais de inflamação</option>
                                <option value="Boa">Boa — higiene adequada, com presença mínima de placa</option>
                                <option value="Regular">Regular — presença visível de placa e/ou sinais leves de inflamação</option>
                                <option value="Ruim">Ruim — acúmulo significativo de placa e inflamação evidente</option>
                                <option value="Nao reavaliado">A reavaliação clínica não foi realizada</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="acompanhamento_clinico">Como foi o acompanhamento clínico do caso após a instalação? <span style="color:red">*</span></label>
                            <select name="acompanhamento_clinico" id="acompanhamento_clinico" required>
                                <option value="">Selecione</option>
                                <option value="Integral">Calendário de consultas foi seguido integralmente</option>
                                <option value="Parcial">Acompanhamento clínico parcial</option>
                                <option value="Nao houve">Não houve acompanhamento clínico</option>
                            </select>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Na sua análise clínica, uma ou mais das situações abaixo podem ter contribuído para o evento relatado? <span style="color:red">*</span></label>
                            <input type="checkbox" id="cb_bruxismo" name="contribuicao_evento[]" value="Bruxismo" required> <label for="cb_bruxismo">Bruxismo</label><br>
                            <input type="checkbox" id="cb_sobrecarga" name="contribuicao_evento[]" value="Sobrecarga biomecânica"> <label for="cb_sobrecarga">Sobrecarga biomecânica</label><br>
                            <input type="checkbox" id="cb_interferencia" name="contribuicao_evento[]" value="Interferência funcional"> <label for="cb_interferencia">Interferência funcional (ex.: pressão da língua ou bochecha)</label><br>
                            <input type="checkbox" id="cb_trauma" name="contribuicao_evento[]" value="Trauma / acidente"> <label for="cb_trauma">Trauma / acidente</label><br>
                            <input type="checkbox" id="cb_superaquecimento" name="contribuicao_evento[]" value="Superaquecimento ósseo"> <label for="cb_superaquecimento">Superaquecimento ósseo</label><br>
                            <input type="checkbox" id="cb_perf_seio" name="contribuicao_evento[]" value="Perfuração de seio"> <label for="cb_perf_seio">Perfuração de seio</label><br>
                            <input type="checkbox" id="cb_comp_nervo" name="contribuicao_evento[]" value="Compressão do nervo"> <label for="cb_comp_nervo">Compressão do nervo</label><br>
                            <input type="checkbox" id="cb_ext_imediata" name="contribuicao_evento[]" value="Extração imediata"> <label for="cb_ext_imediata">Extração imediata</label><br>
                            <input type="checkbox" id="cb_qual_ossea" name="contribuicao_evento[]" value="Qualidade/quantidade óssea insuficiente"> <label for="cb_qual_ossea">Qualidade/quantidade óssea insuficiente</label><br>
                            <input type="checkbox" id="cb_reab_ossea" name="contribuicao_evento[]" value="Reabsorção óssea"> <label for="cb_reab_ossea">Reabsorção óssea</label><br>
                            <input type="checkbox" id="cb_infeccao" name="contribuicao_evento[]" value="Infecção"> <label for="cb_infeccao">Infecção</label><br>
                            <input type="checkbox" id="cb_periimplantite" name="contribuicao_evento[]" value="Peri-implantite"> <label for="cb_periimplantite">Peri-implantite</label><br>
                            <input type="checkbox" id="cb_falha_oss" name="contribuicao_evento[]" value="Falha de osseointegração"> <label for="cb_falha_oss">Falha de osseointegração</label><br>
                            <input type="checkbox" id="cb_estab_sec" name="contribuicao_evento[]" value="Ausência de estabilidade secundária"> <label for="cb_estab_sec">Ausência de estabilidade secundária</label><br>
                            <input type="checkbox" id="cb_frat_impl" name="contribuicao_evento[]" value="Fratura do Implante"> <label for="cb_frat_impl">Fratura do Implante</label><br>
                            <input type="checkbox" id="cb_dente_adj" name="contribuicao_evento[]" value="Dente adjacente recebeu tratamento endodôntico"> <label for="cb_dente_adj">Dente adjacente recebeu tratamento endodôntico</label><br>
                            <input type="checkbox" id="cb_nenhuma_op" name="contribuicao_evento[]" value="Nenhuma das opções contribuíram para o evento"> <label for="cb_nenhuma_op">Nenhuma das opções contribuíram para o evento</label><br>
                            <input type="checkbox" id="contribuicao_evento_outros_check" name="contribuicao_evento[]" value="Outros"> <label for="contribuicao_evento_outros_check">Outros</label><br>
                            <div id="contribuicao_evento_outros_div" class="conditional-field" style="display:none; border: none; padding-left: 0; margin-top: 0.5rem;">
                                <label for="contribuicao_evento_outros">Descreva:</label>
                                <input type="text" id="contribuicao_evento_outros" name="contribuicao_evento_outros">
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Caso tenha ocorrido perda do implante, ela foi acompanhada por algum dos seguintes sinais ou sintomas? <span style="color:red">*</span>
                                <span title="A escrita entre parênteses pode ser colocada na tooltip">&#9432;</span>
                            </label>
                            <input type="checkbox" id="sp_dor" name="sinais_perda_implante[]" value="Dor" required> <label for="sp_dor">Dor</label><br>
                            <input type="checkbox" id="sp_hiper" name="sinais_perda_implante[]" value="Hipersensibilidade"> <label for="sp_hiper">Hipersensibilidade</label><br>
                            <input type="checkbox" id="sp_aumento" name="sinais_perda_implante[]" value="Aumento de sensibilidade"> <label for="sp_aumento">Aumento de sensibilidade</label><br>
                            <input type="checkbox" id="sp_dorm" name="sinais_perda_implante[]" value="Dormência"> <label for="sp_dorm">Dormência</label><br>
                            <input type="checkbox" id="sp_fist" name="sinais_perda_implante[]" value="Fístula"> <label for="sp_fist">Fístula</label><br>
                            <input type="checkbox" id="sp_inch" name="sinais_perda_implante[]" value="Inchaço"> <label for="sp_inch">Inchaço</label><br>
                            <input type="checkbox" id="sp_infl" name="sinais_perda_implante[]" value="Inflamação"> <label for="sp_infl">Inflamação</label><br>
                            <input type="checkbox" id="sp_abc" name="sinais_perda_implante[]" value="Abcesso"> <label for="sp_abc">Abcesso</label><br>
                            <input type="checkbox" id="sp_sang" name="sinais_perda_implante[]" value="Sangramento"> <label for="sp_sang">Sangramento</label><br>
                            <input type="checkbox" id="sp_mob" name="sinais_perda_implante[]" value="Mobilidade"> <label for="sp_mob">Mobilidade</label><br>
                            <input type="checkbox" id="sp_ass" name="sinais_perda_implante[]" value="Assintomático"> <label for="sp_ass">Assintomático</label><br>
                            <input type="checkbox" id="sp_nenhum" name="sinais_perda_implante[]" value="Nenhum dos itens se aplica"> <label for="sp_nenhum">Nenhum dos itens se aplica</label><br>
                            <input type="checkbox" id="sp_nao_houve" name="sinais_perda_implante[]" value="Não houve a perda do implante"> <label for="sp_nao_houve">Não houve a perda do implante</label><br>
                            <input type="checkbox" id="sinais_perda_implante_outros_check" name="sinais_perda_implante[]" value="Outros"> <label for="sinais_perda_implante_outros_check">Outros</label><br>
                            <div id="sinais_perda_implante_outros_div" class="conditional-field" style="display:none; border: none; padding-left: 0; margin-top: 0.5rem;">
                                <label for="sinais_perda_implante_outros">Descreva:</label>
                                <input type="text" id="sinais_perda_implante_outros" name="sinais_perda_implante_outros">
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Caso o implante ainda não tenha sido removido, foram observadas uma ou mais das seguintes evidências clínicas? <span style="color:red">*</span>
                                <span title="A escrita entre parênteses pode ser colocada na tooltip">&#9432;</span>
                            </label>
                            <input type="checkbox" id="ec_deis" name="evidencias_clinicas[]" value="Deiscência" required> <label for="ec_deis">Deiscência</label><br>
                            <input type="checkbox" id="ec_fene" name="evidencias_clinicas[]" value="Fenestração"> <label for="ec_fene">Fenestração</label><br>
                            <input type="checkbox" id="ec_peri" name="evidencias_clinicas[]" value="Peri-implantite"> <label for="ec_peri">Peri-implantite</label><br>
                            <input type="checkbox" id="perda_ossea_check" name="evidencias_clinicas[]" value="Perda óssea"> <label for="perda_ossea_check">Perda óssea</label>
                            <span title="Se selecionado, informar medida em mm">&#9432;</span>
                            <input type="text" id="perda_ossea_medida" name="perda_ossea_medida" placeholder="Medida (mm)" style="display:none; width:120px; padding: 0.5rem; height: auto; display: inline-block; margin-left: 1rem;">
                            <br>
                            <input type="checkbox" id="ec_nenhuma" name="evidencias_clinicas[]" value="Nenhuma das evidências clínicas foi observada"> <label for="ec_nenhuma">Nenhuma das evidências clínicas foi observada</label><br>
                            <input type="checkbox" id="ec_nao_remov" name="evidencias_clinicas[]" value="O implante ainda não foi removido"> <label for="ec_nao_remov">O implante ainda não foi removido</label><br>
                            <input type="checkbox" id="evidencias_clinicas_outros_check" name="evidencias_clinicas[]" value="Outros"> <label for="evidencias_clinicas_outros_check">Outros</label><br>
                            <div id="evidencias_clinicas_outros_div" class="conditional-field" style="display:none; border: none; padding-left: 0; margin-top: 0.5rem;">
                                <label for="evidencias_clinicas_outros">Descreva:</label>
                                <input type="text" id="evidencias_clinicas_outros" name="evidencias_clinicas_outros">
                            </div>
                        </div>
                    </div>

                    <div id="secao_componente" class="conditional-field" style="display:none;">
                        <h4>Relato Técnico | Componente Protético</h4>

                        <div id="pacienteInfoComponente" class="conditional-field" style="display:none; border:1px solid #eee; padding:1rem; margin-bottom:1rem; background: #fdfdfd;">
                            <h5>Informações do Paciente</h5>
                            <div class="form-group">
                                <label for="regiao_componente">Selecione a região em que o componente protético foi instalado: <span style="color:red">*</span></label>
                                <select name="regiao_componente" id="regiao_componente">
                                    <option value="">Selecione</option>
                                    <option value="Região 1">Região 1</option>
                                    <option value="Região 2">Região 2</option>
                                    <option value="Região 3">Região 3</option>
                                    </select>
                            </div>

                            <div class="form-group radio-group">
                                <label>Informe a posição do componente protético: <span style="color:red">*</span></label>
                                <input type="radio" id="pos_comp_ada" name="posicao_componente" value="ADA" required> <label for="pos_comp_ada">ADA</label>
                                <input type="radio" id="pos_comp_fdi" name="posicao_componente" value="FDI" required> <label for="pos_comp_fdi">FDI</label>
                            </div>
                        </div>

                        <h5>Informações da Prótese</h5>
                        <div class="form-group radio-group">
                            <label>Qual o tipo de prótese confeccionada? <span style="color:red">*</span></label>
                            <input type="radio" id="tp_unitaria" name="tipo_protese" value="Prótese unitária" required> <label for="tp_unitaria">Prótese unitária</label><br>
                            <input type="radio" id="tp_ponte" name="tipo_protese" value="Ponte fixa" required> <label for="tp_ponte">Ponte fixa</label><br>
                            <input type="radio" id="tp_fixa" name="tipo_protese" value="Prótese fixa" required> <label for="tp_fixa">Prótese fixa</label><br>
                            <input type="radio" id="tp_protocolo" name="tipo_protese" value="Protocolo" required> <label for="tp_protocolo">Protocolo</label><br>
                            <input type="radio" id="tp_over" name="tipo_protese" value="Overdenture" required> <label for="tp_over">Overdenture</label><br>
                            <input type="radio" id="tp_nenhuma" name="tipo_protese" value="Nenhuma prótese foi confeccionada" required> <label for="tp_nenhuma">Nenhuma prótese foi confeccionada</label><br>
                            <input type="radio" id="tipo_protese_outros_radio" name="tipo_protese" value="Outras" required> <label for="tipo_protese_outros_radio">Outras</label>
                            <input type="text" id="tipo_protese_outros" name="tipo_protese_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group radio-group">
                            <label>Foi utilizado um dispositivo para controle de torque? <span style="color:red">*</span></label>
                            <input type="radio" id="ct_sim" name="controle_torque" value="Sim" required> <label for="ct_sim">Sim</label><br>
                            <input type="radio" id="ct_nao" name="controle_torque" value="Não" required> <label for="ct_nao">Não</label><br>
                            <input type="radio" id="ct_nao_inst" name="controle_torque" value="O componente protético não foi instalado" required> <label for="ct_nao_inst">O componente protético não foi instalado</label>
                        </div>

                        <div class="form-group">
                            <label for="torque_aplicado">Torque aplicado: <span title="Se o torque foi aplicado, informar o valor é essencial para a análise. A ausência pode limitar a conclusão técnica do SAC.">&#9432;</span></label>
                            <input type="text" name="torque_aplicado" id="torque_aplicado" placeholder="Valor em N.cm">
                            <div class="checkbox-group" style="margin-top: 0.5rem;">
                                <input type="checkbox" id="torque_nao_aplicado" name="torque_aplicado_opcao" value="Não foi aplicado torque" onchange="toggleTorqueInputComponente()"> <label for="torque_nao_aplicado">Não foi aplicado torque</label><br>
                                <input type="checkbox" id="torque_desconhecido" name="torque_aplicado_opcao" value="Torque aplicado desconhecido" onchange="toggleTorqueInputComponente()"> <label for="torque_desconhecido">Torque aplicado desconhecido</label>
                            </div>
                        </div>

                        <h5>Informações do Evento</h5>
                        <div class="form-group">
                            <label>Data de instalação do implante:</label>
                            <input type="date" id="data_instalacao_implante_componente" name="data_instalacao_implante_componente">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="data_instalacao_implante_na" name="data_instalacao_implante_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_implante_componente','data_instalacao_implante_na')"> <label for="data_instalacao_implante_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Data da remoção do pilar:</label>
                            <input type="date" id="data_remocao_pilar" name="data_remocao_pilar">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="data_remocao_pilar_na" name="data_remocao_pilar_na" value="Não aplicável" onchange="toggleDataInput('data_remocao_pilar','data_remocao_pilar_na')"> <label for="data_remocao_pilar_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Data da instalação da prótese provisória:</label>
                            <input type="date" id="data_instalacao_protese_prov" name="data_instalacao_protese_prov">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="data_instalacao_protese_prov_na" name="data_instalacao_protese_prov_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_protese_prov','data_instalacao_protese_prov_na')"> <label for="data_instalacao_protese_prov_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Data da remoção da prótese temporária:</label>
                            <input type="date" id="data_remocao_protese_temp" name="data_remocao_protese_temp">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="data_remocao_protese_temp_na" name="data_remocao_protese_temp_na" value="Não aplicável" onchange="toggleDataInput('data_remocao_protese_temp','data_remocao_protese_temp_na')"> <label for="data_remocao_protese_temp_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Data da instalação da prótese definitiva:</label>
                            <input type="date" id="data_instalacao_protese_def" name="data_instalacao_protese_def">
                            <div class="checkbox-group" style="display: inline-block; margin-left: 1rem;">
                                <input type="checkbox" id="data_instalacao_protese_def_na" name="data_instalacao_protese_def_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_protese_def','data_instalacao_protese_def_na')"> <label for="data_instalacao_protese_def_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Considerando o histórico e o comportamento do paciente, algum dos fatores abaixo pode ter contribuído para o evento observado? <span style="color:red">*</span></label>
                            <input type="checkbox" id="fe_trauma" name="fatores_evento_componente[]" value="Trauma / Acidente" required> <label for="fe_trauma">Trauma / Acidente</label><br>
                            <input type="checkbox" id="fe_bruxismo" name="fatores_evento_componente[]" value="Bruxismo"> <label for="fe_bruxismo">Bruxismo</label><br>
                            <input type="checkbox" id="fe_sobrecarga" name="fatores_evento_componente[]" value="Sobrecarga biomecânica"> <label for="fe_sobrecarga">Sobrecarga biomecânica</label><br>
                            <input type="checkbox" id="fe_higiene" name="fatores_evento_componente[]" value="Higiene oral inadequada"> <label for="fe_higiene">Higiene oral inadequada</label><br>
                            <input type="checkbox" id="fe_manut" name="fatores_evento_componente[]" value="Falta de manutenção periódica / ausência de acompanhamento clínico"> <label for="fe_manut">Falta de manutenção periódica / ausência de acompanhamento clínico</label><br>
                            <input type="checkbox" id="fe_instab" name="fatores_evento_componente[]" value="Instabilidade ou falha do implante previamente detectada"> <label for="fe_instab">Instabilidade ou falha do implante previamente detectada</label><br>
                            <input type="checkbox" id="fe_nenhum" name="fatores_evento_componente[]" value="Nenhum fator observado"> <label for="fe_nenhum">Nenhum fator observado</label><br>
                            <input type="checkbox" id="fatores_evento_componente_outros_check" name="fatores_evento_componente[]" value="Outros"> <label for="fatores_evento_componente_outros_check">Outros</label>
                            <input type="text" id="fatores_evento_componente_outros" name="fatores_evento_componente_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group radio-group">
                            <label>Foi realizada consulta de controle após a instalação do componente protético? <span style="color:red">*</span></label>
                            <input type="radio" id="cc_sim" name="consulta_controle" value="Sim" required> <label for="cc_sim">Sim</label><br>                          <input type="radio" id="cc_nao" name="consulta_controle" value="Não" required> <label for="cc_nao">Não</label><br>
                            <input type="radio" id="cc_nao_inst" name="consulta_controle" value="O componente não foi instalado" required> <label for="cc_nao_inst">O componente não foi instalado</label><br>
                            <input type="radio" id="cc_antes" name="consulta_controle" value="O evento ocorreu antes da consulta de controle" required> <label for="cc_antes">O evento ocorreu antes da consulta de controle</label>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Com base na sua análise clínica e técnica, quais fatores abaixo podem estar diretamente relacionados ao evento observado com o componente protético? <span style="color:red">*</span></label>
                            <input type="checkbox" id="fa_desaperto" name="fatores_analise_componente[]" value="Desaperto do parafuso" required> <label for="fa_desaperto">Desaperto do parafuso</label><br>
                            <input type="checkbox" id="fa_fratura" name="fatores_analise_componente[]" value="Fratura do parafuso"> <label for="fa_fratura">Fratura do parafuso</label><br>
                            <input type="checkbox" id="fa_adapt" name="fatores_analise_componente[]" value="Adaptação imprecisa entre componente e implante"> <label for="fa_adapt">Adaptação imprecisa entre componente e implante</label><br>
                            <input type="checkbox" id="fa_incomp" name="fatores_analise_componente[]" value="Incompatibilidade entre componente e sistema utilizado"> <label for="fa_incomp">Incompatibilidade entre componente e sistema utilizado</label><br>
                            <input type="checkbox" id="fa_passiv" name="fatores_analise_componente[]" value="Passividade protética comprometida"> <label for="fa_passiv">Passividade protética comprometida</label><br>
                            <input type="checkbox" id="fa_desgaste" name="fatores_analise_componente[]" value="Desgaste ou fratura do componente protético"> <label for="fa_desgaste">Desgaste ou fratura do componente protético</label><br>
                            <input type="checkbox" id="fa_interf" name="fatores_analise_componente[]" value="Interferência oclusal"> <label for="fa_interf">Interferência oclusal</label><br>
                            <input type="checkbox" id="fa_lab" name="fatores_analise_componente[]" value="Fatores laboratoriais na confecção da prótese"> <label for="fa_lab">Fatores laboratoriais na confecção da prótese</label><br>
                            <input type="checkbox" id="fa_nenhuma" name="fatores_analise_componente[]" value="Nenhuma das opções contribuíram para o evento"> <label for="fa_nenhuma">Nenhuma das opções contribuíram para o evento</label><br>
                            <input type="checkbox" id="fatores_analise_componente_outros_check" name="fatores_analise_componente[]" value="Outros"> <label for="fatores_analise_componente_outros_check">Outros</label>
                            <input type="text" id="fatores_analise_componente_outros" name="fatores_analise_componente_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group radio-group">
                            <label>Caso haja indício de influência laboratorial, qual etapa você considera que pode ter contribuído para o evento? <span style="color:red">*</span></label>
                            <input type="radio" id="el_plan" name="etapa_laboratorial" value="Planejamento digital" required> <label for="el_plan">Planejamento digital</label><br>
                            <input type="radio" id="el_mold" name="etapa_laboratorial" value="Moldagem ou escaneamento" required> <label for="el_mold">Moldagem ou escaneamento</label><br>
                            <input type="radio" id="el_design" name="etapa_laboratorial" value="Design da estrutura protética" required> <label for="el_design">Design da estrutura protética</label><br>
                            <input type="radio" id="el_conf" name="etapa_laboratorial" value="Confecção da estrutura" required> <label for="el_conf">Confecção da estrutura <span class="udf-tooltip-icon">?<span class="udf-tooltip-content">Ex: usinagem, fundição</span></span></label><br>
                            <input type="radio" id="el_prova" name="etapa_laboratorial" value="Prova clínica intermediária" required> <label for="el_prova">Prova clínica intermediária</label><br>
                            <input type="radio" id="el_final" name="etapa_laboratorial" value="Finalização estética ou funcional" required> <label for="el_final">Finalização estética ou funcional</label><br>
                            <input type="radio" id="el_nenhuma" name="etapa_laboratorial" value="Nenhuma etapa laboratorial contribuiu" required> <label for="el_nenhuma">Nenhuma etapa laboratorial contribuiu</label><br>
                            <input type="radio" id="el_nao_houve" name="etapa_laboratorial" value="Não houve etapa laboratorial" required> <label for="el_nao_houve">Não houve etapa laboratorial</label><br>
                            <input type="radio" id="el_nao_aval" name="etapa_laboratorial" value="Ainda não foi possível avaliar" required> <label for="el_nao_aval">Ainda não foi possível avaliar</label><br>
                            <input type="radio" id="etapa_laboratorial_outros_radio" name="etapa_laboratorial" value="Outros" required> <label for="etapa_laboratorial_outros_radio">Outros</label>
                            <input type="text" id="etapa_laboratorial_outros" name="etapa_laboratorial_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group radio-group">
                            <label>Qual era a fase clínica no momento em que a ocorrência foi identificada? <span style="color:red">*</span></label>
                            <input type="radio" id="fc_antes_lab" name="fase_clinica" value="Antes da instalação – identificado em laboratório" required> <label for="fc_antes_lab">Antes da instalação – identificado em laboratório</label><br>
                            <input type="radio" id="fc_antes_cir" name="fase_clinica" value="Antes da instalação – identificado pelo cirurgião/dentista" required> <label for="fc_antes_cir">Antes da instalação – identificado pelo cirurgião/dentista</label><br>
                            <input type="radio" id="fc_plan" name="fase_clinica" value="Durante planejamento ou seleção do componente protético" required> <label for="fc_plan">Durante planejamento ou seleção do componente protético</label><br>
                            <input type="radio" id="fc_prova" name="fase_clinica" value="Durante a prova clínica" required> <label for="fc_prova">Durante a prova clínica (ex.: estrutura, metalocerâmica etc.)</label><br>
                            <input type="radio" id="fc_prov" name="fase_clinica" value="Durante fase provisória" required> <label for="fc_prov">Durante fase provisória (prótese provisória)</label><br>
                            <input type="radio" id="fc_apos" name="fase_clinica" value="Após instalação da prótese definitiva" required> <label for="fc_apos">Após instalação da prótese definitiva</label><br>
                            <input type="radio" id="fc_reint" name="fase_clinica" value="Após manutenção ou reintervenção clínica" required> <label for="fc_reint">Após manutenção ou reintervenção clínica</label><br>
                            <input type="radio" id="fc_nenhum" name="fase_clinica" value="Nenhum procedimento foi realizado até o momento do evento" required> <label for="fc_nenhum">Nenhum procedimento foi realizado até o momento do evento</label><br>
                            <input type="radio" id="fc_nao_id" name="fase_clinica" value="Não foi possível identificar a fase clínica no momento da ocorrência" required> <label for="fc_nao_id">Não foi possível identificar a fase clínica no momento da ocorrência</label><br>
                            <input type="radio" id="fase_clinica_outros_radio" name="fase_clinica" value="Outros" required> <label for="fase_clinica_outros_radio">Outros</label>
                            <input type="text" id="fase_clinica_outros" name="fase_clinica_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>
                    </div>
                    
                    <div id="secao_instrumental" class="conditional-field" style="display:none;">
                        <h4>Relato Técnico | Instrumental Cirúrgico</h4>
                        
                        <h5>Informações do Evento</h5>

                        <div class="form-group radio-group">
                            <label>Número aproximado de utilizações: <span style="color:red">*</span></label>
                            <input type="radio" id="nu_1" name="num_utilizacoes" value="1 vez" required> <label for="nu_1">1 vez</label><br>
                            <input type="radio" id="nu_2_5" name="num_utilizacoes" value="2 a 5 vezes" required> <label for="nu_2_5">2 a 5 vezes</label><br>
                            <input type="radio" id="nu_6_10" name="num_utilizacoes" value="6 a 10 vezes" required> <label for="nu_6_10">6 a 10 vezes</label><br>
                            <input type="radio" id="nu_11_15" name="num_utilizacoes" value="11 a 15 vezes" required> <label for="nu_11_15">11 a 15 vezes</label><br>
                            <input type="radio" id="nu_15_mais" name="num_utilizacoes" value="Mais de 15 vezes" required> <label for="nu_15_mais">Mais de 15 vezes</label><br>
                            <input type="radio" id="nu_nunca" name="num_utilizacoes" value="Nunca foi utilizado" required> <label for="nu_nunca">Nunca foi utilizado</label>
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Método(s) de limpeza utilizado(s): <span style="color:red">*</span></label>
                            <input type="checkbox" id="ml_manual" name="metodo_limpeza[]" value="Manual" required> <label for="ml_manual">Manual</label><br>
                            <input type="checkbox" id="ml_ultra" name="metodo_limpeza[]" value="Ultrassom"> <label for="ml_ultra">Ultrassom</label><br>
                            <input type="checkbox" id="ml_termo" name="metodo_limpeza[]" value="Termodesinfectora"> <label for="ml_termo">Termodesinfectora (desinfecção térmica automatizada)</label><br>
                            <input type="checkbox" id="ml_nenhum" name="metodo_limpeza[]" value="Nenhum método de limpeza foi realizado"> <label for="ml_nenhum">Nenhum método de limpeza foi realizado</label><br>
                            <input type="checkbox" id="metodo_limpeza_outros_check" name="metodo_limpeza[]" value="Outros"> <label for="metodo_limpeza_outros_check">Outros</label>
                            <input type="text" id="metodo_limpeza_outros" name="metodo_limpeza_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Produto(s) utilizado(s) na limpeza: <span style="color:red">*</span></label>
                            <input type="checkbox" id="pl_soro" name="produto_limpeza[]" value="Soro fisiológico" required> <label for="pl_soro">Soro fisiológico</label><br>
                            <input type="checkbox" id="pl_agua_ox" name="produto_limpeza[]" value="Água oxigenada"> <label for="pl_agua_ox">Água oxigenada</label><br>
                            <input type="checkbox" id="pl_deterg" name="produto_limpeza[]" value="Detergente enzimático"> <label for="pl_deterg">Detergente enzimático</label><br>
                            <input type="checkbox" id="pl_alcool" name="produto_limpeza[]" value="Álcool 70%"> <label for="pl_alcool">Álcool 70%</label><br>
                            <input type="checkbox" id="pl_clor" name="produto_limpeza[]" value="Clorexidina 2%"> <label for="pl_clor">Clorexidina 2%</label><br>
                            <input type="checkbox" id="pl_glut" name="produto_limpeza[]" value="Glutaraldeído"> <label for="pl_glut">Glutaraldeído</label><br>
                            <input type="checkbox" id="pl_nenhum" name="produto_limpeza[]" value="Nenhum produto foi utilizado"> <label for="pl_nenhum">Nenhum produto foi utilizado</label><br>
                            <input type="checkbox" id="produto_limpeza_outros_check" name="produto_limpeza[]" value="Outros"> <label for="produto_limpeza_outros_check">Outros</label>
                            <input type="text" id="produto_limpeza_outros" name="produto_limpeza_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Material utilizado para escovação/fricção (antissepsia manual): <span style="color:red">*</span></label>
                            <input type="checkbox" id="me_nylon" name="material_escovacao[]" value="Escova de nylon" required> <label for="me_nylon">Escova de nylon</label><br>
                            <input type="checkbox" id="me_gaze" name="material_escovacao[]" value="Panos ou gaze"> <label for="me_gaze">Panos ou gaze</label><br>
                            <input type="checkbox" id="me_esponja" name="material_escovacao[]" value="Esponja multiuso"> <label for="me_esponja">Esponja multiuso</label><br>
                            <input type="checkbox" id="me_aco_esc" name="material_escovacao[]" value="Escova de aço"> <label for="me_aco_esc">Escova de aço</label><br>
                            <input type="checkbox" id="me_aco_esp" name="material_escovacao[]" value="Esponja de aço"> <label for="me_aco_esp">Esponja de aço</label><br>
                            <input type="checkbox" id="me_nenhum" name="material_escovacao[]" value="Nenhum material foi utilizado"> <label for="me_nenhum">Nenhum material foi utilizado</label><br>
                            <input type="checkbox" id="material_escovacao_outros_check" name="material_escovacao[]" value="Outros"> <label for="material_escovacao_outros_check">Outros</label>
                            <input type="text" id="material_escovacao_outros" name="material_escovacao_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group checkbox-group">
                            <label>Método(s) de esterilização: <span style="color:red">*</span></label>
                            <input type="checkbox" id="mes_auto" name="metodo_esterilizacao[]" value="Autoclave" required> <label for="mes_auto">Autoclave</label><br>
                            <input type="checkbox" id="mes_estufa" name="metodo_esterilizacao[]" value="Estufa (calor seco)"> <label for="mes_estufa">Estufa (calor seco)</label><br>
                            <input type="checkbox" id="mes_chemi" name="metodo_esterilizacao[]" value="Chemiclave"> <label for="mes_chemi">Chemiclave</label><br>
                            <input type="checkbox" id="mes_nenhum" name="metodo_esterilizacao[]" value="Não foi esterilizado"> <label for="mes_nenhum">Não foi esterilizado</label><br>
                            <input type="checkbox" id="metodo_esterilizacao_outros_check" name="metodo_esterilizacao[]" value="Outros"> <label for="metodo_esterilizacao_outros_check">Outros</label>
                            <input type="text" id="metodo_esterilizacao_outros" name="metodo_esterilizacao_outros" style="display:none; margin-top: 0.5rem;" placeholder="Descreva">
                        </div>

                        <div class="form-group radio-group">
                            <label>O instrumental foi completamente seco antes da esterilização? <span style="color:red">*</span></label>
                            <input type="radio" id="seco_sim" name="seco_antes_esterilizacao" value="Sim" required> <label for="seco_sim">Sim</label><br>
                            <input type="radio" id="seco_nao" name="seco_antes_esterilizacao" value="Não" required> <label for="seco_nao">Não</label><br>
                            <input type="radio" id="seco_na" name="seco_antes_esterilizacao" value="O instrumental não foi submetido à esterilização" required> <label for="seco_na">O instrumental não foi submetido à esterilização</label>
                        </div>
                    </div>

                    <h4>Condições de Uso e Armazenamento</h4>
                    <div class="form-group radio-group">
                        <label>O produto foi utilizado em um paciente? <span style="color:red">*</span></label>
                        <input type="radio" id="utilizado_sim" name="utilizado_paciente" value="Sim" required onchange="togglePacienteInfo()">
                        <label for="utilizado_sim">Sim</label>
                        <input type="radio" id="utilizado_nao" name="utilizado_paciente" value="Não" required onchange="togglePacienteInfo()">
                        <label for="utilizado_nao">Não</label>
                    </div>

                    <div id="pacienteInfo" class="conditional-field" style="display:none; border:1px solid #eee; padding:1rem; margin-bottom:1rem; background: #fdfdfd;">
                        <h4>Informações do Paciente</h4>
                        <div class="form-group">
                            <label for="nome_paciente">Nome do Paciente</label>
                            <input type="text" id="nome_paciente" name="nome_paciente">
                        </div>
                        <div class="form-group">
                            <label for="idade_paciente">Idade</label>
                            <input type="number" id="idade_paciente" name="idade_paciente" min="0">
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>Registro médico: <span style="color:red">*</span></label>
                            <input type="checkbox" id="rm_diabetes" name="registro_medico[]" value="Diabetes mellitus" required> <label for="rm_diabetes">Diabetes mellitus</label><br>
                            <input type="checkbox" id="rm_cond" name="registro_medico[]" value="Condições endócrinas"> <label for="rm_cond">Condições endócrinas</label><br>
                            <input type="checkbox" id="rm_resist" name="registro_medico[]" value="Resistência imunológica"> <label for="rm_resist">Resistência imunológica</label><br>
                            <input type="checkbox" id="rm_medic" name="registro_medico[]" value="Uso crônico de medicamentos"> <label for="rm_medic">Uso crônico de medicamentos</label><br>
                            <input type="checkbox" id="rm_cort" name="registro_medico[]" value="Uso de corticoide"> <label for="rm_cort">Uso de corticoide</label><br>
                            <input type="checkbox" id="rm_quimio" name="registro_medico[]" value="Quimioterapia"> <label for="rm_quimio">Quimioterapia</label><br>
                            <input type="checkbox" id="rm_radio" name="registro_medico[]" value="Radioterapia na área da cabeça"> <label for="rm_radio">Radioterapia na área da cabeça</label><br>
                            <input type="checkbox" id="rm_infec" name="registro_medico[]" value="Infecção local"> <label for="rm_infec">Infecção local</label><br>
                            <input type="checkbox" id="rm_alt_linf" name="registro_medico[]" value="Alteração linfática"> <label for="rm_alt_linf">Alteração linfática</label><br>
                            <input type="checkbox" id="rm_dist_circ" name="registro_medico[]" value="Distúrbio circulatório"> <label for="rm_dist_circ">Distúrbio circulatório</label><br>
                            <input type="checkbox" id="rm_xero" name="registro_medico[]" value="Xerostomia"> <label for="rm_xero">Xerostomia</label><br>
                            <input type="checkbox" id="rm_dist_psi" name="registro_medico[]" value="Distúrbios psicológicos"> <label for="rm_dist_psi">Distúrbios psicológicos</label><br>
                            <input type="checkbox" id="rm_drogas" name="registro_medico[]" value="Consumo excessivo de drogas"> <label for="rm_drogas">Consumo excessivo de drogas</label><br>
                            <input type="checkbox" id="rm_alcool" name="registro_medico[]" value="Consumo excessivo de álcool"> <label for="rm_alcool">Consumo excessivo de álcool</label><br>
                            <input type="checkbox" id="rm_def_nut" name="registro_medico[]" value="Deficiência nutricional"> <label for="rm_def_nut">Deficiência nutricional</label><br>
                            <input type="checkbox" id="rm_sem_diag" name="registro_medico[]" value="Sem diagnóstico relevante"> <label for="rm_sem_diag">Sem diagnóstico relevante</label><br>
                            <input type="checkbox" id="registro_medico_outros_check" name="registro_medico[]" value="Outros"> <label for="registro_medico_outros_check">Outros</label>
                            <div id="registro_medico_outros_div" class="conditional-field" style="display:none; border: none; padding-left: 0; margin-top: 0.5rem;">
                                <label for="registro_medico_outros">Descreva:</label>
                                <input type="text" id="registro_medico_outros" name="registro_medico_outros">
                            </div>
                        </div>

                        <div class="form-group radio-group">
                            <label>Gênero: <span style="color:red">*</span></label>
                            <input type="radio" id="gen_masc" name="genero_paciente" value="Masculino" required> <label for="gen_masc">Masculino</label><br>
                            <input type="radio" id="gen_fem" name="genero_paciente" value="Feminino" required> <label for="gen_fem">Feminino</label><br>
                            <input type="radio" id="gen_trans" name="genero_paciente" value="Transgênero" required> <label for="gen_trans">Transgênero</label><br>
                            <input type="radio" id="gen_inter" name="genero_paciente" value="Intersexo" required> <label for="gen_inter">Intersexo</label><br>
                            <input type="radio" id="gen_nao" name="genero_paciente" value="Não reportado" required> <label for="gen_nao">Não reportado</label>
                        </div>

                        <div class="form-group radio-group">
                            <label>Paciente fumante: <span style="color:red">*</span></label>
                            <input type="radio" id="fum_sim" name="fumante_paciente" value="Sim" required> <label for="fum_sim">Sim</label>
                            <input type="radio" id="fum_nao" name="fumante_paciente" value="Não" required> <label for="fum_nao">Não</label>
                        </div>

                        <div class="form-group">
                            <label for="alergias">Alergias:</label>
                            <input type="text" id="alergias" name="alergias" placeholder="Descreva ou marque 'Não aplicável'">
                            <div class="checkbox-group" style="margin-top: 0.5rem;">
                                <input type="checkbox" id="alergias_na" name="alergias_na" value="Não aplicável" onchange="toggleAlergiasInput()"> <label for="alergias_na">Não aplicável</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="outras_doencas">Outras doenças locais ou sistêmicas relevantes:</label>
                            <input type="text" id="outras_doencas" name="outras_doencas" placeholder="Descreva ou marque 'Não aplicável'">
                            <div class="checkbox-group" style="margin-top: 0.5rem;">
                                <input type="checkbox" id="outras_doencas_na" name="outras_doencas_na" value="Não aplicável" onchange="toggleDoencasInput()"> <label for="outras_doencas_na">Não aplicável</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="armazenamento">Como o produto foi armazenado? <span style="color:red">*</span></label>
                        <select id="armazenamento" name="armazenamento" required>
                            <option value="">Selecione uma opção</option>
                            <option value="Em ambiente limpo, seco e com temperatura controlada (entre 15 °C e 30 °C)">Em ambiente limpo, seco e com temperatura controlada (entre 15 °C e 30 °C)</option>
                            <option value="Em ambiente refrigerado (abaixo de 10 °C)">Em ambiente refrigerado (abaixo de 10 °C)</option>
                            <option value="Em ambiente limpo, porém sem controle ativo de temperatura">Em ambiente limpo, porém sem controle ativo de temperatura</option>
                            <option value="Em ambiente com variações significativas de temperatura ao longo do dia (ex: locais sem climatização expostos ao sol)">Em ambiente com variações significativas de temperatura ao longo do dia (ex: locais sem climatização expostos ao sol)</option>
                            <option value="Em local exposto à umidade ou proximidade de lavadoras/pias">Em local exposto à umidade ou proximidade de lavadoras/pias</option>
                            <option value="Em local exposto a calor excessivo (acima de 40 °C)">Em local exposto a calor excessivo (acima de 40C)</option>
                            <option value="Em local provisório, sem controle ambiental adequado">Em local provisório, sem controle ambiental adequado</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div id="armazenamento_outros_div" class="conditional-field" style="display:none;">
                        <div class="form-group">
                            <label for="armazenamento_outros">Descreva:</label>
                            <input type="text" id="armazenamento_outros" name="armazenamento_outros">
                        </div>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>Após o recebimento, o produto foi exposto a alguma das situações abaixo? <span style="color:red">*</span></label>
                        <input type="checkbox" id="ex_bisturi" name="exposicao[]" value="Proximidade ou contato com bisturi elétrico" required> <label for="ex_bisturi">Proximidade ou contato com bisturi elétrico</label><br>
                        <input type="checkbox" id="ex_quim" name="exposicao[]" value="Contato com agentes químicos (ex: irrigadores, desinfetantes, hemostáticos)"> <label for="ex_quim">Contato com agentes químicos <span class="udf-tooltip-icon">?<span class="udf-tooltip-content">Ex: irrigadores, desinfetantes, hemostáticos</span></span></label><br>
                        <input type="checkbox" id="ex_calor" name="exposicao[]" value="Exposição direta a calor intenso ou chama (ex: cautério, termocautério)"> <label for="ex_calor">Exposição direta a calor intenso ou chama <span class="udf-tooltip-icon">?<span class="udf-tooltip-content">Ex: cautério, termocautério</span></span></label><br>
                        <input type="checkbox" id="ex_amb" name="exposicao[]" value="Presença em ambientes cirúrgicos externos ao consultório (ex: hospital, centro cirúrgico, campanhas e mutirões)"> <label for="ex_amb">Presença em ambientes cirúrgicos externos ao consultório <span class="udf-tooltip-icon">?<span class="udf-tooltip-content">Ex: hospital, centro cirúrgico, campanhas e mutirões</span></span></label><br>
                        <input type="checkbox" id="ex_queda" name="exposicao[]" value="Queda ou impacto acidental durante manuseio ou armazenamento local"> <label for="ex_queda">Queda ou impacto acidental durante manuseio ou armazenamento local</label><br>
                        <input type="checkbox" id="ex_nenhuma" name="exposicao[]" value="Nenhuma das situações mencionadas"> <label for="ex_nenhuma">Nenhuma das situações mencionadas</label><br>
                        <input type="checkbox" id="exposicao_outros_check" name="exposicao[]" value="Outros"> <label for="exposicao_outros_check">Outros</label>
                        <div id="exposicao_outros_div" class="conditional-field" style="display:none; border: none; padding-left: 0; margin-top: 0.5rem;">
                            <label for="exposicao_outros">Descreva:</label>
                            <input type="text" id="exposicao_outros" name="exposicao_outros">
                        </div>
                    </div>

                    <div class="button-group">
                        <button type="button" class="secondary-btn" onclick="prevStep3()">Voltar</button>
                        <button type="button" class="primary-btn" onclick="nextStep4()">Próximo</button>
                    </div>
                </div>

                <div id="step4" class="form-step" style="display:none;">
                    <h3>Passo 4: Documentação</h3>

                    <div class="form-group">
                        <h4>Nota Fiscal</h4>
                        <p class="helper-text">Anexe a nota fiscal do produto para prosseguirmos com a análise. (Opcional)</p>
                        <input type="file" id="nota_fiscal" name="nota_fiscal">
                    </div>

                    <div class="form-group">
                        <h4>Documentação Recomendada</h4>
                        <p class="helper-text">Obrigatório o anexo de pelo menos um arquivo nesta seção; caso não tenha feito nenhum upload nesta seção, é obrigatório que seja selecionado a opção "Não aplicável".</p>

                        <h5>Para ocorrências envolvendo implantes já instalados:</h5>
                        <div class="form-group">
                            <label for="radiografia_pre_operatoria">Radiografia pré-operatória (Tomografia, periapical e panorâmica)</label>
                            <input type="file" id="radiografia_pre_operatoria" name="radiografia_pre_operatoria">
                        </div>
                        <div class="form-group">
                            <label for="radiografia_pos_operatoria">Radiografia pós-operatória (Periapical)</label>
                            <input type="file" id="radiografia_pos_operatoria" name="radiografia_pos_operatoria">
                        </div>

                        <h5>Para ocorrências envolvendo componentes protéticos já instalados:</h5>
                        <div class="form-group">
                            <label for="radiografia_periapical_componente">Radiografia periapical com o componente instalado</label>
                            <input type="file" id="radiografia_periapical_componente" name="radiografia_periapical_componente">
                        </div>
                        <div class="form-group">
                            <label for="panoramica_protese">Panorâmica, no caso de próteses tipo protocolo</label>
                            <input type="file" id="panoramica_protese" name="panoramica_protese">
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="doc_nao_aplicavel" name="doc_nao_aplicavel" value="Não aplicável">
                            <label for="doc_nao_aplicavel">Não aplicável - Se nenhum dos cenários acima se aplica, selecione esta opção.</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <h4>Outros Arquivos</h4>
                        <p class="helper-text">Deseja enviar outros arquivos que possam auxiliar na análise? (Por exemplo: fotos clínicas intraoperatórias, imagens do produto na situação atual ou do lote/embalagem). (Opcional)</p>
                        <input type="file" id="outros_arquivos" name="outros_arquivos" multiple>
                    </div>

                    <div class="form-group">
                        <h4>Informações Importantes:</h4>
                        <ul>
                            <li><strong>Confidencialidade dos dados:</strong> As informações e documentos enviados serão utilizados exclusivamente para fins de análise técnica, com total respeito à confidencialidade e ao sigilo dos dados fornecidos.</li>
                        </ul>
                    </div>


                    <div class="button-group">
                        <button type="button" class="secondary-btn" onclick="prevStep4()">Voltar</button>
                        <button type="button" class="primary-btn" id="enviarJsonBtn">Enviar</button>
                    </div>
                </div>

            </div> </form>
    </div> <script>
    function toggleTipoPessoa() {
        var pessoaFisica = document.getElementById('pessoa_fisica').checked;
        var pessoaJuridica = document.getElementById('pessoa_juridica').checked;
        var fisicaFields = document.getElementById('pessoaFisicaFields');
        var juridicaFields = document.getElementById('pessoaJuridicaFields');
        var nameFisica = document.getElementById('name_fisica');
        var cpf = document.getElementById('cpf');
        var nameJuridica = document.getElementById('name_juridica');
        var cnpj = document.getElementById('cnpj');

        if (pessoaFisica) {
            fisicaFields.style.display = 'block';
            juridicaFields.style.display = 'none';
            nameFisica.required = true;
            cpf.required = true;
            nameJuridica.required = false;
            cnpj.required = false;
        } else if (pessoaJuridica) {
            fisicaFields.style.display = 'none';
            juridicaFields.style.display = 'block';
            nameFisica.required = false;
            cpf.required = false;
            nameJuridica.required = true;
            cnpj.required = true;
        }
    }

    function nextStep() {
        var numero = document.getElementById('numero').value;
        var email = document.getElementById('email').value;
        var pessoaFisica = document.getElementById('pessoa_fisica').checked;

        if (pessoaFisica) {
            var cpf = document.getElementById('cpf').value;
            if (!/^\d+$/.test(cpf)) {
                alert('O campo CPF deve conter apenas dígitos.');
                document.getElementById('cpf').focus();
                return;
            }
        } else {
            var cnpj = document.getElementById('cnpj').value;
            if (!/^\d+$/.test(cnpj)) {
                alert('O campo CNPJ deve conter apenas dígitos.');
                document.getElementById('cnpj').focus();
                return;
            }
        }

        // 2. Número: number input detection (apenas dígitos)
        if (!/^\d+$/.test(numero)) {
            alert('O campo Número deve conter apenas dígitos.');
            document.getElementById('numero').focus();
            return;
        }

        // 3. E-mail: contains "@"
        if (email.indexOf('@') === -1) {
            alert('O campo E-mail precisa conter um endereço válido com "@".');
            document.getElementById('email').focus();
            return;
        }

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
    function nextStep4() {
        document.getElementById('step3').style.display = 'none';
        document.getElementById('step4').style.display = 'block';
    }
    function prevStep4() {
        document.getElementById('step4').style.display = 'none';
        document.getElementById('step3').style.display = 'block';
    }

    // --- Real-time Validation & Input Restriction ---

    // CPF/CNPJ: Restrict input to numbers only (prevent letters)
    document.getElementById('cpf').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
    document.getElementById('cnpj').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // Number: Restrict input to numbers only
    document.getElementById('numero').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // Phone: Restrict input to numbers and standard phone symbols
    document.getElementById('phone').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9()\s\-+]/g, '');
    });

    // Email: Visual feedback on blur (red border if invalid)
    document.getElementById('email').addEventListener('blur', function() {
        if (this.value && this.value.indexOf('@') === -1) {
            this.style.borderColor = 'var(--cor-erro)';
        } else {
            this.style.borderColor = ''; // Reset
        }
    });
    document.getElementById('email').addEventListener('input', function() {
        this.style.borderColor = ''; // Reset while typing
    });

    document.getElementById('cep').addEventListener('blur', function() {
        var cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (!data.erro) {
                        document.getElementById('rua').value = data.logradouro || '';
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('municipio').value = data.localidade || '';
                        document.getElementById('estado').value = data.uf || '';
                        document.getElementById('complemento').value = data.complemento || '';
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

    <?php
    $json_path = plugin_dir_path(__FILE__) . 'produtos.json';
    $json_content = file_get_contents($json_path);
    if ($json_content === false) { $json_content = '{}'; }
    ?>

    // produtos/famílias
    const produtos = <?php echo $json_content; ?>;

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

    document.getElementById('referencia').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission
            preencherDescricaoProduto();
        }
    });

    // Exibe informações do paciente se selecionado "Sim"
    function togglePacienteInfo() {
        var sim = document.getElementById('utilizado_sim').checked;
        var pacienteDiv = document.getElementById('pacienteInfo');
        if (sim) {
            pacienteDiv.style.display = 'block';
            document.getElementById('nome_paciente').required = true;
            document.getElementById('idade_paciente').required = true;
        } else {
            pacienteDiv.style.display = 'none';
            document.getElementById('nome_paciente').required = false;
            document.getElementById('idade_paciente').required = false;
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

    // Data da remoção do implante: habilita/desabilita campo se "Não aplicável"
    function toggleRemocaoInput() {
        var remocaoInput = document.getElementById('data_remocao_implante');
        var remocaoNA = document.getElementById('remocao_na');
        if (remocaoNA.checked) {
            remocaoInput.value = '';
            remocaoInput.disabled = true;
        } else {
            remocaoInput.disabled = false;
        }
    }

    // Perda óssea: exibe campo de medida se selecionado
    document.getElementById('perda_ossea_check').addEventListener('change', function() {
        var medida = document.getElementById('perda_ossea_medida');
        if (this.checked) {
            medida.style.display = 'inline-block';
            medida.required = true;
        } else {
            medida.style.display = 'none';
            medida.required = false;
            medida.value = '';
        }
    });

    // Outros: exibe campo aberto para "contribuição evento"
    document.getElementById('contribuicao_evento_outros_check').addEventListener('change', function() {
        var outrosDiv = document.getElementById('contribuicao_evento_outros_div');
        if (this.checked) {
            outrosDiv.style.display = 'block';
            document.getElementById('contribuicao_evento_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('contribuicao_evento_outros').required = false;
        }
    });

    // Outros: exibe campo aberto para "sinais perda implante"
    document.getElementById('sinais_perda_implante_outros_check').addEventListener('change', function() {
        var outrosDiv = document.getElementById('sinais_perda_implante_outros_div');
        if (this.checked) {
            outrosDiv.style.display = 'block';
            document.getElementById('sinais_perda_implante_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('sinais_perda_implante_outros').required = false;
        }
    });

    // Outros: exibe campo aberto para "evidências clínicas"
    document.getElementById('evidencias_clinicas_outros_check').addEventListener('change', function() {
        var outrosDiv = document.getElementById('evidencias_clinicas_outros_div');
        if (this.checked) {
            outrosDiv.style.display = 'block';
            document.getElementById('evidencias_clinicas_outros').required = true;
        } else {
            outrosDiv.style.display = 'none';
            document.getElementById('evidencias_clinicas_outros').required = false;
        }
    });

    // Exibe campo aberto para "Outras" em tipo de prótese
    document.getElementById('tipo_protese_outros_radio').addEventListener('change', function() {
        document.getElementById('tipo_protese_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em fatores evento
    document.getElementById('fatores_evento_componente_outros_check').addEventListener('change', function() {
        document.getElementById('fatores_evento_componente_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em fatores análise
    document.getElementById('fatores_analise_componente_outros_check').addEventListener('change', function() {
        document.getElementById('fatores_analise_componente_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em etapa laboratorial
    document.getElementById('etapa_laboratorial_outros_radio').addEventListener('change', function() {
        document.getElementById('etapa_laboratorial_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em fase clínica
    document.getElementById('fase_clinica_outros_radio').addEventListener('change', function() {
        document.getElementById('fase_clinica_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Torque aplicado: desabilita campo se "Não foi aplicado" ou "Desconhecido"
    function toggleTorqueInputComponente() {
        var torqueInput = document.getElementById('torque_aplicado');
        var naoAplicado = document.getElementById('torque_nao_aplicado').checked;
        var desconhecido = document.getElementById('torque_desconhecido').checked;
        if (naoAplicado || desconhecido) {
            torqueInput.value = '';
            torqueInput.disabled = true;
        } else {
            torqueInput.disabled = false;
        }
    }

    // Datas: desabilita campo se "Não aplicável"
    function toggleDataInput(inputId, checkId) {
        var input = document.getElementById(inputId);
        var check = document.getElementById(checkId);
        if (check.checked) {
            input.value = '';
            input.disabled = true;
        } else {
            input.disabled = false;
        }
    }

    // Exibe Informações do Paciente para componente protético se "Sim" em utilizado_paciente
    document.getElementById('utilizado_sim').addEventListener('change', function() {
        var pacienteDiv = document.getElementById('pacienteInfoComponente');
        if (this.checked) {
            pacienteDiv.style.display = 'block';
            document.getElementById('regiao_componente').required = true;
            document.getElementsByName('posicao_componente')[0].required = true;
        }
    });
    document.getElementById('utilizado_nao').addEventListener('change', function() {
        var pacienteDiv = document.getElementById('pacienteInfoComponente');
        if (this.checked) {
            pacienteDiv.style.display = 'none';
            document.getElementById('regiao_componente').required = false;
            document.getElementsByName('posicao_componente')[0].required = false;
        }
    });

    // Exibe campo aberto para "Outros" em métodos de limpeza
    document.getElementById('metodo_limpeza_outros_check').addEventListener('change', function() {
        document.getElementById('metodo_limpeza_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em produtos de limpeza
    document.getElementById('produto_limpeza_outros_check').addEventListener('change', function() {
        document.getElementById('produto_limpeza_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em material de escovação
    document.getElementById('material_escovacao_outros_check').addEventListener('change', function() {
        document.getElementById('material_escovacao_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    // Exibe campo aberto para "Outros" em métodos de esterilização
    document.getElementById('metodo_esterilizacao_outros_check').addEventListener('change', function() {
        document.getElementById('metodo_esterilizacao_outros').style.display = this.checked ? 'inline-block' : 'none';
    });

    document.addEventListener('DOMContentLoaded', function() {
    toggleTipoPessoa();
});

    document.getElementById('enviarJsonBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const form = document.getElementById('multiStepForm');
        const formData = new FormData(form);
        const btn = this;

        btn.disabled = true;
        btn.textContent = 'Enviando...';

        const obj = {};
        for (let [key, value] of formData.entries()) {
            if (form.elements[key] && form.elements[key].type === 'file') continue;
            if (obj[key]) {
                if (!Array.isArray(obj[key])) obj[key] = [obj[key]];
                obj[key].push(value);
            } else {
                obj[key] = value;
            }
        }

        for (let pair of formData.keys()) {
            if (form.elements[pair] && form.elements[pair].type !== 'file') {
                formData.delete(pair);
            }
        }

        formData.append('json_data', JSON.stringify(obj));

        //console.log(Object.fromEntries(formData.entries()));
        //console.log('JSON string:', formData.get('json_data'));

        fetch('<?php echo esc_url(admin_url('admin-post.php?action=udf_handle_upload', '')); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert('Enviado com sucesso!');
            form.reset();
            // Reset steps and conditional fields
            document.getElementById('step4').style.display = 'none';
            document.getElementById('step3').style.display = 'none';
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
            document.querySelectorAll('.conditional-field').forEach(el => el.style.display = 'none');
            toggleTipoPessoa();

        })
        .catch(err => {
            alert('Erro ao enviar!');
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Enviar';
        });
    });

    </script>
    <?php return ob_get_clean();
}
add_shortcode('upload_drive_form', 'qualiform_register_shortcode');

add_action('admin_post_udf_handle_upload', 'udf_handle_upload');
add_action('admin_post_nopriv_udf_handle_upload', 'udf_handle_upload');

require_once plugin_dir_path(__FILE__) . 'drive-upload-handler.php';

// NOTE: The stray '}' that was here in your original file has been REMOVED.