    <?php require_once 'perguntas_controller.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Gerenciamento de Perguntas</title>
    <style>
        #form-alterar { display: none; margin-top: 20px; padding: 15px; border: 1px solid #ccc; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tabs button { padding: 10px; border: 1px solid #ccc; background: #f0f0f0; cursor: pointer; }
        .tabs button.active { background: #ddd; }
    </style>
</head>
<body>
    <nav>
        <a href="listar_perguntas_av1.php">Listar Perguntas</a>
    </nav>
    <div id="msg"></div>

    <div class="tabs">
        <button onclick="openTab(event, 'cadastrar')" class="active">Cadastrar Pergunta</button>
        <button onclick="openTab(event, 'alterar')">Alterar Pergunta</button>
    </div>

    <div id="cadastrar" class="tab-content active">
        <h2>Cadastrar Pergunta</h2>
        <form id="form-cadastrar" method="post" action="perguntas_controller.php">
            <input type="hidden" name="acao" value="cadastrar">
            <label>Tipo:
                <select name="tipo" class="tipo-select">
                    <option value="MULTIPLA">Múltipla Escolha</option>
                    <option value="TEXTO">Texto</option>
                </select>
            </label><br><br>
            <label>Enunciado:<br>
                <textarea name="enunciado" required rows="3" cols="50"></textarea>
            </label><br><br>
            <div class="multipla-fields">
                <label>Opção 1: <input type="text" name="opcao1" required></label><br>
                <label>Opção 2: <input type="text" name="opcao2" required></label><br>
                <label>Opção 3: <input type="text" name="opcao3" required></label><br>
                <label>Opção 4: <input type="text" name="opcao4" required></label><br>
                <label>Correta:
                    <select name="correta" required>
                        <option value="0">Opção 1</option>
                        <option value="1">Opção 2</option>
                        <option value="2">Opção 3</option>
                        <option value="3">Opção 4</option>
                    </select>
                </label><br>
            </div>
            <div class="texto-fields" style="display:none;">
                <label>Resposta:<br>
                    <textarea name="resposta" rows="2" cols="50"></textarea>
                </label><br>
            </div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <div id="alterar" class="tab-content">
        <h2>Alterar Pergunta</h2>
        <form id="form-buscar">
            <label>Código da Pergunta: <input type="text" id="codigo_pergunta" required></label>
            <button type="submit">Buscar</button>
        </form>

        <form id="form-alterar" method="post" action="perguntas_controller.php">
            <input type="hidden" name="acao" value="alterar">
            <input type="hidden" name="codigo" id="alterar_codigo">
            <input type="hidden" name="tipo" id="alterar_tipo">

            <label>Enunciado:<br>
                <textarea name="enunciado" id="alterar_enunciado" required rows="3" cols="50"></textarea>
            </label><br><br>

            <div id="alterar_multipla" style="display:none;">
                <label>Opção 1: <input type="text" name="opcao1" id="alterar_opcao1" required></label><br>
                <label>Opção 2: <input type="text" name="opcao2" id="alterar_opcao2" required></label><br>
                <label>Opção 3: <input type="text" name="opcao3" id="alterar_opcao3" required></label><br>
                <label>Opção 4: <input type="text" name="opcao4" id="alterar_opcao4" required></label><br>
                <label>Correta:
                    <select name="correta" id="alterar_correta" required>
                        <option value="0">Opção 1</option>
                        <option value="1">Opção 2</option>
                        <option value="2">Opção 3</option>
                        <option value="3">Opção 4</option>
                    </select>
                </label><br>
            </div>
            <div id="alterar_texto" style="display:none;">
                <label>Resposta:<br>
                    <textarea name="resposta" id="alterar_resposta" rows="2" cols="50"></textarea>
                </label><br>
            </div>
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tabs")[0].getElementsByTagName("button");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
            document.getElementById('msg').innerHTML = '';
        }

        function configurarCamposDinamicos(form) {
            var tipoSelect = form.querySelector('.tipo-select');
            var multiplaFields = form.querySelector('.multipla-fields');
            var textoFields = form.querySelector('.texto-fields');

            function mostrarCampos() {
                var tipo = tipoSelect.value;
                if (multiplaFields) multiplaFields.style.display = tipo === 'MULTIPLA' ? 'block' : 'none';
                if (textoFields) textoFields.style.display = tipo === 'TEXTO' ? 'block' : 'none';
                
                var multiplaInputs = multiplaFields ? multiplaFields.querySelectorAll('input, select') : [];
                multiplaInputs.forEach(function(input) { input.required = (tipo === 'MULTIPLA'); });
                
                var textoInputs = textoFields ? textoFields.querySelectorAll('textarea') : [];
                textoInputs.forEach(function(input) { input.required = (tipo === 'TEXTO'); });
            }

            tipoSelect.addEventListener('change', mostrarCampos);
            mostrarCampos();
        }

        document.addEventListener('DOMContentLoaded', function() {
            configurarCamposDinamicos(document.getElementById('form-cadastrar'));

            var msgDiv = document.getElementById('msg');
            var controllerUrl = 'perguntas_controller.php';

            document.getElementById('form-cadastrar').addEventListener('submit', function(e) {
                e.preventDefault();
                var form = this;
                var fd = new FormData(form);
                fetch(controllerUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
                .then(function(r) { return r.json(); })
                .then(function(j) {
                    msgDiv.innerHTML = '<p style="color:green;"><b>' + (j.message || '') + '</b></p>';
                    form.reset();
                    configurarCamposDinamicos(form);
                })
                .catch(function() { msgDiv.innerHTML = '<p style="color:red;"><b>Erro na requisição de cadastro.</b></p>'; });
            });

            document.getElementById('form-buscar').addEventListener('submit', function(e) {
                e.preventDefault();
                var codigo = document.getElementById('codigo_pergunta').value;
                var formAlterar = document.getElementById('form-alterar');
                
                fetch(controllerUrl + '?acao=buscar&codigo=' + encodeURIComponent(codigo))
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Pergunta não encontrada');
                    }
                    return response.json();
                })
                .then(function(data) {
                    msgDiv.innerHTML = '';
                    formAlterar.style.display = 'block';
                    
                    document.getElementById('alterar_codigo').value = data.codigo;
                    document.getElementById('alterar_tipo').value = data.tipo;
                    document.getElementById('alterar_enunciado').value = data.enunciado;

                    var divMultipla = document.getElementById('alterar_multipla');
                    var divTexto = document.getElementById('alterar_texto');

                    if (data.tipo === 'MULTIPLA') {
                        divMultipla.style.display = 'block';
                        divTexto.style.display = 'none';
                        document.getElementById('alterar_opcao1').value = data.opcoes[0];
                        document.getElementById('alterar_opcao2').value = data.opcoes[1];
                        document.getElementById('alterar_opcao3').value = data.opcoes[2];
                        document.getElementById('alterar_opcao4').value = data.opcoes[3];
                        document.getElementById('alterar_correta').value = data.correta;
                    } else {
                        divMultipla.style.display = 'none';
                        divTexto.style.display = 'block';
                        document.getElementById('alterar_resposta').value = data.resposta;
                    }
                })
                .catch(function(error) {
                    msgDiv.innerHTML = '<p style="color:red;"><b>' + error.message + '</b></p>';
                    formAlterar.style.display = 'none';
                });
            });

            document.getElementById('form-alterar').addEventListener('submit', function(e) {
                e.preventDefault();
                var form = this;
                var fd = new FormData(form);
                fetch(controllerUrl, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd })
                .then(function(r) { return r.json(); })
                .then(function(j) {
                    msgDiv.innerHTML = '<p style="color:green;"><b>' + (j.message || '') + '</b></p>';
                    form.style.display = 'none';
                    document.getElementById('form-buscar').reset();
                })
                .catch(function() { msgDiv.innerHTML = '<p style="color:red;"><b>Erro na requisição de alteração.</b></p>'; });
            });
        });
    </script>
</body>
</html>
