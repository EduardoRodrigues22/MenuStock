<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

requireRole(['admin', 'garcom']);

$pedidoModel = new Pedido();
$statusFiltro = (string) ($_GET['status'] ?? '');
$pedidos = $pedidoModel->listar($statusFiltro !== '' ? $statusFiltro : null, true);
$pageTitle = 'Pedidos';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Pedidos</h1>
    <p>Atualize o status operacional dos pedidos realizados hoje.</p>
</section>

<section class="toolbar section-head">
    <form method="get" class="filters">
        <label style="min-width: 140px;">
            Status
            <select name="status">
                <option value="">Todos</option>
                <?php foreach (Pedido::STATUS as $status): ?>
                    <option value="<?= e($status) ?>" <?= $statusFiltro === $status ? 'selected' : '' ?>><?= statusLabel($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="inline-form">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-ghost" href="gerenciar_pedidos.php">Limpar</a>
        </div>
    </form>
</section>

<section class="stack">
    <?php foreach ($pedidos as $i => $pedido): ?>
        <?php $itens = $pedidoModel->itens((int) $pedido['id']); ?>
        <article class="panel order-card">
            <div class="order-head">
                <div>
                    <h2>Pedido #<?= count($pedidos) - $i ?></h2>
                    <span class="muted">
                        <?php if (!empty($pedido['mesa'])): ?>
                            <strong>Mesa <?= (int) $pedido['mesa'] ?></strong>
                        <?php else: ?>
                            <?= e($pedido['cliente']) ?> - <?= e($pedido['telefone']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <strong><?= formatMoney($pedido['total']) ?></strong>
                <form method="post" action="../controllers/atualizar_status_pedido.php" class="inline-form pedido-status-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                    <select name="status" <?= in_array($pedido['status'], ['cancelado', 'entregue'], true) ? 'disabled' : '' ?>>
                        <?php foreach (Pedido::STATUS as $status): ?>
                            <option value="<?= e($status) ?>" <?= $pedido['status'] === $status ? 'selected' : '' ?>><?= statusLabel($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-secondary" type="submit" <?= in_array($pedido['status'], ['cancelado', 'entregue'], true) ? 'disabled' : '' ?>>Salvar</button>
                </form>
            </div>
            <?php if ($pedido['obs_geral']): ?>
                <p><strong>Obs.:</strong> <?= e($pedido['obs_geral']) ?></p>
            <?php endif; ?>
            <?php require __DIR__ . '/partials/_pedido_itens_table.php'; ?>
        </article>
    <?php endforeach; ?>
    <?php if (!$pedidos): ?>
        <div class="empty-state">Nenhum pedido de hoje encontrado.</div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
