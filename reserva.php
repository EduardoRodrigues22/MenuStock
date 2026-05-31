<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Reserva.php';

requireRole('user', 'login.php');

$usuarioId = currentUserId() ?? 0;
$reservaModel = new Reserva();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $acao = (string) ($_POST['acao'] ?? 'criar');

    try {
        if ($acao === 'cancelar') {
            $ok = $reservaModel->cancelar((int) ($_POST['reserva_id'] ?? 0), $usuarioId);
            flash($ok ? 'success' : 'error', $ok ? 'Reserva removida.' : 'Não foi possível remover a reserva.');
            redirect('reserva.php');
        }

        $reservaModel->cadastrar($usuarioId, $_POST);
        flash('success', 'Reserva solicitada. Aguarde a confirmação.');
        redirect('reserva.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('reserva.php');
    }
}

$reservas = $reservaModel->listarPorUsuario($usuarioId);
$pageTitle = 'Reservas';
require_once __DIR__ . '/admin/config/public_header.php';
?>
<section class="page-heading">
    <h1>Reservas</h1>
    <p>Solicite uma mesa e acompanhe a confirmação pela equipe.</p>
</section>

<section class="split-layout">
    <article class="panel">
        <h2>Nova reserva</h2>
        <form method="post" class="stack-form">
            <?= csrfField() ?>
            <input type="hidden" name="acao" value="criar">
            <label>
                Data
                <input type="date" name="data" min="<?= date('Y-m-d') ?>" required>
            </label>
            <label>
                Horário
                <input type="time" name="horario" min="10:00" max="23:00" required>
            </label>
            <label>
                Pessoas
                <input type="number" name="num_pessoas" min="1" max="60" value="2" required>
            </label>
            <label>
                Observação
                <textarea name="observacao" rows="3" placeholder="Ex: mesa próxima à janela"></textarea>
            </label>
            <button class="btn btn-primary" type="submit">Solicitar reserva</button>
        </form>
    </article>

    <article class="panel">
        <h2>Minhas reservas</h2>
        <?php if (!$reservas): ?>
            <p class="muted">Nenhuma reserva registrada.</p>
        <?php else: ?>
            <div class="stack">
                <?php foreach ($reservas as $reserva): ?>
                    <div class="reservation-row">
                        <div>
                            <strong><?= date('d/m/Y', strtotime((string) $reserva['data'])) ?> às <?= substr((string) $reserva['horario'], 0, 5) ?></strong>
                            <span class="muted"><?= (int) $reserva['num_pessoas'] ?> pessoa(s)</span>
                            <?php if ($reserva['observacao']): ?>
                                <span class="muted"><?= e($reserva['observacao']) ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="status status-<?= e($reserva['status']) ?>"><?= statusLabel($reserva['status']) ?></span>
                        <?php if ($reserva['status'] !== 'cancelada'): ?>
                            <form method="post">
                                <?= csrfField() ?>
                                <input type="hidden" name="acao" value="cancelar">
                                <input type="hidden" name="reserva_id" value="<?= (int) $reserva['id'] ?>">
                                <button class="btn btn-danger" type="submit">Cancelar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>
<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
