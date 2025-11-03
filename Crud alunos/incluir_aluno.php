<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $email = $_POST['email'];
    
    $sql = "INSERT INTO alunos (nome, matricula, email) VALUES ('$nome', '$matricula', '$email')";
    
    if (mysqli_query($conexao, $sql)) {
        echo "Aluno incluído com sucesso!";
    } else {
        echo "Erro ao incluir aluno: " . mysqli_error($conexao);
    }
}

mysqli_close($conexao);
?>