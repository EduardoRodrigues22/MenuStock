<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Prato.php';
require_once __DIR__ . '/../models/Categoria.php';

requireRole(['admin', 'garcom']);

$pratoModel = new Prato();
$categoriaModel = new Categoria();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'toggle') {
    verifyCsrfOrFail();
    if (!canManagePratoDisponibilidade()) {
        flash('error', 'Você não tem permissão para alterar a disponibilidade de pratos.');
        redirect('gerenciar_pratos.php');
    }
    $pratoModel->alterarDisponibilidade((int) ($_POST['id'] ?? 0), (int) ($_POST['disponivel'] ?? 0) === 1);
    flash('success', 'Disponibilidade atualizada.');
    redirect('gerenciar_pratos.php');
}

$busca = trim((string) ($_GET['q'] ?? ''));
$categoriaId = isset($_GET['categoria']) && $_GET['categoria'] !== '' ? (int) $_GET['categoria'] : null;
$categorias = $categoriaModel->listar();
$pratos = $pratoModel->listar($busca ?: null, $categoriaId);
$pageTitle = 'Pratos';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Pratos</h1>
    <p>Gerencie cardápio, preços, imagens e disponibilidade.</p>
</section>

<section class="toolbar section-head">
    <form class="filters" method="get">
        <label style="min-width: 150px;">
            Buscar
            <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Nome ou categoria">
        </label>
        <label style="min-width: 130px;">
            Categoria
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                        <?= e($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="inline-form">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-ghost" href="gerenciar_pratos.php">Limpar</a>
        </div>
    </form>
    <?php if (hasRole('admin')): ?>
        <a class="btn btn-secondary" href="cadastrar_prato.php">Novo prato</a>
    <?php endif; ?>
</section>

<div class="panel table-wrap">
    <table class="pratos-table <?= !hasRole('admin') ? 'table-garcom' : '' ?>">
        <thead>
            <tr>
                <th>Prato</th>
                <?php if (hasRole('admin')): ?>
                    <th>Categoria</th>
                <?php endif; ?>
                <th>Preço</th>
                <th>Tempo</th>
                <?php if (hasRole('admin')): ?>
                    <th>Ações</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pratos as $prato): ?>
                <tr>
                    <td>
                        <div class="table-title">
                            <img src="../public/imagens/<?= e($prato['imagem'] ?: 'placeholder.svg') ?>" onerror="this.src='../public/imagens/placeholder.svg'" alt="" class="thumb">
                            <div>
                                <strong><?= e($prato['nome']) ?></strong>
                                <?php if (hasRole('admin')): ?>
                                    <span class="muted"><?= e(strlen((string) $prato['descricao']) > 80 ? substr((string) $prato['descricao'], 0, 80) . '...' : (string) $prato['descricao']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <?php if (hasRole('admin')): ?>
                        <td><?= e($prato['categoria']) ?></td>
                    <?php endif; ?>
                    <td>
                        <?php if ($prato['preco_promocional'] !== null): ?>
                            <span class="old-price"><?= formatMoney($prato['preco']) ?></span><br>
                            <strong><?= formatMoney($prato['preco_promocional']) ?></strong>
                        <?php else: ?>
                            <?= formatMoney($prato['preco']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) $prato['tempo_preparo'] ?> min</td>
                    <?php if (hasRole('admin')): ?>
                        <td>
                            <div class="prato-actions">
                                <?php if (canManagePratoDisponibilidade()): ?>
                                    <form method="post" class="inline-form">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="acao" value="toggle">
                                        <input type="hidden" name="id" value="<?= (int) $prato['id'] ?>">
                                        <input type="hidden" name="disponivel" value="<?= (int) $prato['disponivel'] === 1 ? 0 : 1 ?>">
                                        <button class="btn <?= (int) $prato['disponivel'] === 1 ? 'btn-secondary' : 'btn-ghost' ?>" type="submit">
                                            <?= (int) $prato['disponivel'] === 1 ? 'Ativo' : 'Inativo' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="status <?= (int) $prato['disponivel'] === 1 ? 'status-pronto' : 'status-cancelado' ?>">
                                        <?= (int) $prato['disponivel'] === 1 ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                <?php endif; ?>

                                <?php if (hasRole('admin')): ?>
                                    <a class="btn btn-ghost" href="editar_prato.php?id=<?= (int) $prato['id'] ?>">Editar</a>
                                    <form method="post" action="../controllers/deletar_prato.php" onsubmit="return confirm('Excluir este prato?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= (int) $prato['id'] ?>">
                                        <button class="btn btn-danger" type="submit">Excluir</button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">Operacional</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pratos): ?>
                <tr><td colspan="<?= hasRole('admin') ? 5 : 3 ?>">Nenhum prato encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
