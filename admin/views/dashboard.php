<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Reserva.php';

requireRole(['admin', 'garcom']);

$pdo = getConnection();
$pedidoModel = new Pedido();
$reservaModel = new Reserva();

$pedidosAbertos = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status IN ('recebido','preparo','pronto')")->fetchColumn();
$reservasHoje = (int) $pdo->query('SELECT COUNT(*) FROM reservas WHERE data = CURDATE() AND status <> "cancelada"')->fetchColumn();
$pratosIndisponiveis = (int) $pdo->query('SELECT COUNT(*) FROM pratos WHERE disponivel = 0')->fetchColumn();
$promocoesAtivas = (int) $pdo->query('SELECT COUNT(*) FROM vw_promocoes_ativas')->fetchColumn();
$mostrarReceita = hasRole('admin');
$faturamentoHoje = $mostrarReceita
    ? (float) $pdo->query('SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE DATE(created_at) = CURDATE() AND status <> "cancelado"')->fetchColumn()
    : 0.0;

$ultimosPedidos = array_slice($pedidoModel->listar(), 0, 6);
$reservasDoDia = $reservaModel->listar(date('Y-m-d'));

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Dashboard</h1>
    <p>Resumo operacional do dia.</p>
</section>

<section class="stats-grid <?= $mostrarReceita ? '' : 'stats-grid-centered' ?>">
    <article class="stat-card">
        <span>Pedidos abertos</span>
        <strong><?= $pedidosAbertos ?></strong>
    </article>
    <article class="stat-card">
        <span>Reservas de hoje</span>
        <strong><?= $reservasHoje ?></strong>
    </article>
    <article class="stat-card">
        <span>Pratos indisponíveis</span>
        <strong><?= $pratosIndisponiveis ?></strong>
    </article>
    <article class="stat-card">
        <span>Promoções ativas</span>
        <strong><?= $promocoesAtivas ?></strong>
    </article>
    <?php if ($mostrarReceita): ?>
        <article class="stat-card">
            <span>Receita hoje</span>
            <strong><?= formatMoney($faturamentoHoje) ?></strong>
        </article>
    <?php endif; ?>
</section>

<section class="split-layout">
    <article class="panel">
        <div class="section-head">
            <h2>Pedidos recentes</h2>
            <a class="btn btn-ghost" href="gerenciar_pedidos.php">Ver todos</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosPedidos as $pedido): ?>
                        <tr>
                            <td><?= (int) $pedido['id'] ?></td>
                            <td><?= e($pedido['cliente']) ?></td>
                            <td><span class="status status-<?= e($pedido['status']) ?>"><?= statusLabel($pedido['status']) ?></span></td>
                            <td><?= formatMoney($pedido['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$ultimosPedidos): ?>
                        <tr><td colspan="4">Sem pedidos recentes.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="panel">
        <div class="section-head">
            <h2>Reservas de hoje</h2>
            <a class="btn btn-ghost" href="gerenciar_reservas.php">Ver todas</a>
        </div>
        <div class="stack">
            <?php foreach ($reservasDoDia as $reserva): ?>
                <div class="reservation-row">
                    <div>
                        <strong><?= e($reserva['cliente']) ?></strong>
                        <span class="muted"><?= substr((string) $reserva['horario'], 0, 5) ?> - <?= (int) $reserva['num_pessoas'] ?> pessoa(s)</span>
                    </div>
                    <span class="status status-<?= e($reserva['status']) ?>"><?= statusLabel($reserva['status']) ?></span>
                </div>
            <?php endforeach; ?>
            <?php if (!$reservasDoDia): ?>
                <p class="muted">Sem reservas para hoje.</p>
            <?php endif; ?>
        </div>
    </article>
</section>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
