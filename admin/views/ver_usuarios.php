<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

requireRole('admin');

$usuarioModel = new Usuario();
$busca = trim((string) ($_GET['q'] ?? ''));
$usuarios = $usuarioModel->listar($busca ?: null);
$pageTitle = 'Usuários';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Usuários</h1>
    <p>Gerencie clientes, administradores e garçons.</p>
</section>

<section class="toolbar section-head">
    <form method="get" class="filters">
        <label>
            Buscar
            <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Nome ou e-mail">
        </label>
        <button class="btn btn-primary" type="submit">Filtrar</button>
        <a class="btn btn-ghost" href="ver_usuarios.php">Limpar</a>
    </form>
    <a class="btn btn-secondary" href="cadastrar_usuario.php">Novo usuário</a>
</section>

<div class="panel table-wrap">
    <table class="usuarios-table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Tipo</th>
                <th>Criado em</th>
                <th style="width: 1%; white-space: nowrap;">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= e($usuario['nome']) ?></td>
                    <td><?= e($usuario['email']) ?></td>
                    <td><?= e($usuario['telefone']) ?></td>
                    <td><span class="status"><?= e($usuario['tipo']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime((string) $usuario['created_at'])) ?></td>
                    <td style="width: 1%; white-space: nowrap;">
                        <div class="inline-form" style="flex-wrap: nowrap;">
                            <a class="btn btn-ghost" href="editar_usuario.php?id=<?= (int) $usuario['id'] ?>">Editar</a>
                            <a class="btn btn-ghost" href="alterar_senha_usuario.php?id=<?= (int) $usuario['id'] ?>">Senha</a>
                            <?php if ((int) $usuario['id'] !== currentUserId()): ?>
                                <form method="post" action="../controllers/deletar_usuario.php" onsubmit="return confirm('Excluir este usuário?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$usuarios): ?>
                <tr><td colspan="6">Nenhum usuário encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
