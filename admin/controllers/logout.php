<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../index.php');
}

verifyCsrfOrFail();
logoutUser();
redirect('../../index.php');
