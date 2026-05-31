<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/conexao.php';

date_default_timezone_set('America/Sao_Paulo');

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consumeFlash(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function verifyCsrfOrFail(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Token CSRF inválido.');
    }
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['usuario_id']);
}

function currentUserId(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
}

function currentUserTipo(): ?string
{
    return $_SESSION['usuario_tipo'] ?? null;
}

function hasRole(array|string $roles): bool
{
    $roles = is_array($roles) ? $roles : [$roles];
    return isLoggedIn() && in_array(currentUserTipo(), $roles, true);
}

function canManageReservaStatus(string $status): bool
{
    if (hasRole('admin')) {
        return true;
    }

    return hasRole('garcom') && $status !== 'cancelada';
}

function canManagePratoDisponibilidade(): bool
{
    return hasRole('admin');
}

function getActiveWaiterClientId(): int
{
    return isset($_SESSION['active_waiter_client_id']) ? (int) $_SESSION['active_waiter_client_id'] : 0;
}

function setActiveWaiterClientId(int $id): void
{
    $_SESSION['active_waiter_client_id'] = $id;
}

function clearActiveWaiterClientId(): void
{
    unset($_SESSION['active_waiter_client_id']);
}

function handleGenericDeletion(
    string $modelClass,
    string $successMsg,
    string $errorMsg,
    string $redirectUrl,
    array $extraArgs = []
): never {
    requireRole('admin');
    verifyCsrfOrFail();

    $id = (int) ($_POST['id'] ?? 0);
    $ok = false;

    try {
        $model = new $modelClass();
        if (!empty($extraArgs)) {
            $ok = $model->deletar($id, ...$extraArgs);
        } else {
            $ok = $model->deletar($id);
        }
    } catch (Throwable) {
        $ok = false;
    }

    flash($ok ? 'success' : 'error', $ok ? $successMsg : $errorMsg);
    redirect($redirectUrl);
}

function handleGenericPostCRUD(
    object $model,
    string $successCadastrar,
    string $successEditar,
    string $redirectUrl
): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrfOrFail();
        $acao = (string) ($_POST['acao'] ?? 'cadastrar');

        try {
            if ($acao === 'editar') {
                $model->editar((int) ($_POST['id'] ?? 0), $_POST);
                flash('success', $successEditar);
            } else {
                $model->cadastrar($_POST);
                flash('success', $successCadastrar);
            }
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
        }

        redirect($redirectUrl);
    }
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = getConnection()->prepare('SELECT id, nome, email, telefone, tipo FROM usuarios WHERE id = ?');
    $stmt->execute([currentUserId()]);
    $user = $stmt->fetch();

    if (!$user) {
        logoutUser();
        return null;
    }

    return $user;
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $user['id'];
    $_SESSION['usuario_tipo'] = $user['tipo'];
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}

function requireLogin(string $redirectTo = 'login.php'): void
{
    if (!isLoggedIn()) {
        flash('warning', 'Entre com seu e-mail e senha para continuar.');
        redirect($redirectTo);
    }
}

function requireRole(array|string $roles, string $redirectTo = '../../login.php'): void
{
    requireLogin($redirectTo);
    if (!hasRole($roles)) {
        flash('error', 'Você não tem permissão para acessar esta área.');
        redirect($redirectTo);
    }
}

function redirectByRole(): never
{
    if (hasRole(['admin', 'garcom'])) {
        redirect('admin/views/dashboard.php');
    }

    redirect('index.php');
}

function formatMoney(float|int|string $value): string
{
    return 'R$ ' . number_format((float) $value, 2, ',', '.');
}

function statusLabel(string $status): string
{
    $labels = [
        'recebido' => 'Recebido',
        'preparo' => 'Em preparo',
        'pronto' => 'Pronto',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado',
        'pendente' => 'Pendente',
        'confirmada' => 'Confirmada',
        'cancelada' => 'Cancelada',
    ];

    return $labels[$status] ?? ucfirst($status);
}

function publicImagePath(?string $image): string
{
    if (!$image) {
        return 'admin/public/imagens/placeholder.svg';
    }

    return 'admin/public/imagens/' . rawurlencode($image);
}
