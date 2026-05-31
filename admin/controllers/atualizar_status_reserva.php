<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Reserva.php';

requireRole(['admin', 'garcom']);
verifyCsrfOrFail();

$reserva = new Reserva();
$id = (int) ($_POST['id'] ?? 0);
$status = (string) ($_POST['status'] ?? '');

if (!canManageReservaStatus($status)) {
    flash('error', 'Garçom não tem permissão para cancelar reservas.');
    redirect('../views/gerenciar_reservas.php');
}

if ($status === 'confirmada') {
    $ok = $reserva->confirmar($id);
} elseif ($status === 'cancelada') {
    $ok = $reserva->cancelar($id);
} else {
    $ok = $reserva->atualizarStatus($id, $status);
}

flash($ok ? 'success' : 'error', $ok ? 'Status da reserva atualizado.' : 'Não foi possível atualizar a reserva.');
redirect('../views/gerenciar_reservas.php');
