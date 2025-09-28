<?php
$mensagem = '';
$arquivo_respostas = __DIR__ . '/respostas.txt';
$arquivo_perguntas = __DIR__ . '/perguntas.txt';
$arquivo_usuarios = __DIR__ . '/usuarios.txt';
$erros = [
    'perguntas' => 'Arquivo de perguntas não encontrado ou não pode ser lido.',
    'usuarios' => 'Arquivo de usuários não encontrado ou não pode ser lido.',
    'respostas' => 'Arquivo de respostas não pode ser escrito.',
    'campos' => 'Preencha todos os campos.'
];
$mensagem_erro = '';

$perguntas = [];
if (file_exists($arquivo_perguntas)) {
    $fp = fopen($arquivo_perguntas, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if (count($dados) < 3) continue;
            $perguntas[] = $dados;
        }
        fclose($fp);
    } else {
        $mensagem_erro = $erros['perguntas'];
    }
} else {
    $mensagem_erro = $erros['perguntas'];
}
$usuarios = [];
if (file_exists($arquivo_usuarios)) {
    $fp = fopen($arquivo_usuarios, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if (count($dados) < 3) continue;
            $usuarios[] = $dados;
        }
        fclose($fp);
    } else {
        $mensagem_erro = $erros['usuarios'];
    }
} else {
    $mensagem_erro = $erros['usuarios'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'] ?? '';
    $pergunta_id = $_POST['pergunta_id'] ?? '';
    $resposta = $_POST['resposta'] ?? '';
    if ($usuario_id && $pergunta_id && $resposta !== '') {
        $linha = $usuario_id . ';' . $pergunta_id . ';' . $resposta . "\n";
        $fp = fopen($arquivo_respostas, 'a');
        if ($fp) {
            if (fwrite($fp, $linha) === false) {
                $mensagem = $erros['respostas'];
            } else {
                $mensagem = 'Resposta registrada!';
            }
            fclose($fp);
        } else {
            $mensagem = $erros['respostas'];
        }
    } else {
        $mensagem = $erros['campos'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrar Resposta</title>
</head>
<body>
    <h2>Registrar Resposta</h2>
    <?php if ($mensagem_erro): ?>
        <p style="color:red;"><b><?php echo htmlspecialchars($mensagem_erro); ?></b></p>
    <?php endif; ?>
    <form method="post">
        Usuário:
        <select name="usuario_id">
            <?php foreach ($usuarios as $u): ?>
                <option value="<?php echo htmlspecialchars($u[0]); ?>"><?php echo htmlspecialchars($u[1] . ' (' . $u[2] . ')'); ?></option>
            <?php endforeach; ?>
        </select><br>
        Pergunta:
        <select name="pergunta_id">
            <?php foreach ($perguntas as $p): ?>
                <option value="<?php echo htmlspecialchars($p[0]); ?>"><?php echo htmlspecialchars($p[2]); ?></option>
            <?php endforeach; ?>
        </select><br>
        Resposta: <input type="text" name="resposta"><br>
        <button type="submit">Registrar</button>
    </form>
    <p style="color:blue;"><b><?php echo htmlspecialchars($mensagem); ?></b></p>
</body>
</html>
