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
