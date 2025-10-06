<?php
$mensagem = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arquivo = __DIR__ . '/perguntas.txt';
    $tipo = $_POST['tipo'] ?? '';
    $enunciado = $_POST['enunciado'] ?? '';
    while (substr($enunciado, 0, 1) === ' ') $enunciado = substr($enunciado, 1);
    while (substr($enunciado, -1) === ' ') $enunciado = substr($enunciado, 0, -1);
    if ($tipo === 'MULTIPLA') {
        $opcoes = [
            (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao1'] ?? '')),
            (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao2'] ?? '')),
            (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao3'] ?? '')),
            (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao4'] ?? ''))
        ];
        $correta = $_POST['correta'] ?? '';
        if ($enunciado && $correta !== '' && !in_array('', $opcoes)) {
            $id = uniqid('pm_');
            $linha = $id . ';MULTIPLA;' . $enunciado . ';' . implode('|', $opcoes) . ';' . $correta . "\n";
            $fp = fopen($arquivo, 'a');
            if ($fp) {
                fwrite($fp, $linha);
                fclose($fp);
                $mensagem = 'Pergunta de múltipla escolha cadastrada!';
            } else {
                $mensagem = 'Erro ao salvar a pergunta. Verifique permissões do arquivo.';
            }
        } else {
            $mensagem = 'Preencha todos os campos da pergunta de múltipla escolha.';
        }
    } elseif ($tipo === 'TEXTO') {
    $resposta = $_POST['resposta'] ?? '';
    while (substr($resposta, 0, 1) === ' ') $resposta = substr($resposta, 1);
    while (substr($resposta, -1) === ' ') $resposta = substr($resposta, 0, -1);
        if ($enunciado && $resposta) {
            $id = uniqid('pt_');
            $linha = $id . ';TEXTO;' . $enunciado . ';' . $resposta . "\n";
            $fp = fopen($arquivo, 'a');
            if ($fp) {
                fwrite($fp, $linha);
                fclose($fp);
                $mensagem = 'Pergunta de texto cadastrada!';
            } else {
                $mensagem = 'Erro ao salvar a pergunta. Verifique permissões do arquivo.';
            }
        } else {
            $mensagem = 'Preencha todos os campos da pergunta de texto.';
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['message'=>$mensagem]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Cadastro de Perguntas AV1</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; line-height: 1.4; padding: 1rem; }
        label { display:block; margin-bottom: .5rem; }
        .controls { margin-bottom: 1rem; }
        .message { padding: .5rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .message.success { background:#e6ffed; color:#0a6b2d; border:1px solid #b6f0c7; }
        .message.error { background:#ffecec; color:#8b1a1a; border:1px solid #f1b6b6; }
    </style>
</head>
<body>
    <h2>Cadastrar Pergunta</h2>
    <nav>
        <a href="listar_perguntas_av1.php">Listar Perguntas</a> |
        <a href="perguntas_av1.php">Cadastrar Nova Pergunta</a>
    </nav>
    <div id="msg"><?php if ($mensagem): ?><div class="message success"><b><?php echo htmlspecialchars($mensagem); ?></b></div><?php endif; ?></div>
    <form method="post" action="">
        <div class="controls">
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo">
                <option value="MULTIPLA">Múltipla Escolha</option>
                <option value="TEXTO">Texto</option>
            </select>
        </div>

        <div class="controls">
            <label for="enunciado">Enunciado:</label>
            <textarea id="enunciado" name="enunciado" required rows="3" cols="50"></textarea>
        </div>

        <div id="multipla">
            <label for="opcao1">Opção 1:</label>
            <input id="opcao1" type="text" name="opcao1" required>
            <label for="opcao2">Opção 2:</label>
            <input id="opcao2" type="text" name="opcao2" required>
            <label for="opcao3">Opção 3:</label>
            <input id="opcao3" type="text" name="opcao3" required>
            <label for="opcao4">Opção 4:</label>
            <input id="opcao4" type="text" name="opcao4" required>
            <div class="controls">
                <label for="correta">Correta:</label>
                <select id="correta" name="correta" required>
                    <option value="0">Opção 1</option>
                    <option value="1">Opção 2</option>
                    <option value="2">Opção 3</option>
                    <option value="3">Opção 4</option>
                </select>
            </div>
        </div>

        <div id="texto" style="display:none;">
            <label for="resposta">Resposta:</label>
            <textarea id="resposta" name="resposta" rows="2" cols="50"></textarea>
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <script>
        // Modern simplified JS: handle field toggling and AJAX submit
        document.addEventListener('DOMContentLoaded', function () {
            var tipoEl = document.getElementById('tipo');
            var multiplaEl = document.getElementById('multipla');
            var textoEl = document.getElementById('texto');
            var form = document.querySelector('form');
            var msg = document.getElementById('msg');

            function mostrarCampos() {
                var tipo = tipoEl.value;
                multiplaEl.style.display = tipo === 'MULTIPLA' ? 'block' : 'none';
                textoEl.style.display = tipo === 'TEXTO' ? 'block' : 'none';
            }

            // initialize UI
            if (tipoEl) tipoEl.addEventListener('change', mostrarCampos);
            mostrarCampos();

            if (!form) return;

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                var fd = new FormData(form);
                try {
                    var response = await fetch('', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
                    var json = await response.json();
                    msg.innerHTML = '<div class="message success"><b>' + (json.message || '') + '</b></div>';
                    form.reset();
                    mostrarCampos();
                } catch (err) {
                    msg.innerHTML = '<div class="message error"><b>Erro na requisição.</b></div>';
                    console.error(err);
                }
            });
        });
    </script>
</body>
</html>
