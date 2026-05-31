<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
requireRole(['admin', 'garcom']);

$adminUser = currentUser();
$pageTitle = $pageTitle ?? 'MenuStock Admin';
$flashMessages = consumeFlash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> - MenuStock</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body class="admin-body">
<header class="admin-topbar">
    <a class="brand" href="<?= hasRole('garcom') ? 'menu_garcom.php' : 'dashboard.php' ?>">MenuStock</a>
    <nav class="admin-nav" aria-label="Menu administrativo">
        <a href="dashboard.php">Dashboard</a>
        <a href="gerenciar_pratos.php">Pratos</a>
        <a href="gerenciar_pedidos.php">Pedidos</a>
        <a href="gerenciar_reservas.php">Reservas</a>
        <?php if (hasRole('garcom')): ?>
            <a href="menu_garcom.php">Menu Garçom</a>
        <?php endif; ?>
        <?php if (hasRole('admin')): ?>
            <a href="gerenciar_categorias.php">Categorias</a>
            <a href="gerenciar_ingredientes.php">Ingredientes</a>
            <a href="gerenciar_promocoes.php">Promoções</a>
            <a href="ver_usuarios.php">Usuários</a>
        <?php endif; ?>
    </nav>
    <div class="topbar-actions">
        <span><?= e($adminUser['nome'] ?? 'Usuário') ?> (<?= e(currentUserTipo() ?? '') ?>)</span>
        <a class="btn btn-ghost" href="../../index.php">Loja</a>
        <form method="post" action="../controllers/logout.php">
            <?= csrfField() ?>
            <button class="btn btn-danger" type="submit">Sair</button>
        </form>
    </div>
</header>
<main class="container admin-main">
    <?php foreach ($flashMessages as $message): ?>
        <div class="alert alert-<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
    <?php endforeach; ?>
