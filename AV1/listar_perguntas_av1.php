<?php
$arquivo = __DIR__ . '/perguntas.txt';
$perguntas = [];
if (file_exists($arquivo)) {
    $fp = fopen($arquivo, 'r');
    if ($fp) {
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if ($dados[1] === 'MULTIPLA') {
                $opcoes = explode('|', $dados[3]);
                $perguntas[] = [
                    'id' => $dados[0],
                    'tipo' => $dados[1],
                    'enunciado' => $dados[2],
                    'opcoes' => $opcoes,
                    'correta' => $dados[4]
                ];
            } elseif ($dados[1] === 'TEXTO') {
                $perguntas[] = [
                    'id' => $dados[0],
                    'tipo' => $dados[1],
                    'enunciado' => $dados[2],
                    'resposta' => $dados[3]
                ];
            }
        }
        fclose($fp);
    }
}
        ?>
<!DOCTYPE html>
<html>
<head>
    <title>Listar Perguntas AV1</title>
</head>
                                                <body>
    <h2>Perguntas Cadastradas</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Enunciado</th>
            <th>Respostas/Opções</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($perguntas as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars($p['id']); ?></td>
            <td><?php echo htmlspecialchars($p['tipo']); ?></td>
            <td><?php echo htmlspecialchars($p['enunciado']); ?></td>
            <td>
                <?php if ($p['tipo'] === 'MULTIPLA'): ?>
                    <ul>
                    <?php foreach ($p['opcoes'] as $i => $op): ?>
                        <li><?php echo htmlspecialchars($op); ?> <?php if ($i == $p['correta']) echo '<b>(Correta)</b>'; ?></li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?php echo htmlspecialchars($p['resposta']); ?>
                <?php endif; ?>
            </td>
            <td>
                <a href="editar_pergunta_av1.php?id=<?php echo urlencode($p['id']); ?>">Editar</a> |
                <a href="excluir_pergunta_av1.php?id=<?php echo urlencode($p['id']); ?>" onclick="return confirm('Confirma excluir?');">Excluir</a> |
                <a href="ver_pergunta_av1.php?id=<?php echo urlencode($p['id']); ?>">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <a href="perguntas_av1.php">Cadastrar Nova Pergunta</a>
</body>
</html>
