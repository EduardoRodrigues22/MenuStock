<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Prato.php';

handleGenericDeletion(
    Prato::class,
    'Prato excluído.',
    'Não foi possível excluir. Verifique pedidos vinculados.',
    '../views/gerenciar_pratos.php'
);
