<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM alunos WHERE id=$id";
    
    if (mysqli_query($conexao, $sql)) {
        echo "Aluno excluído com sucesso!";
    } else {
        echo "Erro ao excluir aluno: " . mysqli_error($conexao);
    }
}

mysqli_close($conexao);
?>