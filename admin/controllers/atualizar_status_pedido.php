<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

requireRole(['admin', 'garcom']);
verifyCsrfOrFail();

$pedidoModel = new Pedido();
$pedido = $pedidoModel->buscarPorId((int) ($_POST['id'] ?? 0));

if ($pedido && in_array($pedido['status'], ['entregue', 'cancelado'], true)) {
    flash('error', 'Este pedido está finalizado e não pode ser alterado.');
    redirect('../views/gerenciar_pedidos.php');
}

$ok = $pedidoModel->atualizarStatus((int) ($_POST['id'] ?? 0), (string) ($_POST['status'] ?? ''));
flash($ok ? 'success' : 'error', $ok ? 'Status do pedido atualizado.' : 'Não foi possível atualizar o pedido.');
redirect('../views/gerenciar_pedidos.php');
