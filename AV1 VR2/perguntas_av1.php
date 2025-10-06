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
    echo json_encode(['message' => $mensagem]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Perguntas AV1</title>
</head>
<body>
    <h2>Cadastrar Pergunta</h2>
    <nav>
        <a href="listar_perguntas_av1.php">Listar Perguntas</a> |
        <a href="perguntas_av1.php">Cadastrar Nova Pergunta</a>
    </nav>
    <div id="msg"><?php if ($mensagem): ?><p style="color: green;"><b><?php echo htmlspecialchars($mensagem); ?></b></p><?php endif; ?></div>
    <form method="post" action="">
        <label>Tipo:
            <select name="tipo" id="tipo" onchange="mostrarCampos()">
                <option value="MULTIPLA">Múltipla Escolha</option>
                <option value="TEXTO">Texto</option>
            </select>
        </label><br><br>
        <label>Enunciado:<br>
            <textarea name="enunciado" required rows="3" cols="50"></textarea>
        </label><br><br>
        <div id="multipla">
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
        <div id="texto" style="display:none;">
            <label>Resposta:<br>
                <textarea name="resposta" rows="2" cols="50"></textarea>
            </label><br>
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <script>
        (function(){
            var form = document.querySelector('form');
            var msg = document.getElementById('msg');
            form.addEventListener('submit', function(e){
                e.preventDefault();
                var fd = new FormData(form);
                fetch('', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
                .then(function(r){ return r.json(); })
                .then(function(j){ msg.innerHTML = '<p style="color:green;"><b>'+ (j.message || '') +'</b></p>'; })
                .catch(function(err){ msg.innerHTML = '<p style="color:red;"><b>Erro na requisição.</b></p>'; });
            });
        })();
    </script>
    <script>
        function mostrarCampos() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('multipla').style.display = tipo === 'MULTIPLA' ? 'block' : 'none';
            document.getElementById('texto').style.display = tipo === 'TEXTO' ? 'block' : 'none';
        }
        window.onload = mostrarCampos;
    </script>
</body>
</html>
