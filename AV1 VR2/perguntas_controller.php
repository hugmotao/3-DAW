                        <?php
        $mensagem = '';
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Database config from environment (no fallback to file)
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPass = getenv('DB_PASS') ?: '';
        $dbName = getenv('DB_DATABASE') ?: '';

        function send_json($data, $status = 200) {
            header('Content-Type: application/json');
            http_response_code($status);
            echo json_encode($data);
            exit;
        }

        if ($dbName === '') {
            send_json(['error' => 'Database not configured. Set DB_DATABASE environment variable.'], 500);
        }

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        if ($conn->connect_error) {
            send_json(['error' => 'Database connection failed: ' . $conn->connect_error], 500);
        }

        // Enforce POST-only: all actions (listar, buscar, cadastrar, alterar, deletar) are handled via POST only
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            send_json(['error' => 'Este endpoint aceita apenas requisições POST.'], 405);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $acao = $_POST['acao'] ?? 'cadastrar';

            if ($acao === 'cadastrar') {
                $tipo = $_POST['tipo'] ?? '';
                $enunciado = trim($_POST['enunciado'] ?? '');
                if ($tipo === 'MULTIPLA') {
                    $opcoes = [trim($_POST['opcao1'] ?? ''), trim($_POST['opcao2'] ?? ''), trim($_POST['opcao3'] ?? ''), trim($_POST['opcao4'] ?? '')];
                    $correta = $_POST['correta'] ?? '';
                    if ($enunciado && $correta !== '' && !in_array('', $opcoes)) {
                        $codigo = uniqid('pm_');
                        $opcoes_str = implode('|', $opcoes);
                        $stmt = $conn->prepare('INSERT INTO Perguntas (codigo, tipo, enunciado, opcoes, correta) VALUES (?, ?, ?, ?, ?)');
                        if ($stmt) {
                            $stmt->bind_param('sssss', $codigo, $tipo, $enunciado, $opcoes_str, $correta);
                            if ($stmt->execute()) {
                                $mensagem = 'Pergunta de múltipla escolha cadastrada!';
                            } else {
                                $mensagem = 'Erro ao salvar no banco: ' . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $mensagem = 'Erro ao preparar instrução no banco.';
                        }
                    } else {
                        $mensagem = 'Preencha todos os campos da pergunta de múltipla escolha.';
                    }
                } elseif ($tipo === 'TEXTO') {
                    $resposta = trim($_POST['resposta'] ?? '');
                    if ($enunciado && $resposta) {
                        $codigo = uniqid('pt_');
                        $stmt = $conn->prepare('INSERT INTO Perguntas (codigo, tipo, enunciado, resposta) VALUES (?, ?, ?, ?)');
                        if ($stmt) {
                            $stmt->bind_param('ssss', $codigo, $tipo, $enunciado, $resposta);
                            if ($stmt->execute()) {
                                $mensagem = 'Pergunta de texto cadastrada!';
                            } else {
                                $mensagem = 'Erro ao salvar no banco: ' . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $mensagem = 'Erro ao preparar instrução no banco.';
                        }
                    } else {
                        $mensagem = 'Preencha todos os campos da pergunta de texto.';
                    }
                } else {
                    $mensagem = 'Tipo de pergunta inválido.';
                }
            } elseif ($acao === 'alterar') {
                $codigo = $_POST['codigo'] ?? '';
                $tipo = $_POST['tipo'] ?? '';
                $enunciado = $_POST['enunciado'] ?? '';
                if (!$codigo || !$tipo || !$enunciado) {
                    $mensagem = 'Dados insuficientes para alterar a pergunta.';
                } else {
                    if ($tipo === 'MULTIPLA') {
                        $opcoes = [trim($_POST['opcao1'] ?? ''), trim($_POST['opcao2'] ?? ''), trim($_POST['opcao3'] ?? ''), trim($_POST['opcao4'] ?? '')];
                        $correta = $_POST['correta'] ?? '';
                        $opcoes_str = implode('|', $opcoes);
                        $stmt = $conn->prepare('UPDATE Perguntas SET tipo=?, enunciado=?, opcoes=?, correta=?, resposta=NULL WHERE codigo=?');
                        if ($stmt) {
                            $stmt->bind_param('sssss', $tipo, $enunciado, $opcoes_str, $correta, $codigo);
                            if ($stmt->execute()) {
                                $mensagem = 'Pergunta alterada com sucesso!';
                            } else {
                                $mensagem = 'Erro ao alterar no banco: ' . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $mensagem = 'Erro ao preparar instrução de alteração.';
                        }
                    } else {
                        $resposta = $_POST['resposta'] ?? '';
                        $stmt = $conn->prepare('UPDATE Perguntas SET tipo=?, enunciado=?, resposta=?, opcoes=NULL, correta=NULL WHERE codigo=?');
                        if ($stmt) {
                            $stmt->bind_param('ssss', $tipo, $enunciado, $resposta, $codigo);
                            if ($stmt->execute()) {
                                $mensagem = 'Pergunta alterada com sucesso!';
                            } else {
                                $mensagem = 'Erro ao alterar no banco: ' . $stmt->error;
                            }
                            $stmt->close();
                        } else {
                            $mensagem = 'Erro ao preparar instrução de alteração.';
                        }
                    }
                }
            } elseif ($acao === 'deletar') {
                $codigo = $_POST['codigo'] ?? '';
                if (!$codigo) {
                    $mensagem = 'Código não informado para exclusão.';
                } else {
                    $stmt = $conn->prepare('DELETE FROM Perguntas WHERE codigo = ?');
                    if ($stmt) {
                        $stmt->bind_param('s', $codigo);
                        if ($stmt->execute()) {
                            if ($stmt->affected_rows > 0) {
                                $mensagem = 'Pergunta removida com sucesso!';
                            } else {
                                $mensagem = 'Pergunta não encontrada para remoção.';
                            }
                        } else {
                            $mensagem = 'Erro ao excluir no banco: ' . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $mensagem = 'Erro ao preparar instrução de exclusão.';
                    }
                }
            }

            if ($isAjax) {
                send_json(['message' => $mensagem]);
            } else {
                echo $mensagem;
            }
        }

        $conn->close();

