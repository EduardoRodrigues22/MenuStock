<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

requireRole('admin');

$usuarioModel = new Usuario();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    try {
        $usuarioModel->cadastrar($_POST);
        flash('success', 'Usuário cadastrado. O tipo foi definido pelo domínio do e-mail.');
        redirect('ver_usuarios.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('cadastrar_usuario.php');
    }
}

$pageTitle = 'Novo usuário';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Novo usuário</h1>
    <p>O tipo de acesso é calculado automaticamente pelo domínio do e-mail.</p>
</section>

<form method="post" class="panel stack-form narrow-form">
    <?= csrfField() ?>
    <label>
        Nome
        <input type="text" name="nome" required maxlength="100">
    </label>
    <label>
        E-mail
        <input type="email" name="email" required maxlength="150">
    </label>
    <label>
        Telefone
        <input type="tel" name="telefone" maxlength="20">
    </label>
    <label>
        Senha
        <input type="password" name="senha" required minlength="6">
    </label>
    <div class="form-actions">
        <a class="btn btn-ghost" href="ver_usuarios.php">Cancelar</a>
        <button class="btn btn-primary" type="submit">Salvar usuário</button>
    </div>
</form>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
