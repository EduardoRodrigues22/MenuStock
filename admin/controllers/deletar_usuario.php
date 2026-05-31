<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

handleGenericDeletion(
    Usuario::class,
    'Usuário excluído.',
    'Não foi possível excluir este usuário.',
    '../views/ver_usuarios.php',
    [currentUserId()]
);
