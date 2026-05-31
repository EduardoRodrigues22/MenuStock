<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

handleGenericDeletion(
    Categoria::class,
    'Categoria excluída.',
    'Categoria com pratos vinculados não pode ser excluída.',
    '../views/gerenciar_categorias.php'
);
