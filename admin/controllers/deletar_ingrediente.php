<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Ingrediente.php';

handleGenericDeletion(
    Ingrediente::class,
    'Ingrediente excluído.',
    'Ingrediente vinculado a pratos não pode ser excluído.',
    '../views/gerenciar_ingredientes.php'
);
