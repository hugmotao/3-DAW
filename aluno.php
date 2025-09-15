        <?php
$alunos = [];
$mensagem = '';

// ...existing code...
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula'] ?? '');
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $regex_email = '/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$/';

    if ($matricula === '' || $nome === '' || $email === '') {
        $mensagem = 'Preencha todos os campos!';
    } elseif (!preg_match($regex_email, $email)) {
        $mensagem = 'Email inválido!';
    } else {
    // ...existing code...
        $linha = "{$matricula};{$nome};{$email}\n";
        file_put_contents($arquivo, $linha, FILE_APPEND);
        $mensagem = 'Aluno registrado com sucesso!';
    // ...existing code...
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
$arquivo = __DIR__ . '/alunos.txt';
if (file_exists($arquivo)) {
    $handle = fopen($arquivo, 'r');
    if ($handle) {
        $i = 0;
        while (($linha = fgets($handle)) !== false) {
            $linha = trim($linha);
            if ($i == 0 || $linha === '') { $i++; continue; }
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
    <h2>Registrar Aluno</h2>
    <?php if ($mensagem): ?>
        <p style="color: red;"><b><?php echo htmlspecialchars($mensagem); ?></b></p>
    <?php endif; ?>
    <form method="post" action="">
        <label>Matrícula: <input type="text" name="matricula" required></label><br>
        <label>Nome: <input type="text" name="nome" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <button type="submit">Registrar</button>
    </form>
    <hr>
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
