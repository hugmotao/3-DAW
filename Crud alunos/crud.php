<?php
define('DATA_FILE', 'disciplinas.csv');

function carregarDisciplinas() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $disciplinas = [];
    $handle = fopen(DATA_FILE, 'r');
    while (($dados = fgetcsv($handle)) !== FALSE) {
        $disciplinas[$dados[0]] = ['id' => $dados[0], 'nome' => $dados[1], 'codigo' => $dados[2], 'carga_horaria' => $dados[3]];
    }
    fclose($handle);
    return $disciplinas;
}

function salvarDisciplinas($disciplinas) {
    $handle = fopen(DATA_FILE, 'w');
    foreach ($disciplinas as $disciplina) {
        fputcsv($handle, $disciplina);
    }
    fclose($handle);
}

function proximoId($disciplinas) {
    return count($disciplinas) > 0 ? max(array_keys($disciplinas)) + 1 : 1;
}

$acao = $_GET['acao'] ?? 'listar'; 
$id = $_GET['id'] ?? null;
$mensagem = '';
$erros = [];

$disciplinas = carregarDisciplinas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $carga_horaria = trim($_POST['carga_horaria'] ?? '');
    $id_form = $_POST['id'] ?? null;

   
    if (empty($nome)) $erros[] = 'O nome é obrigatório.';
    if (empty($codigo)) $erros[] = 'O código é obrigatório.';
    if (!is_numeric($carga_horaria) || $carga_horaria <= 0) $erros[] = 'A carga horária deve ser um número positivo.';

    if (count($erros) === 0) {
        if ($acao === 'criar') {
            $novoId = proximoId($disciplinas);
            $disciplinas[$novoId] = ['id' => $novoId, 'nome' => $nome, 'codigo' => $codigo, 'carga_horaria' => $carga_horaria];
            $mensagem = 'Disciplina adicionada com sucesso!';
        } elseif ($acao === 'editar' && isset($disciplinas[$id_form])) {
            $disciplinas[$id_form] = ['id' => $id_form, 'nome' => $nome, 'codigo' => $codigo, 'carga_horaria' => $carga_horaria];
            $mensagem = 'Disciplina atualizada com sucesso!';
        }
        salvarDisciplinas($disciplinas);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?mensagem=' . urlencode($mensagem));
        exit;
    }
   
}

if ($acao === 'excluir' && $id && isset($disciplinas[$id])) {
    unset($disciplinas[$id]);
    salvarDisciplinas($disciplinas);
    header('Location: ' . $_SERVER['PHP_SELF'] . '?mensagem=' . urlencode('Disciplina excluída com sucesso!'));
    exit;
}

if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Disciplinas</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f5f5f5; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            background: #fff; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            max-width: 900px; 
            margin: 0 auto;
        }
        h1 { 
            margin-top: 0; 
            color: #2c3e50; 
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a { 
            color: #3498db; 
            text-decoration: none; 
        }
        a:hover { 
            text-decoration: underline; 
        }
        .acoes a { 
            margin-right: 10px; 
        }
        .acoes a.excluir:hover { 
            color: #e74c3c; 
        }
        .botao { 
            display: inline-block; 
            padding: 8px 15px; 
            background: #3498db; 
            color: white; 
            border-radius: 4px; 
            text-decoration: none; 
            border: none; 
            cursor: pointer; 
            font-size: 14px;
        }
        .botao:hover { 
            background: #2980b9; 
        }
        .botao.cancelar { 
            background: #95a5a6; 
        }
        .botao.cancelar:hover { 
            background: #7f8c8d; 
        }
        .botao.novo {
            background: #27ae60;
            margin-bottom: 15px;
        }
        .botao.novo:hover {
            background: #219653;
        }
        .mensagem, .erros { 
            padding: 10px; 
            margin-bottom: 15px; 
            border-radius: 4px; 
        }
        .mensagem { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .erros { 
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
        .grupo-form { 
            margin-bottom: 15px; 
        }
        .grupo-form label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold;
        }
        .grupo-form input { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        .grupo-form input:focus {
            border-color: #3498db;
            outline: none;
        }
        .acoes-form { 
            margin-top: 20px; 
        }
        .sem-dados {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
        }
        .rodape {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #7f8c8d;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestão de Disciplinas</h1>

        <?php if ($mensagem): ?>
            <div class="mensagem"><?= $mensagem ?></div>
        <?php endif; ?>
        
        <?php if (!empty($erros)): ?>
            <div class="erros">
                <strong>Erros encontrados:</strong>
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= $erro ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php?>

        <?php if ($acao === 'criar' || $acao === 'editar'): ?>
            <?php
            
            $disciplina_atual = ['id' => '', 'nome' => '', 'codigo' => '', 'carga_horaria' => ''];
            if ($acao === 'editar' && $id && isset($disciplinas[$id])) {
                $disciplina_atual = $disciplinas[$id];
            }
            
            if (!empty($erros)) {
                $disciplina_atual['nome'] = $_POST['nome'] ?? '';
                $disciplina_atual['codigo'] = $_POST['codigo'] ?? '';
                $disciplina_atual['carga_horaria'] = $_POST['carga_horaria'] ?? '';
            }
            ?>
            <h2><?= $acao === 'criar' ? 'Adicionar Nova Disciplina' : 'Editar Disciplina' ?></h2>
            <form action="?acao=<?= $acao ?>" method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($disciplina_atual['id']) ?>">
                <div class="grupo-form">
                    <label for="nome">Nome da Disciplina:</label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($disciplina_atual['nome']) ?>" required>
                </div>
                <div class="grupo-form">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($disciplina_atual['codigo']) ?>" required>
                </div>
                <div class="grupo-form">
                    <label for="carga_horaria">Carga Horária:</label>
                    <input type="number" id="carga_horaria" name="carga_horaria" value="<?= htmlspecialchars($disciplina_atual['carga_horaria']) ?>" required min="1">
                </div>
                <div class="acoes-form">
                    <button type="submit" class="botao">Salvar</button>
                    <a href="<?= $_SERVER['PHP_SELF'] ?>" class="botao cancelar">Cancelar</a>
                </div>
            </form>

        <?php else: ?>

            <a href="?acao=criar" class="botao novo">Nova Disciplina</a>
            
            <?php if (count($disciplinas) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>Carga Horária</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disciplinas as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['nome']) ?></td>
                            <td><?= htmlspecialchars($d['codigo']) ?></td>
                            <td><?= htmlspecialchars($d['carga_horaria']) ?>h</td>
                            <td class="acoes">
                                <a href="?acao=editar&id=<?= $d['id'] ?>">Editar</a>
                                <a href="?acao=excluir&id=<?= $d['id'] ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir esta disciplina?');">Excluir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="sem-dados">
                    <p>Nenhuma disciplina cadastrada. Clique no botão acima para adicionar a primeira.</p>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
        
        <div class="rodape">
            Sistema de Gestão de Disciplinas &copy; <?= date('Y') ?>
        </div>
    </div>
</body>
</html>