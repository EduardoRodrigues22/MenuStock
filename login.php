<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Usuario.php';

$usuarioModel = new Usuario();

if (isLoggedIn() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectByRole();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $acao = (string) ($_POST['acao'] ?? 'login');

    try {
        if ($acao === 'cadastro') {
            $id = $usuarioModel->cadastrar($_POST);
            $usuario = $usuarioModel->buscarPorId($id);
            if ($usuario) {
                loginUser($usuario);
            }
            flash('success', 'Cadastro realizado com sucesso.');
            redirectByRole();
        }

        $usuario = $usuarioModel->autenticar((string) ($_POST['email'] ?? ''), (string) ($_POST['senha'] ?? ''));

        if (!$usuario) {
            flash('error', 'E-mail ou senha inválidos.');
            redirect('login.php');
        }

        loginUser($usuario);
        flash('success', 'Login realizado com sucesso.');
        redirectByRole();
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('login.php');
    }
}

$pageTitle = 'Entrar';
require_once __DIR__ . '/admin/config/public_header.php';
?>
<section class="auth-grid">
    <article class="panel">
        <h1>Entrar</h1>
        <form method="post" class="stack-form">
            <?= csrfField() ?>
            <input type="hidden" name="acao" value="login">
            <label>
                E-mail
                <input type="email" name="email" required autocomplete="email">
            </label>
            <label>
                Senha
                <input type="password" name="senha" required autocomplete="current-password">
            </label>
            <button class="btn btn-primary" type="submit">Entrar</button>
        </form>
    </article>

    <article class="panel">
        <h2>Criar conta</h2>
        <form method="post" class="stack-form">
            <?= csrfField() ?>
            <input type="hidden" name="acao" value="cadastro">
            <label>
                Nome
                <input type="text" name="nome" required autocomplete="name">
            </label>
            <label>
                E-mail
                <input type="email" name="email" required autocomplete="email">
            </label>
            <label>
                Telefone
                <input type="tel" name="telefone" autocomplete="tel">
            </label>
            <label>
                Senha
                <input type="password" name="senha" required minlength="6" autocomplete="new-password">
            </label>
            <button class="btn btn-secondary" type="submit">Cadastrar</button>
        </form>
    </article>
</section>
<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
