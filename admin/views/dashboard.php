<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Reserva.php';

requireRole(['admin', 'garcom']);

$pdo = getConnection();
$pedidoModel = new Pedido();
$reservaModel = new Reserva();

$pedidosRecebidos = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'recebido'")->fetchColumn();
$pedidosPreparo = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'preparo'")->fetchColumn();
$pedidosProntos = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'pronto'")->fetchColumn();
$reservasHoje = (int) $pdo->query('SELECT COUNT(*) FROM reservas WHERE data = CURDATE() AND status <> "cancelada"')->fetchColumn();
$pratosIndisponiveis = (int) $pdo->query('SELECT COUNT(*) FROM pratos WHERE disponivel = 0')->fetchColumn();
$promocoesAtivas = (int) $pdo->query('SELECT COUNT(*) FROM vw_promocoes_ativas')->fetchColumn();
$mostrarReceita = hasRole('admin');
$faturamentoHoje = $mostrarReceita
    ? (float) $pdo->query('SELECT COALESCE(SUM(total), 0) FROM pedidos WHERE DATE(created_at) = CURDATE() AND status <> "cancelado"')->fetchColumn()
    : 0.0;

$verTodos       = isset($_GET['pedidos']) && $_GET['pedidos'] === 'todos';
$ultimosPedidos = $verTodos
    ? $pedidoModel->listar()
    : array_slice($pedidoModel->listar(null, true), 0, 6);
$totalHoje      = $pedidoModel->contar(null, true);
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
        <span>Recebidos</span>
        <strong><?= $pedidosRecebidos ?></strong>
    </article>
    <article class="stat-card">
        <span>Em preparo</span>
        <strong><?= $pedidosPreparo ?></strong>
    </article>
    <article class="stat-card">
        <span>Prontos</span>
        <strong><?= $pedidosProntos ?></strong>
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
            <h2>
                <?= $verTodos ? 'Todos os pedidos' : 'Pedidos recentes' ?>
                <?php if (!$verTodos && $totalHoje > 0): ?>
                    <span class="badge"><?= $totalHoje ?></span>
                <?php endif; ?>
            </h2>
            <div class="inline-form" style="gap: 8px;">
                <?php if ($verTodos): ?>
                    <a class="btn btn-ghost" href="dashboard.php">Recentes</a>
                <?php else: ?>
                    <a class="btn btn-ghost" href="dashboard.php?pedidos=todos">Ver todos</a>
                <?php endif; ?>
                <a class="btn btn-ghost" href="gerenciar_pedidos.php">Gerenciar</a>
            </div>
        </div>

        <?php if (!$verTodos): ?>
            <p class="muted dashboard-date-label">
                Exibindo pedidos de hoje —
                <?= date('d/m/Y') ?>
            </p>
        <?php else: ?>
            <p class="muted dashboard-date-label">
                Exibindo todos os pedidos (histórico completo)
            </p>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cliente / Mesa</th>
                        <th>Status</th>
                        <th>Total</th>
                        <?php if ($verTodos): ?>
                            <th>Data</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimosPedidos as $i => $pedido): ?>
                        <tr>
                            <td><?= $verTodos ? (int) $pedido['id'] : count($ultimosPedidos) - $i ?></td>
                            <td>
                                <?php if (!empty($pedido['mesa'])): ?>
                                    <strong>Mesa <?= (int) $pedido['mesa'] ?></strong>
                                <?php else: ?>
                                    <?= e($pedido['cliente'] ?? '—') ?>
                                <?php endif; ?>
                            </td>
                            <td><span class="status status-<?= e($pedido['status']) ?>"><?= statusLabel($pedido['status']) ?></span></td>
                            <td><?= formatMoney($pedido['total']) ?></td>
                            <?php if ($verTodos): ?>
                                <td class="muted">
                                    <?= date('d/m/Y', strtotime((string) $pedido['created_at'])) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$ultimosPedidos): ?>
                        <tr>
                            <td colspan="<?= $verTodos ? 5 : 4 ?>" class="muted" style="text-align:center; padding: 18px 0;">
                                <?= $verTodos ? 'Nenhum pedido encontrado.' : 'Nenhum pedido hoje.' ?>
                            </td>
                        </tr>
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
