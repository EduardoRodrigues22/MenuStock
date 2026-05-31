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
        <label>
            Data
            <input type="date" name="data" value="<?= e($dataFiltro) ?>">
        </label>

        <button class="btn btn-primary" type="submit">
            Filtrar
        </button>

        <a class="btn btn-ghost" href="gerenciar_reservas.php">
            Limpar
        </a>
    </form>
</section>

<div class="panel table-wrap">
    <table class="reservas-table">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Data</th>
                <th>Horário</th>
                <th>Pessoas</th>
                <th>Status</th>
                <th>Observação</th>

                <?php if (hasRole('admin')): ?>
                    <th style="width: 1%; white-space: nowrap;">Ação</th>
                <?php endif; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td>
                        <strong><?= e($reserva['cliente']) ?></strong>

                        <span class="muted">
                            <?= e($reserva['telefone']) ?>
                        </span>
                    </td>

                    <td>
                        <?= date('d/m/Y', strtotime((string) $reserva['data'])) ?>
                    </td>

                    <td>
                        <?= substr((string) $reserva['horario'], 0, 5) ?>
                    </td>

                    <td>
                        <?= (int) $reserva['num_pessoas'] ?>
                    </td>

                    <td>
                        <span class="status status-<?= e($reserva['status']) ?>">
                            <?= statusLabel($reserva['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?= e($reserva['observacao'] ?: '-') ?>
                    </td>

                    <?php if (hasRole('admin')): ?>
                        <td>
                            <form
                                method="post"
                                action="../controllers/atualizar_status_reserva.php"
                                class="inline-form reserva-status-form"
                            >
                                <?= csrfField() ?>

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int) $reserva['id'] ?>"
                                >

                                <select name="status">
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= $reserva['status'] === $status ? 'selected' : '' ?>
                                        >
                                            <?= statusLabel($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button
                                    class="btn btn-secondary"
                                    type="submit"
                                >
                                    Salvar
                                </button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>

            <?php if (!$reservas): ?>
                <tr>
                    <td colspan="<?= hasRole('admin') ? '7' : '6' ?>">
                        Nenhuma reserva encontrada.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
