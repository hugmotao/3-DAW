                                                                <?php
                                                                $arquivo = __DIR__ . '/perguntas.txt';
                                                                $id = $_GET['id'] ?? '';
                                                                $mensagem = '';
                                                                if ($id && file_exists($arquivo)) {
                                                                    $novas = [];
                                                                    $fp = fopen($arquivo, 'r');
                                                                    if ($fp) {
                                                                        while (($linha = fgets($fp)) !== false) {
                                                                            $linha = trim($linha);
                                                                            if ($linha === '') continue;
                                                                            $dados = explode(';', $linha);
                                                                            if ($dados[0] !== $id) {
                                                                                $novas[] = $linha;
                                                                            }
                                                                        }
                                                                        fclose($fp);
                                                                        $fpw = fopen($arquivo, 'w');
                                                                        if ($fpw) {
                                                                            foreach ($novas as $linha) {
                                                                                fwrite($fpw, $linha . "\n");
                                                                            }
                                                                            fclose($fpw);
                                                                            $mensagem = 'Pergunta excluída com sucesso!';
                                                                        } else {
                                                                            $mensagem = 'Erro ao salvar alterações.';
                                                                        }
                                                                    } else {
                                                                        $mensagem = 'Erro ao abrir o arquivo.';
                                                                    }
                                                                }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Excluir Pergunta AV1</title>
    <meta http-equiv="refresh" content="2;url=listar_perguntas_av1.php">
</head>
<body>
    <h2><?php echo htmlspecialchars($mensagem); ?></h2>
    <a href="listar_perguntas_av1.php">Voltar</a>
</body>
</html>
