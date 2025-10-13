<?php
$mensagem = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && $_GET['acao'] === 'buscar' && isset($_GET['codigo'])) {
    $arquivo = __DIR__ . '/perguntas.txt';
    $codigo = $_GET['codigo'];
    $pergunta = null;

    if (file_exists($arquivo)) {
        $linhas = file($arquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($linhas as $linha) {
            $dados = explode(';', $linha);
            if ($dados[0] === $codigo) {
                $pergunta = ['codigo' => $dados[0], 'tipo' => $dados[1], 'enunciado' => $dados[2]];
                if ($dados[1] === 'MULTIPLA') {
                    $pergunta['opcoes'] = explode('|', $dados[3]);
                    $pergunta['correta'] = $dados[4];
                } else {
                    $pergunta['resposta'] = $dados[3];
                }
                break;
            }
        }
    }

    header('Content-Type: application/json');
    if ($pergunta) {
        echo json_encode($pergunta);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pergunta não encontrada.']);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $arquivo = __DIR__ . '/perguntas.txt';
    $acao = $_POST['acao'] ?? 'cadastrar';

    if ($acao === 'alterar') {
        $codigo = $_POST['codigo'] ?? '';
        $enunciado = $_POST['enunciado'] ?? '';
        $tipo = $_POST['tipo'] ?? '';

        if (!$codigo || !$enunciado || !$tipo) {
            $mensagem = 'Dados insuficientes para alterar a pergunta.';
        } else {
            $linhas = file($arquivo, FILE_IGNORE_NEW_LINES);
            $encontrado = false;
            foreach ($linhas as $i => $linha) {
                if (strpos($linha, $codigo . ';') === 0) {
                    if ($tipo === 'MULTIPLA') {
                        $opcoes = [
                            $_POST['opcao1'] ?? '', $_POST['opcao2'] ?? '',
                            $_POST['opcao3'] ?? '', $_POST['opcao4'] ?? ''
                        ];
                        $correta = $_POST['correta'] ?? '';
                        $linhas[$i] = $codigo . ';MULTIPLA;' . $enunciado . ';' . implode('|', $opcoes) . ';' . $correta;
                    } else {
                        $resposta = $_POST['resposta'] ?? '';
                        $linhas[$i] = $codigo . ';TEXTO;' . $enunciado . ';' . $resposta;
                    }
                    $encontrado = true;
                    break;
                }
            }

            if ($encontrado) {
                file_put_contents($arquivo, implode("\n", $linhas) . "\n");
                $mensagem = 'Pergunta alterada com sucesso!';
            } else {
                $mensagem = 'Erro: Pergunta não encontrada para alteração.';
            }
        }
    } else {
        $tipo = $_POST['tipo'] ?? '';
        $enunciado = $_POST['enunciado'] ?? '';
        while (substr($enunciado, 0, 1) === ' ') $enunciado = substr($enunciado, 1);
        while (substr($enunciado, -1) === ' ') $enunciado = substr($enunciado, 0, -1);
        if ($tipo === 'MULTIPLA') {
            $opcoes = [
                (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao1'] ?? '')),
                (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao2'] ?? '')),
                (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao3'] ?? '')),
                (function($v){while(substr($v,0,1)===' ')$v=substr($v,1);while(substr($v,-1)===' ')$v=substr($v,0,-1);return $v;})(($_POST['opcao4'] ?? ''))
            ];
            $correta = $_POST['correta'] ?? '';
            if ($enunciado && $correta !== '' && !in_array('', $opcoes)) {
                $id = uniqid('pm_');
                $linha = $id . ';MULTIPLA;' . $enunciado . ';' . implode('|', $opcoes) . ';' . $correta . "\n";
                $fp = fopen($arquivo, 'a');
                if ($fp) {
                    fwrite($fp, $linha);
                    fclose($fp);
                    $mensagem = 'Pergunta de múltipla escolha cadastrada!';
                } else {
                    $mensagem = 'Erro ao salvar a pergunta. Verifique permissões do arquivo.';
                }
            } else {
                $mensagem = 'Preencha todos os campos da pergunta de múltipla escolha.';
            }
        } elseif ($tipo === 'TEXTO') {
        $resposta = $_POST['resposta'] ?? '';
        while (substr($resposta, 0, 1) === ' ') $resposta = substr($resposta, 1);
        while (substr($resposta, -1) === ' ') $resposta = substr($resposta, 0, -1);
            if ($enunciado && $resposta) {
                $id = uniqid('pt_');
                $linha = $id . ';TEXTO;' . $enunciado . ';' . $resposta . "\n";
                $fp = fopen($arquivo, 'a');
                if ($fp) {
                    fwrite($fp, $linha);
                    fclose($fp);
                    $mensagem = 'Pergunta de texto cadastrada!';
                } else {
                    $mensagem = 'Erro ao salvar a pergunta. Verifique permissões do arquivo.';
                }
            } else {
                $mensagem = 'Preencha todos os campos da pergunta de texto.';
            }
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['message'=>$mensagem]);
    exit;
}
