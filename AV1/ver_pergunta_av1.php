<?php
$arquivo = __DIR__ . '/perguntas.txt';
$id = $_GET['id'] ?? '';
$pergunta = null;
$erro = '';
if (!$id) {
    $erro = 'ID da pergunta não informado.';
} elseif (!file_exists($arquivo)) {
    $erro = 'Arquivo de perguntas não encontrado.';
} else {
    $fp = fopen($arquivo, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
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
                break;
            }
        }
        fclose($fp);
        if (!$pergunta) {
            $erro = 'Pergunta não encontrada.';
        }
    } else {
        $erro = 'Erro ao abrir o arquivo.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visualizar Pergunta AV1</title>
</head>
<body>
    <h2>Pergunta</h2>
    <?php if ($erro): ?>
        <p style="color: red;"><b><?php echo htmlspecialchars($erro); ?></b></p>
    <?php else: ?>
        <b>ID:</b> <?php echo htmlspecialchars($pergunta['id']); ?><br>
        <b>Tipo:</b> <?php echo htmlspecialchars($pergunta['tipo']); ?><br>
        <b>Enunciado:</b> <?php echo htmlspecialchars($pergunta['enunciado']); ?><br>
        <?php if ($pergunta['tipo'] === 'MULTIPLA'): ?>
            <b>Opções:</b>
            <ul>
            <?php foreach ($pergunta['opcoes'] as $i => $op): ?>
                <li><?php echo htmlspecialchars($op); ?> <?php if ($i == $pergunta['correta']) echo '<b>(Correta)</b>'; ?></li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <b>Resposta:</b> <?php echo htmlspecialchars($pergunta['resposta']); ?><br>
        <?php endif; ?>
        <br>
        <a href="editar_pergunta_av1.php?id=<?php echo urlencode($pergunta['id']); ?>">Editar</a> |
        <a href="excluir_pergunta_av1.php?id=<?php echo urlencode($pergunta['id']); ?>">Excluir</a>
    <?php endif; ?>
    <br>
    <a href="listar_perguntas_av1.php">Voltar</a>
</body>
</html>
