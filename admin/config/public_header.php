<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../models/Carrinho.php';

$pageTitle = $pageTitle ?? 'MenuStock';
$publicUser = currentUser();
$cartCount = 0;

if ($publicUser && ($publicUser['tipo'] ?? '') === 'user') {
    $cartCount = (new Carrinho())->contarItens((int) $publicUser['id']);
}

$flashMessages = consumeFlash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - MenuStock</title>
    <link rel="stylesheet" href="admin/public/css/style.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">MenuStock</a>
    <nav class="site-nav" aria-label="Menu principal">
        <?php if ($publicUser && $publicUser['tipo'] === 'garcom'): ?>
            <a href="admin/views/menu_garcom.php">Menu</a>
        <?php else: ?>
            <a href="index.php">Cardápio</a>
        <?php endif; ?>
        <?php if ($publicUser && $publicUser['tipo'] === 'user'): ?>
            <a href="carrinho.php">Carrinho <span class="badge"><?= $cartCount ?></span></a>
            <a href="meus_pedidos.php">Meus pedidos</a>
            <a href="reserva.php">Reservas</a>
        <?php endif; ?>
        <?php if ($publicUser && in_array($publicUser['tipo'], ['admin', 'garcom'], true)): ?>
            <a href="admin/views/dashboard.php">Painel</a>
        <?php endif; ?>
    </nav>
    <div class="site-actions">
        <?php if ($publicUser): ?>
            <span><?= e($publicUser['nome']) ?></span>
            <form method="post" action="admin/controllers/logout.php">
                <?= csrfField() ?>
                <button class="btn btn-ghost" type="submit">Sair</button>
            </form>
        <?php else: ?>
            <a class="btn btn-primary" href="login.php">Entrar</a>
        <?php endif; ?>
    </div>
</header>
<main class="container">
    <?php foreach ($flashMessages as $message): ?>
        <div class="alert alert-<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endforeach; ?>
