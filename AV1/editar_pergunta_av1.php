    <?php
    $arquivo = __DIR__ . '/perguntas.txt';
    $id = $_GET['id'] ?? '';
    $mensagem = '';
    $pergunta = null;
    if ($id && file_exists($arquivo)) {
        $linhas = [];
        $fp = fopen($arquivo, 'r');
        if ($fp) {
            while (($linha = fgets($fp)) !== false) {
                $linha = trim($linha);
                if ($linha === '') continue;
                $dados = explode(';', $linha);
                $linhas[] = $linha;
                if ($dados[0] === $id) {
                    if ($dados[1] === 'MULTIPLA') {
                        $opcoes = explode('|', $dados[3]);
                        $pergunta = [
                            'id' => $dados[0],
                            'tipo' => $dados[1],
                            'enunciado' => $dados[2],
                            'opcoes' => $opcoes,
                            'correta' => $dados[4]
                        ];
                    } elseif ($dados[1] === 'TEXTO') {
                        $pergunta = [
                            'id' => $dados[0],
                            'tipo' => $dados[1],
                            'enunciado' => $dados[2],
                            'resposta' => $dados[3]
                        ];
                    }
                }
            }
            fclose($fp);
        }
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pergunta) {
        $tipo = $_POST['tipo'] ?? '';
        $enunciado = trim($_POST['enunciado'] ?? '');
        $novas = [];
        if ($tipo === 'MULTIPLA') {
            $opcoes = [
                trim($_POST['opcao1'] ?? ''),
                trim($_POST['opcao2'] ?? ''),
                trim($_POST['opcao3'] ?? ''),
                trim($_POST['opcao4'] ?? '')
            ];
            $correta = $_POST['correta'] ?? '';
            if ($enunciado && $correta !== '' && !in_array('', $opcoes)) {
                foreach ($linhas as $linha) {
                    $dados = explode(';', $linha);
                    if ($dados[0] === $id) {
                        $linha = $id . ';MULTIPLA;' . $enunciado . ';' . implode('|', $opcoes) . ';' . $correta;
                    }
                    $novas[] = $linha;
                }
                $fpw = fopen($arquivo, 'w');
                if ($fpw) {
                    foreach ($novas as $linha) {
                        fwrite($fpw, $linha . "\n");
                    }
                    fclose($fpw);
                    $mensagem = 'Pergunta de múltipla escolha alterada!';
                } else {
                    $mensagem = 'Erro ao salvar alterações.';
                }
            } else {
                $mensagem = 'Preencha todos os campos da pergunta de múltipla escolha.';
            }
        } elseif ($tipo === 'TEXTO') {
            $resposta = trim($_POST['resposta'] ?? '');
            if ($enunciado && $resposta) {
                foreach ($linhas as $linha) {
                    $dados = explode(';', $linha);
                    if ($dados[0] === $id) {
                        $linha = $id . ';TEXTO;' . $enunciado . ';' . $resposta;
                    }
                    $novas[] = $linha;
                }
                $fpw = fopen($arquivo, 'w');
                if ($fpw) {
                    foreach ($novas as $linha) {
                        fwrite($fpw, $linha . "\n");
                    }
                    fclose($fpw);
                    $mensagem = 'Pergunta de texto alterada!';
                } else {
                    $mensagem = 'Erro ao salvar alterações.';
                }
            } else {
                $mensagem = 'Preencha todos os campos da pergunta de texto.';
            }
        }
        header('Location: editar_pergunta_av1.php?id=' . urlencode($id) . '&msg=' . urlencode($mensagem));
        exit;
    }
    if (isset($_GET['msg'])) {
        $mensagem = $_GET['msg'];
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Pergunta AV1</title>
</head>
<body>
    <h2>Editar Pergunta</h2>
    <?php if ($mensagem): ?>
        <p style="color: green;"><b><?php echo htmlspecialchars($mensagem); ?></b></p>
    <?php endif; ?>
    <?php if ($pergunta): ?>
    <form method="post" action="">
        <label>Tipo:
            <select name="tipo" id="tipo" onchange="mostrarCampos()">
                <option value="MULTIPLA" <?php if ($pergunta['tipo']==='MULTIPLA') echo 'selected'; ?>>Múltipla Escolha</option>
                <option value="TEXTO" <?php if ($pergunta['tipo']==='TEXTO') echo 'selected'; ?>>Texto</option>
            </select>
        </label><br><br>
        <label>Enunciado:<br>
            <textarea name="enunciado" required rows="3" cols="50"><?php echo htmlspecialchars($pergunta['enunciado']); ?></textarea>
        </label><br><br>
        <div id="multipla">
            <label>Opção 1: <input type="text" name="opcao1" value="<?php echo isset($pergunta['opcoes'][0]) ? htmlspecialchars($pergunta['opcoes'][0]) : ''; ?>" required></label><br>
            <label>Opção 2: <input type="text" name="opcao2" value="<?php echo isset($pergunta['opcoes'][1]) ? htmlspecialchars($pergunta['opcoes'][1]) : ''; ?>" required></label><br>
            <label>Opção 3: <input type="text" name="opcao3" value="<?php echo isset($pergunta['opcoes'][2]) ? htmlspecialchars($pergunta['opcoes'][2]) : ''; ?>" required></label><br>
            <label>Opção 4: <input type="text" name="opcao4" value="<?php echo isset($pergunta['opcoes'][3]) ? htmlspecialchars($pergunta['opcoes'][3]) : ''; ?>" required></label><br>
            <label>Correta:
                <select name="correta" required>
                    <option value="0" <?php if ($pergunta['correta']=='0') echo 'selected'; ?>>Opção 1</option>
                    <option value="1" <?php if ($pergunta['correta']=='1') echo 'selected'; ?>>Opção 2</option>
                    <option value="2" <?php if ($pergunta['correta']=='2') echo 'selected'; ?>>Opção 3</option>
                    <option value="3" <?php if ($pergunta['correta']=='3') echo 'selected'; ?>>Opção 4</option>
                </select>
            </label><br>
        </div>
        <div id="texto" style="display:none;">
            <label>Resposta:<br>
                <textarea name="resposta" rows="2" cols="50"><?php echo isset($pergunta['resposta']) ? htmlspecialchars($pergunta['resposta']) : ''; ?></textarea>
            </label><br>
        </div>
        <button type="submit">Salvar Alterações</button>
    </form>
    <script>
        function mostrarCampos() {
            var tipo = document.getElementById('tipo').value;
            document.getElementById('multipla').style.display = tipo === 'MULTIPLA' ? 'block' : 'none';
            document.getElementById('texto').style.display = tipo === 'TEXTO' ? 'block' : 'none';
        }
        window.onload = mostrarCampos;
    </script>
    <?php else: ?>
        <p>Pergunta não encontrada.</p>
    <?php endif; ?>
    <br>
    <a href="listar_perguntas_av1.php">Voltar</a>
</body>
</html>
