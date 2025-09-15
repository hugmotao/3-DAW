        <?php
$alunos = [];
$arquivo = __DIR__ . '/alunos.txt';
if (file_exists($arquivo)) {
    $handle = fopen($arquivo, 'r');
    if ($handle) {
        $i = 0;
        while (($linha = fgets($handle)) !== false) {
            $linha = trim($linha);
            if ($i == 0 || $linha === '') { $i++; continue; } // pula cabeçalho e linhas vazias
            list($matricula, $nome, $email) = explode(';', $linha);
            $alunos[] = [
                'matricula' => $matricula,
                'nome' => $nome,
                'email' => $email
            ];
            $i++;
        }
        fclose($handle);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista de Alunos</title>
</head>
<body>
    <h2>Alunos</h2>
    <table border="1">
        <tr>
            <th>Matrícula</th>
            <th>Nome</th>
            <th>Email</th>
        </tr>
        <?php foreach ($alunos as $aluno): ?>
        <tr>
            <td><?php echo htmlspecialchars($aluno['matricula']); ?></td>
            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
            <td><?php echo htmlspecialchars($aluno['email']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
