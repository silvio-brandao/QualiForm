<?php
/**
 * Plugin Name: QualiForm
 * Description: An integrated form.
 * Version: 1.94
 * Author: Silvio Brandão
 */

if (!defined('ABSPATH')) exit;

function udf_register_shortcode() {
    ob_start(); ?>

    <style>
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

    <form id="multiStepForm" action="<?php echo esc_url(admin_url('admin-post.php?action=udf_handle_upload', 'https')); ?>" method="post" enctype="multipart/form-data">
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
            <input type="tel" id="phone" name="phone" required pattern="[0-9()\s\-]+"><br><br>

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
                
                <h5>Informações do Paciente</h5>

                <label>Selecione a região em que o implante dentário foi instalado: <span style="color:red">*</span></label>
                <br>
                <label><b>Universal:</b> selecione as caixas <span style="color:darkblue">azuis</span></label>
                <label><b>FDI:</b> selecione as caixas <span style="color:gray">cinzas</span></label>
                <br><br>

                <div class="dente-checkbox-container">
                    <!-- UNIVERSAL TOP-->
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

                    <!-- FDI TOP-->
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

                    <br><br><br>
                    <img decoding="async" src="https://koppimplantes.com/wp-content/uploads/2025/08/arc.png" alt="Arco Dental" class="dente-checkbox-img">
                    <br><br><br>

                    <!-- FDI BOTTOM-->
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

                    <!-- UNIVERSAL BOTTOM-->
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
                </div><br><br><br>

                <label>Informe a posição do implante: <span style="color:red">*</span></label>
                <input type="radio" name="posicao_implante" value="ADA" required> ADA
                <input type="radio" name="posicao_implante" value="FDI" required> FDI
                <br><br>

                <!-- Informações do Evento -->
                <h5>Informações do Evento</h5>
                <label for="data_instalacao_implante">Data de instalação do implante: <span style="color:red">*</span></label>
                <input type="date" id="data_instalacao_implante" name="data_instalacao_implante" required><br><br>

                <label>Data da remoção do implante:</label>
                <input type="date" id="data_remocao_implante" name="data_remocao_implante">
                <input type="checkbox" id="remocao_na" name="remocao_na" value="Não aplicável" onchange="toggleRemocaoInput()"> Não aplicável
                <br><br>

                <label>Qual era a condição de higiene peri-implantar no momento da reavaliação clínica do implante? <span style="color:red">*</span></label>
                <select name="condicao_higiene" id="condicao_higiene" required>
                    <option value="">Selecione</option>
                    <option value="Excelente">Excelente — sem presença de placa visível ou sinais de inflamação</option>
                    <option value="Boa">Boa — higiene adequada, com presença mínima de placa</option>
                    <option value="Regular">Regular — presença visível de placa e/ou sinais leves de inflamação</option>
                    <option value="Ruim">Ruim — acúmulo significativo de placa e inflamação evidente</option>
                    <option value="Nao reavaliado">A reavaliação clínica não foi realizada</option>
                </select><br><br>

                <label>Como foi o acompanhamento clínico do caso após a instalação? <span style="color:red">*</span></label>
                <select name="acompanhamento_clinico" id="acompanhamento_clinico" required>
                    <option value="">Selecione</option>
                    <option value="Integral">Calendário de consultas foi seguido integralmente</option>
                    <option value="Parcial">Acompanhamento clínico parcial</option>
                    <option value="Nao houve">Não houve acompanhamento clínico</option>
                </select><br><br>

                <label>Na sua análise clínica, uma ou mais das situações abaixo podem ter contribuído para o evento relatado? <span style="color:red">*</span></label><br>
                <input type="checkbox" name="contribuicao_evento[]" value="Bruxismo" required> Bruxismo<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Sobrecarga biomecânica"> Sobrecarga biomecânica<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Interferência funcional"> Interferência funcional (ex.: pressão da língua ou bochecha)<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Trauma / acidente"> Trauma / acidente<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Superaquecimento ósseo"> Superaquecimento ósseo<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Perfuração de seio"> Perfuração de seio<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Compressão do nervo"> Compressão do nervo<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Extração imediata"> Extração imediata<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Qualidade/quantidade óssea insuficiente"> Qualidade/quantidade óssea insuficiente<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Reabsorção óssea"> Reabsorção óssea<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Infecção"> Infecção<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Peri-implantite"> Peri-implantite<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Falha de osseointegração"> Falha de osseointegração<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Ausência de estabilidade secundária"> Ausência de estabilidade secundária<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Fratura do Implante"> Fratura do Implante<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Dente adjacente recebeu tratamento endodôntico"> Dente adjacente recebeu tratamento endodôntico<br>
                <input type="checkbox" name="contribuicao_evento[]" value="Nenhuma das opções contribuíram para o evento"> Nenhuma das opções contribuíram para o evento<br>
                <input type="checkbox" id="contribuicao_evento_outros_check" name="contribuicao_evento[]" value="Outros"> Outros<br>
                <div id="contribuicao_evento_outros_div" style="display:none;">
                    <label for="contribuicao_evento_outros">Descreva:</label>
                    <input type="text" id="contribuicao_evento_outros" name="contribuicao_evento_outros"><br>
                </div>
                <br>

                <label>Caso tenha ocorrido perda do implante, ela foi acompanhada por algum dos seguintes sinais ou sintomas? <span style="color:red">*</span>
                    <span title="A escrita entre parênteses pode ser colocada na tooltip">&#9432;</span>
                </label><br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Dor" required> Dor<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Hipersensibilidade"> Hipersensibilidade<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Aumento de sensibilidade"> Aumento de sensibilidade<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Dormência"> Dormência<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Fístula"> Fístula<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Inchaço"> Inchaço<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Inflamação"> Inflamação<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Abcesso"> Abcesso<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Sangramento"> Sangramento<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Mobilidade"> Mobilidade<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Assintomático"> Assintomático<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Nenhum dos itens se aplica"> Nenhum dos itens se aplica<br>
                <input type="checkbox" name="sinais_perda_implante[]" value="Não houve a perda do implante"> Não houve a perda do implante<br>
                <input type="checkbox" id="sinais_perda_implante_outros_check" name="sinais_perda_implante[]" value="Outros"> Outros<br>
                <div id="sinais_perda_implante_outros_div" style="display:none;">
                    <label for="sinais_perda_implante_outros">Descreva:</label>
                    <input type="text" id="sinais_perda_implante_outros" name="sinais_perda_implante_outros"><br>
                </div>
                <br>

                <label>Caso o implante ainda não tenha sido removido, foram observadas uma ou mais das seguintes evidências clínicas? <span style="color:red">*</span>
                    <span title="A escrita entre parênteses pode ser colocada na tooltip">&#9432;</span>
                </label><br>
                <input type="checkbox" name="evidencias_clinicas[]" value="Deiscência" required> Deiscência<br>
                <input type="checkbox" name="evidencias_clinicas[]" value="Fenestração"> Fenestração<br>
                <input type="checkbox" name="evidencias_clinicas[]" value="Peri-implantite"> Peri-implantite<br>
                <input type="checkbox" name="evidencias_clinicas[]" value="Perda óssea" id="perda_ossea_check"> Perda óssea
                <span title="Se selecionado, informar medida em mm">&#9432;</span>
                <input type="text" id="perda_ossea_medida" name="perda_ossea_medida" placeholder="Medida (mm)" style="display:none; width:120px;"><br>
                <input type="checkbox" name="evidencias_clinicas[]" value="Nenhuma das evidências clínicas foi observada"> Nenhuma das evidências clínicas foi observada<br>
                <input type="checkbox" name="evidencias_clinicas[]" value="O implante ainda não foi removido"> O implante ainda não foi removido<br>
                <input type="checkbox" id="evidencias_clinicas_outros_check" name="evidencias_clinicas[]" value="Outros"> Outros<br>
                <div id="evidencias_clinicas_outros_div" style="display:none;">
                    <label for="evidencias_clinicas_outros">Descreva:</label>
                    <input type="text" id="evidencias_clinicas_outros" name="evidencias_clinicas_outros"><br>
                </div>
                <br>
            </div>
            <div id="secao_componente" style="display:none;">
                <h4>Relato Técnico | Componente Protético</h4>

                <!-- Informações do Paciente (aparece só se "Sim" em utilizado_paciente) -->
                <div id="pacienteInfoComponente" style="display:none; border:1px solid #eee; padding:10px; margin-bottom:10px;">
                    <h5>Informações do Paciente</h5>
                    <label>Selecione a região em que o componente protético foi instalado: <span style="color:red">*</span></label>
                    <!-- Aqui futuramente insira o SVG/vetor enviado pela designer -->
                    <select name="regiao_componente" id="regiao_componente">
                        <option value="">Selecione</option>
                        <option value="Região 1">Região 1</option>
                        <option value="Região 2">Região 2</option>
                        <option value="Região 3">Região 3</option>
                        <!-- Adicione as regiões reais conforme o vetor -->
                    </select><br><br>

                    <label>Informe a posição do componente protético: <span style="color:red">*</span></label>
                    <input type="radio" name="posicao_componente" value="ADA" required> ADA
                    <input type="radio" name="posicao_componente" value="FDI" required> FDI
                    <br><br>
                </div>

                <!-- Informações da Prótese -->
                <h5>Informações da Prótese</h5>
                <label>Qual o tipo de prótese confeccionada? <span style="color:red">*</span></label><br>
                <input type="radio" name="tipo_protese" value="Prótese unitária" required> Prótese unitária
                <input type="radio" name="tipo_protese" value="Ponte fixa" required> Ponte fixa
                <input type="radio" name="tipo_protese" value="Prótese fixa" required> Prótese fixa
                <input type="radio" name="tipo_protese" value="Protocolo" required> Protocolo
                <input type="radio" name="tipo_protese" value="Overdenture" required> Overdenture
                <input type="radio" name="tipo_protese" value="Nenhuma prótese foi confeccionada" required> Nenhuma prótese foi confeccionada
                <input type="radio" name="tipo_protese" value="Outras" id="tipo_protese_outros_radio" required> Outras
                <input type="text" id="tipo_protese_outros" name="tipo_protese_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Foi utilizado um dispositivo para controle de torque? <span style="color:red">*</span></label><br>
                <input type="radio" name="controle_torque" value="Sim" required> Sim
                <input type="radio" name="controle_torque" value="Não" required> Não
                <input type="radio" name="controle_torque" value="O componente protético não foi instalado" required> O componente protético não foi instalado
                <br><br>

                <label>Torque aplicado:</label>
                <input type="text" name="torque_aplicado" id="torque_aplicado" placeholder="Valor em N.cm">
                <input type="checkbox" id="torque_nao_aplicado" name="torque_aplicado_opcao" value="Não foi aplicado torque" onchange="toggleTorqueInputComponente()"> Não foi aplicado torque
                <input type="checkbox" id="torque_desconhecido" name="torque_aplicado_opcao" value="Torque aplicado desconhecido" onchange="toggleTorqueInputComponente()"> Torque aplicado desconhecido
                <span title="Se o torque foi aplicado, informar o valor é essencial para a análise. A ausência pode limitar a conclusão técnica do SAC.">&#9432;</span>
                <br><br>

                <!-- Informações do Evento -->
                <h5>Informações do Evento</h5>
                <label>Data de instalação do implante:</label>
                <input type="date" id="data_instalacao_implante_componente" name="data_instalacao_implante_componente">
                <input type="checkbox" id="data_instalacao_implante_na" name="data_instalacao_implante_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_implante_componente','data_instalacao_implante_na')"> Não aplicável
                <br><br>

                <label>Data da remoção do pilar:</label>
                <input type="date" id="data_remocao_pilar" name="data_remocao_pilar">
                <input type="checkbox" id="data_remocao_pilar_na" name="data_remocao_pilar_na" value="Não aplicável" onchange="toggleDataInput('data_remocao_pilar','data_remocao_pilar_na')"> Não aplicável
                <br><br>

                <label>Data da instalação da prótese provisória:</label>
                <input type="date" id="data_instalacao_protese_prov" name="data_instalacao_protese_prov">
                <input type="checkbox" id="data_instalacao_protese_prov_na" name="data_instalacao_protese_prov_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_protese_prov','data_instalacao_protese_prov_na')"> Não aplicável
                <br><br>

                <label>Data da remoção da prótese temporária:</label>
                <input type="date" id="data_remocao_protese_temp" name="data_remocao_protese_temp">
                <input type="checkbox" id="data_remocao_protese_temp_na" name="data_remocao_protese_temp_na" value="Não aplicável" onchange="toggleDataInput('data_remocao_protese_temp','data_remocao_protese_temp_na')"> Não aplicável
                <br><br>

                <label>Data da instalação da prótese definitiva:</label>
                <input type="date" id="data_instalacao_protese_def" name="data_instalacao_protese_def">
                <input type="checkbox" id="data_instalacao_protese_def_na" name="data_instalacao_protese_def_na" value="Não aplicável" onchange="toggleDataInput('data_instalacao_protese_def','data_instalacao_protese_def_na')"> Não aplicável
                <br><br>

                <label>Considerando o histórico e o comportamento do paciente, algum dos fatores abaixo pode ter contribuído para o evento observado? <span style="color:red">*</span></label><br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Trauma / Acidente" required> Trauma / Acidente<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Bruxismo"> Bruxismo<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Sobrecarga biomecânica"> Sobrecarga biomecânica<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Higiene oral inadequada"> Higiene oral inadequada<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Falta de manutenção periódica / ausência de acompanhamento clínico"> Falta de manutenção periódica / ausência de acompanhamento clínico<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Instabilidade ou falha do implante previamente detectada"> Instabilidade ou falha do implante previamente detectada<br>
                <input type="checkbox" name="fatores_evento_componente[]" value="Nenhum fator observado"> Nenhum fator observado<br>
                <input type="checkbox" id="fatores_evento_componente_outros_check" name="fatores_evento_componente[]" value="Outros"> Outros<br>
                <input type="text" id="fatores_evento_componente_outros" name="fatores_evento_componente_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Foi realizada consulta de controle após a instalação do componente protético? <span style="color:red">*</span></label><br>
                <input type="radio" name="consulta_controle" value="Sim" required> Sim
                <input type="radio" name="consulta_controle" value="Não" required> Não
                <input type="radio" name="consulta_controle" value="O componente não foi instalado" required> O componente não foi instalado
                <input type="radio" name="consulta_controle" value="O evento ocorreu antes da consulta de controle" required> O evento ocorreu antes da consulta de controle
                <br><br>

                <label>Com base na sua análise clínica e técnica, quais fatores abaixo podem estar diretamente relacionados ao evento observado com o componente protético? <span style="color:red">*</span></label><br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Desaperto do parafuso" required> Desaperto do parafuso<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Fratura do parafuso"> Fratura do parafuso<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Adaptação imprecisa entre componente e implante"> Adaptação imprecisa entre componente e implante<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Incompatibilidade entre componente e sistema utilizado"> Incompatibilidade entre componente e sistema utilizado<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Passividade protética comprometida"> Passividade protética comprometida<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Desgaste ou fratura do componente protético"> Desgaste ou fratura do componente protético<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Interferência oclusal"> Interferência oclusal<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Fatores laboratoriais na confecção da prótese"> Fatores laboratoriais na confecção da prótese<br>
                <input type="checkbox" name="fatores_analise_componente[]" value="Nenhuma das opções contribuíram para o evento"> Nenhuma das opções contribuíram para o evento<br>
                <input type="checkbox" id="fatores_analise_componente_outros_check" name="fatores_analise_componente[]" value="Outros"> Outros<br>
                <input type="text" id="fatores_analise_componente_outros" name="fatores_analise_componente_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Caso haja indício de influência laboratorial, qual etapa você considera que pode ter contribuído para o evento? <span style="color:red">*</span></label><br>
                <input type="radio" name="etapa_laboratorial" value="Planejamento digital" required> Planejamento digital
                <input type="radio" name="etapa_laboratorial" value="Moldagem ou escaneamento" required> Moldagem ou escaneamento
                <input type="radio" name="etapa_laboratorial" value="Design da estrutura protética" required> Design da estrutura protética
                <input type="radio" name="etapa_laboratorial" value="Confecção da estrutura" required> Confecção da estrutura (ex: usinagem, fundição)
                <input type="radio" name="etapa_laboratorial" value="Prova clínica intermediária" required> Prova clínica intermediária
                <input type="radio" name="etapa_laboratorial" value="Finalização estética ou funcional" required> Finalização estética ou funcional
                <input type="radio" name="etapa_laboratorial" value="Nenhuma etapa laboratorial contribuiu" required> Nenhuma etapa laboratorial contribuiu
                <input type="radio" name="etapa_laboratorial" value="Não houve etapa laboratorial" required> Não houve etapa laboratorial
                <input type="radio" name="etapa_laboratorial" value="Ainda não foi possível avaliar" required> Ainda não foi possível avaliar
                <input type="radio" name="etapa_laboratorial" value="Outros" id="etapa_laboratorial_outros_radio" required> Outros
                <input type="text" id="etapa_laboratorial_outros" name="etapa_laboratorial_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Qual era a fase clínica no momento em que a ocorrência foi identificada? <span style="color:red">*</span></label><br>
                <input type="radio" name="fase_clinica" value="Antes da instalação – identificado em laboratório" required> Antes da instalação – identificado em laboratório
                <input type="radio" name="fase_clinica" value="Antes da instalação – identificado pelo cirurgião/dentista" required> Antes da instalação – identificado pelo cirurgião/dentista
                <input type="radio" name="fase_clinica" value="Durante planejamento ou seleção do componente protético" required> Durante planejamento ou seleção do componente protético
                <input type="radio" name="fase_clinica" value="Durante a prova clínica" required> Durante a prova clínica (ex.: estrutura, metalocerâmica etc.)
                <input type="radio" name="fase_clinica" value="Durante fase provisória" required> Durante fase provisória (prótese provisória)
                <input type="radio" name="fase_clinica" value="Após instalação da prótese definitiva" required> Após instalação da prótese definitiva
                <input type="radio" name="fase_clinica" value="Após manutenção ou reintervenção clínica" required> Após manutenção ou reintervenção clínica
                <input type="radio" name="fase_clinica" value="Nenhum procedimento foi realizado até o momento do evento" required> Nenhum procedimento foi realizado até o momento do evento
                <input type="radio" name="fase_clinica" value="Não foi possível identificar a fase clínica no momento da ocorrência" required> Não foi possível identificar a fase clínica no momento da ocorrência
                <input type="radio" name="fase_clinica" value="Outros" id="fase_clinica_outros_radio" required> Outros
                <input type="text" id="fase_clinica_outros" name="fase_clinica_outros" style="display:none;" placeholder="Descreva"><br><br>
            </div>
            <div id="secao_instrumental" style="display:none;">
                <h4>Relato Técnico | Instrumental Cirúrgico</h4>
                
                <h5>Informações do Evento</h5>

                <label>Número aproximado de utilizações: <span style="color:red">*</span></label><br>
                <input type="radio" name="num_utilizacoes" value="1 vez" required> 1 vez
                <input type="radio" name="num_utilizacoes" value="2 a 5 vezes" required> 2 a 5 vezes
                <input type="radio" name="num_utilizacoes" value="6 a 10 vezes" required> 6 a 10 vezes
                <input type="radio" name="num_utilizacoes" value="11 a 15 vezes" required> 11 a 15 vezes
                <input type="radio" name="num_utilizacoes" value="Mais de 15 vezes" required> Mais de 15 vezes
                <input type="radio" name="num_utilizacoes" value="Nunca foi utilizado" required> Nunca foi utilizado
                <br><br>

                <label>Método(s) de limpeza utilizado(s): <span style="color:red">*</span></label><br>
                <input type="checkbox" name="metodo_limpeza[]" value="Manual" required> Manual<br>
                <input type="checkbox" name="metodo_limpeza[]" value="Ultrassom"> Ultrassom<br>
                <input type="checkbox" name="metodo_limpeza[]" value="Termodesinfectora"> Termodesinfectora (desinfecção térmica automatizada)<br>
                <input type="checkbox" name="metodo_limpeza[]" value="Nenhum método de limpeza foi realizado"> Nenhum método de limpeza foi realizado<br>
                <input type="checkbox" id="metodo_limpeza_outros_check" name="metodo_limpeza[]" value="Outros"> Outros<br>
                <input type="text" id="metodo_limpeza_outros" name="metodo_limpeza_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Produto(s) utilizado(s) na limpeza: <span style="color:red">*</span></label><br>
                <input type="checkbox" name="produto_limpeza[]" value="Soro fisiológico" required> Soro fisiológico<br>
                <input type="checkbox" name="produto_limpeza[]" value="Água oxigenada"> Água oxigenada<br>
                <input type="checkbox" name="produto_limpeza[]" value="Detergente enzimático"> Detergente enzimático<br>
                <input type="checkbox" name="produto_limpeza[]" value="Álcool 70%"> Álcool 70%<br>
                <input type="checkbox" name="produto_limpeza[]" value="Clorexidina 2%"> Clorexidina 2%<br>
                <input type="checkbox" name="produto_limpeza[]" value="Glutaraldeído"> Glutaraldeído<br>
                <input type="checkbox" name="produto_limpeza[]" value="Nenhum produto foi utilizado"> Nenhum produto foi utilizado<br>
                <input type="checkbox" id="produto_limpeza_outros_check" name="produto_limpeza[]" value="Outros"> Outros<br>
                <input type="text" id="produto_limpeza_outros" name="produto_limpeza_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Material utilizado para escovação/fricção (antissepsia manual): <span style="color:red">*</span></label><br>
                <input type="checkbox" name="material_escovacao[]" value="Escova de nylon" required> Escova de nylon<br>
                <input type="checkbox" name="material_escovacao[]" value="Panos ou gaze"> Panos ou gaze<br>
                <input type="checkbox" name="material_escovacao[]" value="Esponja multiuso"> Esponja multiuso<br>
                <input type="checkbox" name="material_escovacao[]" value="Escova de aço"> Escova de aço<br>
                <input type="checkbox" name="material_escovacao[]" value="Esponja de aço"> Esponja de aço<br>
                <input type="checkbox" name="material_escovacao[]" value="Nenhum material foi utilizado"> Nenhum material foi utilizado<br>
                <input type="checkbox" id="material_escovacao_outros_check" name="material_escovacao[]" value="Outros"> Outros<br>
                <input type="text" id="material_escovacao_outros" name="material_escovacao_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>Método(s) de esterilização: <span style="color:red">*</span></label><br>
                <input type="checkbox" name="metodo_esterilizacao[]" value="Autoclave" required> Autoclave<br>
                <input type="checkbox" name="metodo_esterilizacao[]" value="Estufa (calor seco)"> Estufa (calor seco)<br>
                <input type="checkbox" name="metodo_esterilizacao[]" value="Chemiclave"> Chemiclave<br>
                <input type="checkbox" name="metodo_esterilizacao[]" value="Não foi esterilizado"> Não foi esterilizado<br>
                <input type="checkbox" id="metodo_esterilizacao_outros_check" name="metodo_esterilizacao[]" value="Outros"> Outros<br>
                <input type="text" id="metodo_esterilizacao_outros" name="metodo_esterilizacao_outros" style="display:none;" placeholder="Descreva"><br><br>

                <label>O instrumental foi completamente seco antes da esterilização? <span style="color:red">*</span></label><br>
                <input type="radio" name="seco_antes_esterilizacao" value="Sim" required> Sim
                <input type="radio" name="seco_antes_esterilizacao" value="Não" required> Não
                <input type="radio" name="seco_antes_esterilizacao" value="O instrumental não foi submetido à esterilização" required> O instrumental não foi submetido à esterilização
                <br><br>
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
            <button type="button" id="enviarJsonBtn">Enviar</button>
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

    document.getElementById('enviarJsonBtn').addEventListener('click', function(e) {
        e.preventDefault();

        const form = document.getElementById('multiStepForm');
        const formData = new FormData(form);

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

        fetch('<?php echo esc_url(admin_url('admin-post.php?action=udf_handle_upload', 'https')); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert('Enviado com sucesso!');
        })
        .catch(err => {
            alert('Erro ao enviar!');
        });
    });

    </script>
    <?php return ob_get_clean();
}
add_shortcode('upload_drive_form', 'udf_register_shortcode');

add_action('admin_post_udf_handle_upload', 'udf_handle_upload');
add_action('admin_post_nopriv_udf_handle_upload', 'udf_handle_upload');

require_once plugin_dir_path(__FILE__) . 'drive-upload-handler.php';
