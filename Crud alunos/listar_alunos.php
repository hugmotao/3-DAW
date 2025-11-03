<!DOCTYPE html>
<html>
<head>
    <title>Listar Alunos</title>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Lista de Alunos</h2>
    <?php
    include 'conexao.php';
    
    $sql = "SELECT * FROM alunos";
    $resultado = mysqli_query($conexao, $sql);
    
    if (mysqli_num_rows($resultado) > 0) {
        echo "<table border='1'>
        <tr>
            <th>Nome</th>
            <th>Matrícula</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>";
        
        while($row = mysqli_fetch_assoc($resultado)) {
            echo "<tr>";
            echo "<td>" . $row['nome'] . "</td>";
            echo "<td>" . $row['matricula'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>
                <a href='alterar_aluno.html?id=" . $row['id'] . "'>Alterar</a> | 
                <a href='excluir_aluno.php?id=" . $row['id'] . "'>Excluir</a>
            </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum aluno cadastrado";
    }
    
    mysqli_close($conexao);
    ?>
    <br>
    <a href="index.html">Voltar ao Menu Principal</a>
</body>
</html>