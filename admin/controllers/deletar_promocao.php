<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Promocao.php';

handleGenericDeletion(
    Promocao::class,
    'Promoção excluída.',
    'Não foi possível excluir a promoção.',
    '../views/gerenciar_promocoes.php'
);
