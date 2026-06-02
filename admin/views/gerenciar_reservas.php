<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Reserva.php';

requireRole(['admin', 'garcom']);

$reservaModel = new Reserva();
$dataFiltro = (string) ($_GET['data'] ?? '');
$reservas = $reservaModel->listar($dataFiltro !== '' ? $dataFiltro : null);

$statusOptions = array_values(array_filter(
    Reserva::STATUS,
    static fn (string $status): bool => canManageReservaStatus($status)
));

$pageTitle = 'Reservas';
require_once __DIR__ . '/../config/header_admin.php';
?>

<section class="page-heading">
    <h1>Reservas</h1>

    <p>
        <?= hasRole('admin')
            ? 'Confirme ou cancele solicitações de mesa.'
            : 'Visualização de reservas.'
        ?>
    </p>
</section>

<section class="toolbar section-head">
    <form method="get" class="filters">
        <label style="min-width: 150px;">
            Data
            <input type="date" name="data" value="<?= e($dataFiltro) ?>" min="<?= date('Y-m-d') ?>">
        </label>

        <div class="inline-form">
            <button class="btn btn-primary" type="submit">
                Filtrar
            </button>
            <a class="btn btn-ghost" href="gerenciar_reservas.php">
                Limpar
            </a>
        </div>
    </form>
</section>

<section class="stack">
    <?php foreach ($reservas as $reserva): ?>
        <article class="panel order-card">
            <div class="order-head">
                <div>
                    <h2>Reserva #<?= (int) $reserva['id'] ?></h2>
                    <span class="muted">
                        <strong><?= e($reserva['cliente']) ?></strong>
                        <?php if ($reserva['telefone']): ?>
                            - <?= e($reserva['telefone']) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <form method="post" action="../controllers/atualizar_status_reserva.php" class="inline-form reserva-status-form">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= (int) $reserva['id'] ?>">
                    <select name="status" <?= !canManageReservaStatus($reserva['status']) ? 'disabled' : '' ?>>
                        <?php foreach ($statusOptions as $status): ?>
                            <option value="<?= e($status) ?>" <?= $reserva['status'] === $status ? 'selected' : '' ?>>
                                <?= statusLabel($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-secondary" type="submit" <?= !canManageReservaStatus($reserva['status']) ? 'disabled' : '' ?>>Salvar</button>
                </form>
            </div>

            <div class="table-wrap">
                <table class="reservas-card-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Pessoas</th>
                            <th>Obs.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= date('d/m', strtotime((string) $reserva['data'])) ?></td>
                            <td><?= substr((string) $reserva['horario'], 0, 5) ?></td>
                            <td><?= (int) $reserva['num_pessoas'] ?></td>
                            <td><?= e($reserva['observacao'] ?: '-') ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if (!$reservas): ?>
        <div class="empty-state">Nenhuma reserva encontrada.</div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
