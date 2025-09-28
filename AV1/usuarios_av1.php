<?php
$arquivo = __DIR__ . '/usuarios.txt';
$mensagem = '';

function ler_usuarios() {
    $arquivo = __DIR__ . '/usuarios.txt';
    $usuarios = [];
    if (file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        if (!$fp) {
            global $mensagem;
            $mensagem = 'Erro ao abrir o arquivo de usuários.';
            return [];
        }
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            $usuarios[] = [
                'id' => $dados[0],
                'nome' => $dados[1],
                'email' => $dados[2]
            ];
        }
        fclose($fp);
    }
    return $usuarios;
}

// Mensagens de erro centralizadas
$erros = [
    'obrigatorio' => 'Nome, email e senha são obrigatórios.',
    'email_invalido' => 'Email inválido.',
    'email_existente' => 'Email já cadastrado.',
    'senha_fraca' => 'A senha deve ter pelo menos 6 caracteres.',
    'nome_invalido' => 'O nome deve ter pelo menos 3 caracteres e conter apenas letras e espaços.',
    'erro_abrir' => 'Erro ao abrir o arquivo para salvar usuário.',
    'erro_escrever' => 'Erro ao escrever no arquivo de usuários.'
];

function validar_nome($nome) {
    return preg_match('/^[A-Za-zÀ-ÿ ]{3,}$/', $nome);
}

function validar_senha($senha) {
    return strlen($senha) >= 6;
}

function adicionar_usuario($nome, $email) {
    global $arquivo, $erros;
    $senha = $_POST['senha'] ?? '';
    if (empty($nome) || empty($email) || empty($senha)) {
        return $erros['obrigatorio'];
    }
    if (!validar_nome($nome)) {
        return $erros['nome_invalido'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $erros['email_invalido'];
    }
    if (!validar_senha($senha)) {
        return $erros['senha_fraca'];
    }
    $usuarios = ler_usuarios();
    foreach ($usuarios as $u) {
        if ($u['email'] === $email) {
            return $erros['email_existente'];
        }
    }
    $id = uniqid('u_');
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $linha = $id . ';' . $nome . ';' . $email . ';' . $senha_hash . "\n";
    $fp = fopen($arquivo, 'a');
    if (!$fp) {
        return $erros['erro_abrir'];
    }
    if (fwrite($fp, $linha) === false) {
        fclose($fp);
        return $erros['erro_escrever'];
    }
    fclose($fp);
    return true;
function autenticar_usuario($email, $senha) {
    global $arquivo;
    if (file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        if ($fp) {
            while (($linha = fgets($fp)) !== false) {
                $linha = trim($linha);
                if ($linha === '') continue;
                $dados = explode(';', $linha);
                if (count($dados) >= 4 && $dados[2] === $email && password_verify($senha, $dados[3])) {
                    fclose($fp);
                    return true;
                }
            }
            fclose($fp);
        }
    }
    return false;
}
}

function editar_usuario($id, $nome, $email) {
    global $arquivo;
    if (empty($nome) || empty($email)) {
        return 'Nome e email são obrigatórios.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email inválido.';
    }
    $usuarios = ler_usuarios();
    foreach ($usuarios as $u) {
        if ($u['email'] === $email && $u['id'] !== $id) {
            return 'Email já cadastrado.';
        }
    }
    $novas = [];
    if (file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        if (!$fp) {
            return 'Erro ao abrir o arquivo para editar.';
        }
        while (($linha = fgets($fp)) !== false) {
            $linha = trim($linha);
            if ($linha === '') continue;
            $dados = explode(';', $linha);
            if ($dados[0] === $id) {
                $linha = $id . ';' . $nome . ';' . $email;
            }
            $novas[] = $linha;
        }
        fclose($fp);
        $fpw = fopen($arquivo, 'w');
        if (!$fpw) {
            return 'Erro ao abrir o arquivo para salvar edição.';
        }
        foreach ($novas as $linha) {
            if (fwrite($fpw, $linha . "\n") === false) {
                fclose($fpw);
                return 'Erro ao escrever edição no arquivo.';
            }
        }
        fclose($fpw);
        return true;
    }
    return 'Erro ao editar usuário.';
}

function excluir_usuario($id) {
    global $arquivo;
    $novas = [];
    if (file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        if (!$fp) {
            return false;
        }
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
        if (!$fpw) {
            return false;
        }
        foreach ($novas as $linha) {
            if (fwrite($fpw, $linha . "\n") === false) {
                fclose($fpw);
                return false;
            }
        }
        fclose($fpw);
        return true;
    }
    return false;
}

function buscar_usuario($id) {
    global $arquivo;
    if (file_exists($arquivo)) {
        $fp = fopen($arquivo, 'r');
        if ($fp) {
            while (($linha = fgets($fp)) !== false) {
                $linha = trim($linha);
                if ($linha === '') continue;
                $dados = explode(';', $linha);
                if ($dados[0] === $id) {
                    fclose($fp);
                    return [
                        'id' => $dados[0],
                        'nome' => $dados[1],
                        'email' => $dados[2]
                    ];
                }
            }
            fclose($fp);
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao']) && $_POST['acao'] === 'cadastrar') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $res = adicionar_usuario($nome, $email);
        if ($res === true) {
            $mensagem = 'Usuário cadastrado com sucesso!';
        } else {
            $mensagem = $res;
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'login') {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        if (autenticar_usuario($email, $senha)) {
            $mensagem = 'Login realizado com sucesso!';
        } else {
            $mensagem = 'Email ou senha incorretos.';
        }
    } elseif (isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
        $id = $_POST['id'] ?? '';
        if (excluir_usuario($id)) {
            $mensagem = 'Usuário excluído.';
        } else {
            $mensagem = 'Erro ao excluir usuário.';
        }
    }
}

$usuarios = ler_usuarios();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usuários AV1</title>
</head>
<body>
    <?php if (isset($_SESSION['usuario_id'])): ?>
        <p>Bem-vindo, <b><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></b>! (<i><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></i>)</p>
        <form method="post" style="display:inline;">
            <input type="hidden" name="acao" value="logout">
            <button type="submit">Logout</button>
        </form>
    <?php else: ?>
        <h2>Login</h2>
        <form method="post">
            <input type="hidden" name="acao" value="login">
            Email: <input type="email" name="email">
            Senha: <input type="password" name="senha">
            <button type="submit">Entrar</button>
        </form>
    <?php endif; ?>
    <h2>Cadastrar Usuário</h2>
    <form method="post">
        <input type="hidden" name="acao" value="cadastrar">
        Nome: <input type="text" name="nome">
        Email: <input type="email" name="email">
        Senha: <input type="password" name="senha">
        <button type="submit">Cadastrar</button>
    </form>
    <p style="color:blue;"><b><?php echo htmlspecialchars($mensagem); ?></b></p>
    <h2>Usuários Cadastrados</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Ações</th>
        </tr>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?php echo htmlspecialchars($u['id']); ?></td>
            <td><?php echo htmlspecialchars($u['nome']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="acao" value="excluir">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($u['id']); ?>">
                    <button type="submit" onclick="return confirm('Excluir usuário?');">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>