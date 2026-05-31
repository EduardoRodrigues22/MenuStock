<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Pedido.php';

requireRole('user', 'login.php');

$pedidoModel = new Pedido();
$pedidos = $pedidoModel->listarPorUsuario(currentUserId() ?? 0);
$pageTitle = 'Meus pedidos';
require_once __DIR__ . '/admin/config/public_header.php';
?>
<section class="page-heading">
    <h1>Meus pedidos</h1>
    <p>Acompanhe o status de cada pedido confirmado.</p>
</section>

<?php if (!$pedidos): ?>
    <div class="empty-state">
        Você ainda não fez pedidos.
        <a class="btn btn-primary" href="index.php">Ver cardápio</a>
    </div>
<?php endif; ?>

<section class="stack">
    <?php $contadorPedido = count($pedidos); ?>
    <?php foreach ($pedidos as $pedido): ?>
        <?php $itens = $pedidoModel->itens((int) $pedido['id']); ?>
        <article class="panel order-card">
            <div class="order-head">
                <div>
                    <h2>Pedido #<?= $contadorPedido ?></h2>
                    <span class="muted"><?= date('d/m/Y H:i', strtotime((string) $pedido['created_at'])) ?></span>
                </div>
                <span class="status status-<?= e($pedido['status']) ?>"><?= statusLabel($pedido['status']) ?></span>
                <strong><?= formatMoney($pedido['total']) ?></strong>
            </div>
            <ol class="status-steps">
                <?php foreach (['recebido', 'preparo', 'pronto', 'entregue'] as $status): ?>
                    <li class="<?= $pedido['status'] === $status ? 'active' : '' ?>"><?= statusLabel($status) ?></li>
                <?php endforeach; ?>
            </ol>
            <?php if ($pedido['obs_geral']): ?>
                <p><strong>Obs.:</strong> <?= e($pedido['obs_geral']) ?></p>
            <?php endif; ?>
            <?php require __DIR__ . '/admin/views/partials/_pedido_itens_table.php'; ?>
        </article>
        <?php $contadorPedido--; ?>
    <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
