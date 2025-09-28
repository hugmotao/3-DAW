<?php
$arquivo_respostas = __DIR__ . '/respostas.txt';
$arquivo_perguntas = __DIR__ . '/perguntas.txt';
$arquivo_usuarios = __DIR__ . '/usuarios.txt';
$erros = [
    'respostas' => 'Arquivo de respostas não encontrado ou não pode ser lido.',
    'perguntas' => 'Arquivo de perguntas não encontrado ou não pode ser lido.',
    'usuarios' => 'Arquivo de usuários não encontrado ou não pode ser lido.'
];
$mensagem_erro = '';
$respostas = [];
if (file_exists($arquivo_respostas)) {
    $fp = fopen($arquivo_respostas, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if (count($dados) < 3) continue; // resposta incompleta
            $respostas[] = $dados;
        }
        fclose($fp);
    } else {
        $mensagem_erro = $erros['respostas'];
    }
} else {
    $mensagem_erro = $erros['respostas'];
}
$perguntas = [];
if (file_exists($arquivo_perguntas)) {
    $fp = fopen($arquivo_perguntas, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if (count($dados) < 3) continue;
            $perguntas[$dados[0]] = $dados[2];
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
            if (count($dados) < 2) continue;
            $usuarios[$dados[0]] = $dados[1];
        }
        fclose($fp);
    } else {
        $mensagem_erro = $erros['usuarios'];
    }
} else {
    $mensagem_erro = $erros['usuarios'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ver Respostas</title>
</head>
<body>
    <h2>Respostas Registradas</h2>
    <?php if ($mensagem_erro): ?>
        <p style="color:red;"><b><?php echo htmlspecialchars($mensagem_erro); ?></b></p>
    <?php endif; ?>
    <?php if (empty($respostas)): ?>
        <p>Nenhuma resposta cadastrada.</p>
    <?php else: ?>
    <table border="1">
        <tr>
            <th>Usuário</th>
            <th>Pergunta</th>
            <th>Resposta</th>
        </tr>
        <?php foreach ($respostas as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars(isset($usuarios[$r[0]]) ? $usuarios[$r[0]] : 'Usuário desconhecido'); ?></td>
            <td><?php echo htmlspecialchars(isset($perguntas[$r[1]]) ? $perguntas[$r[1]] : 'Pergunta desconhecida'); ?></td>
            <td><?php echo htmlspecialchars($r[2]); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</body>
</html>
