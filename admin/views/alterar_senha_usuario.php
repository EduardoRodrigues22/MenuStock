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

    $senha = (string) ($_POST['senha'] ?? '');
    $confirmarSenha = (string) ($_POST['confirmar_senha'] ?? '');

    if ($senha !== $confirmarSenha) {
        flash('error', 'As senhas não coincidem.');
        redirect('alterar_senha_usuario.php?id=' . $id);
    }

    try {
        $usuarioModel->alterarSenha($id, $senha);
        flash('success', 'Senha alterada.');
        redirect('ver_usuarios.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('alterar_senha_usuario.php?id=' . $id);
    }
}

$pageTitle = 'Alterar senha';
require_once __DIR__ . '/../config/header_admin.php';
?>
<div class="centered-container">
    <section class="page-heading">
        <h1>Alterar senha</h1>
        <p><?= e($usuario['nome']) ?> - <?= e($usuario['email']) ?></p>
    </section>

    <form method="post" class="panel stack-form narrow-form" onsubmit="return validarSenhas(this);">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
        <label>
            Nova senha
            <input type="password" name="senha" required minlength="6">
        </label>
        <label>
            Confirmar nova senha
            <input type="password" name="confirmar_senha" required minlength="6">
        </label>
        <div class="form-actions">
            <a class="btn btn-ghost" href="ver_usuarios.php">Cancelar</a>
            <button class="btn btn-primary" type="submit">Alterar senha</button>
        </div>
    </form>
</div>

<script>
function validarSenhas(form) {
    if (form.senha.value !== form.confirmar_senha.value) {
        alert('As senhas novas não coincidem!');
        return false;
    }
    return true;
}
</script>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
