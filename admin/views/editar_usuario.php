<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

requireRole('admin');

$usuarioModel = new Usuario();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$usuario = $usuarioModel->buscarPorId($id);

if (!$usuario) {
    flash('error', 'Usuário não encontrado.');
    redirect('ver_usuarios.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    try {
        $usuarioModel->editar($id, $_POST);
        flash('success', 'Usuário atualizado.');
        redirect('ver_usuarios.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('editar_usuario.php?id=' . $id);
    }
}

$pageTitle = 'Editar usuário';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Editar usuário</h1>
    <p>Alterar o e-mail pode alterar automaticamente o tipo de acesso.</p>
</section>

<form method="post" class="panel stack-form narrow-form">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
    <label>
        Nome
        <input type="text" name="nome" required maxlength="100" value="<?= e($usuario['nome']) ?>">
    </label>
    <label>
        E-mail
        <input type="email" name="email" required maxlength="150" value="<?= e($usuario['email']) ?>">
    </label>
    <label>
        Telefone
        <input type="tel" name="telefone" maxlength="20" value="<?= e($usuario['telefone']) ?>">
    </label>
    <p class="muted">Tipo atual: <?= e($usuario['tipo']) ?></p>
    <div class="form-actions">
        <a class="btn btn-ghost" href="ver_usuarios.php">Cancelar</a>
        <button class="btn btn-primary" type="submit">Salvar alterações</button>
    </div>
</form>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
