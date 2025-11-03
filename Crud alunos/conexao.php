<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$bancodedados = "escola";

$conexao = mysqli_connect($servidor, $usuario, $senha, $bancodedados);

if (!$conexao) {
    die("Conexão falhou: " . mysqli_connect_error());
}
?>