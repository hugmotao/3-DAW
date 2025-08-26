        <?php
    $dir = __DIR__ . '/disciplinas';
    if (!is_dir($dir)) {
        mkdir($dir);
    }

    // Adicionar disciplina
    if (isset($_POST['adicionar'])) {
        $nome = trim($_POST['nome']);
        if ($nome !== '') {
            $id = uniqid();
            $arquivo = fopen("$dir/$id.txt", "w");
            if ($arquivo) {
                fwrite($arquivo, $nome);
                fclose($arquivo);
            }
        }
    }

    // Editar disciplina
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $novo_nome = trim($_POST['novo_nome']);
        $caminho = "$dir/$id.txt";
        if ($novo_nome !== '' && file_exists($caminho)) {
            $arquivo = fopen($caminho, "w");
            if ($arquivo) {
                fwrite($arquivo, $novo_nome);
                fclose($arquivo);
            }
        }
    }

    // Excluir disciplina
    if (isset($_POST['excluir'])) {
        $id = $_POST['id'];
        $caminho = "$dir/$id.txt";
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    // Listar disciplinas
    $disciplinas = [];
    foreach (glob("$dir/*.txt") as $arquivo) {
        $id = basename($arquivo, '.txt');
        $nome = file_get_contents($arquivo);
        $disciplinas[$id] = $nome;
    }
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
    <?php
$dir = __DIR__ . '/disciplinas';
if (!is_dir($dir)) {
    mkdir($dir);
}

// Adicionar disciplina
if (isset($_POST['adicionar'])) {
    $nome = trim($_POST['nome']);
    if ($nome !== '') {
        $id = uniqid();
        $arquivo = fopen("$dir/$id.txt", "w");
        if ($arquivo) {
            fwrite($arquivo, $nome);
            fclose($arquivo);
        }
    }
}

// Editar disciplina
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $novo_nome = trim($_POST['novo_nome']);
    $caminho = "$dir/$id.txt";
    if ($novo_nome !== '' && file_exists($caminho)) {
        $arquivo = fopen($caminho, "w");
        if ($arquivo) {
            fwrite($arquivo, $novo_nome);
            fclose($arquivo);
        }
    }
}

// Excluir disciplina
if (isset($_POST['excluir'])) {
    $id = $_POST['id'];
    $caminho = "$dir/$id.txt";
    if (file_exists($caminho)) {
        unlink($caminho);
    }
}

// Listar disciplinas
$disciplinas = [];
foreach (glob("$dir/*.txt") as $arquivo) {
    $id = basename($arquivo, '.txt');
    $nome = file_get_contents($arquivo);
    $disciplinas[$id] = $nome;
}
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
<?php
// CRUD simples de disciplinas em PHP
// Armazena disciplinas em arquivos de texto em um diretório
session_start();
$dir = __DIR__ . '/disciplinas';
if (!is_dir($dir)) {
    mkdir($dir);
}

// Adicionar disciplina
if (isset($_POST['adicionar'])) {
    $nome = trim($_POST['nome']);
    if ($nome !== '') {
        $id = uniqid();
        file_put_contents("$dir/$id.txt", $nome);
    }
}

// Editar disciplina
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $novo_nome = trim($_POST['novo_nome']);
    if ($novo_nome !== '' && file_exists("$dir/$id.txt")) {
        file_put_contents("$dir/$id.txt", $novo_nome);
    }
}

// Excluir disciplina
if (isset($_POST['excluir'])) {
    $id = $_POST['id'];
    if (file_exists("$dir/$id.txt")) {
        unlink("$dir/$id.txt");
    }
}

// Listar disciplinas
$disciplinas = [];
foreach (glob("$dir/*.txt") as $arquivo) {
    $id = basename($arquivo, '.txt');
    $nome = file_get_contents($arquivo);
    $disciplinas[$id] = $nome;
}
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
