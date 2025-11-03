<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $email = $_POST['email'];
    
    $sql = "UPDATE alunos SET nome='$nome', matricula='$matricula', email='$email' WHERE id=$id";
    
    if (mysqli_query($conexao, $sql)) {
        echo "Dados do aluno atualizados com sucesso!";
    } else {
        echo "Erro ao atualizar dados: " . mysqli_error($conexao);
    }
}

mysqli_close($conexao);
?>