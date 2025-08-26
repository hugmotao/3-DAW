<?php
include 'disciplinas_arquivo.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>CRUD de Disciplinas</title>
</head>
<body>
    <h2>Adicionar Disciplina</h2>
    <form method="post">
        Nome: <input type="text" name="nome" required>
        <input type="submit" name="adicionar" value="Adicionar">
    </form>
    <h2>Lista de Disciplinas</h2>
    <ul>
        <?php foreach ($disciplinas as $id => $disciplina): ?>
            <li>
                <?php echo htmlspecialchars($disciplina); ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="text" name="novo_nome" placeholder="Editar" required>
                    <input type="submit" name="editar" value="Editar">
                </form>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="submit" name="excluir" value="Excluir">
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
