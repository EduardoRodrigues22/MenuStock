<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Carrinho.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Prato.php';
require_once __DIR__ . '/../models/Usuario.php';

requireRole('garcom');

function menuGarcomUrl(): string
{
    return 'menu_garcom.php';
}

$garcomId = currentUserId() ?? 0;
$carrinhoModel = new Carrinho();
$categoriaModel = new Categoria();
$pedidoModel = new Pedido();
$pratoModel = new Prato();
$usuarioModel = new Usuario();

$clientes = $usuarioModel->listarClientes();

if (isset($_GET['clear_cliente'])) {
    clearActiveWaiterClientId();
    redirect('menu_garcom.php');
}

if (isset($_POST['cliente_id']) || isset($_GET['cliente_id'])) {
    $clienteId = (int) ($_POST['cliente_id'] ?? $_GET['cliente_id'] ?? 0);
    setActiveWaiterClientId($clienteId);
} else {
    $clienteId = getActiveWaiterClientId();
}

$clienteSelecionado = null;

if ($clienteId > 0) {
    $clienteSelecionado = $usuarioModel->buscarPorId($clienteId);

    if (!$clienteSelecionado || $clienteSelecionado['tipo'] !== 'user') {
        clearActiveWaiterClientId();
        flash('error', 'Selecione um cliente válido para o pedido.');
        redirect('menu_garcom.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $acao = (string) ($_POST['acao'] ?? '');

    try {
        if ($acao === 'adicionar') {
            $ok = $carrinhoModel->adicionar(
                $garcomId,
                (int) ($_POST['prato_id'] ?? 0),
                (int) ($_POST['quantidade'] ?? 1),
                (string) ($_POST['obs_item'] ?? '')
            );

            flash($ok ? 'success' : 'error', $ok ? 'Item adicionado ao Menu Garçom.' : 'Não foi possível adicionar o item.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'atualizar') {
            foreach (($_POST['quantidades'] ?? []) as $itemId => $quantidade) {
                $carrinhoModel->atualizarQuantidade($garcomId, (int) $itemId, (int) $quantidade);
            }

            flash('success', 'Menu Garçom atualizado.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'remover') {
            $carrinhoModel->remover($garcomId, (int) ($_POST['item_id'] ?? 0));
            flash('success', 'Item removido.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'limpar') {
            $carrinhoModel->limpar($garcomId);
            flash('success', 'Menu Garçom limpo.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'confirmar') {
            if (!$clienteSelecionado) {
                flash('error', 'Selecione o cliente que receberá o pedido.');
                redirect('menu_garcom.php');
            }

            $pedidoId = $pedidoModel->criarDoCarrinhoParaUsuario(
                $garcomId,
                $clienteId,
                (string) ($_POST['obs_geral'] ?? '')
            );

            if ($pedidoId) {
                clearActiveWaiterClientId();
            }

            flash(
                $pedidoId ? 'success' : 'error',
                $pedidoId
                    ? 'Pedido confirmado para ' . $clienteSelecionado['nome'] . '.'
                    : 'O Menu Garçom está vazio.'
            );

            redirect($pedidoId ? 'gerenciar_pedidos.php' : menuGarcomUrl());
        }
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect(menuGarcomUrl());
    }
}

$busca = trim((string) ($_GET['q'] ?? ''));
$categoriaId = isset($_GET['categoria']) && $_GET['categoria'] !== ''
    ? (int) $_GET['categoria']
    : null;

$categorias = $categoriaModel->listar();
$pratos = $pratoModel->listarDisponiveis($busca ?: null, $categoriaId);
$estadoCarrinho = $carrinhoModel->obterEstadoCarrinho($garcomId);
$itens = $estadoCarrinho['itens'];
$total = $estadoCarrinho['total'];

$pageTitle = 'Menu Garçom';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Menu Garçom</h1>
    <p>Monte o menu e confirme o pedido em nome de um cliente.</p>
</section>

<section class="toolbar section-head">
    <form method="get" class="filters">
        <label>
            Cliente do pedido
            <select name="cliente_id">
                <option value="">Selecionar cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= (int) $cliente['id'] ?>" <?= $clienteId === (int) $cliente['id'] ? 'selected' : '' ?>>
                        <?= e($cliente['nome']) ?> - <?= e($cliente['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button class="btn btn-primary" type="submit">Atribuir</button>

        <a class="btn btn-ghost" href="menu_garcom.php?clear_cliente=1">Limpar cliente</a>
    </form>
</section>

<?php if ($clienteSelecionado): ?>
    <div class="alert alert-success">
        Pedido sera atribuido a <?= e($clienteSelecionado['nome']) ?>.
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Selecione o cliente antes de confirmar o pedido.
    </div>
<?php endif; ?>

<?php if (!$itens): ?>
    <div class="empty-state">
        Menu Garçom vazio. Adicione pratos abaixo.
    </div>
<?php else: ?>
    <?php require __DIR__ . '/partials/_carrinho_table.php'; ?>

    <form method="post" class="panel stack-form">
        <?= csrfField() ?>
        <input type="hidden" name="acao" value="confirmar">

        <label>
            Observação geral do pedido
            <textarea name="obs_geral" rows="3" placeholder="Ex: pedido lançado pelo garçom, mesa 4, sem talheres"></textarea>
        </label>

        <button class="btn btn-primary" type="submit" <?= $clienteSelecionado ? '' : 'disabled' ?>>Confirmar pedido para cliente</button>
    </form>
<?php endif; ?>

<section class="toolbar">
    <form class="filters" method="get" action="menu_garcom.php">
        <label>
            Buscar
            <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Nome, categoria ou descrição">
        </label>

        <label>
            Categoria
            <select name="categoria">
                <option value="">Todas</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int) $categoria['id'] ?>" <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>>
                        <?= e($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button class="btn btn-primary" type="submit">Filtrar</button>
        <a class="btn btn-ghost" href="<?= e(menuGarcomUrl()) ?>">Limpar</a>
    </form>
</section>

<section class="grid menu-grid">
    <?php if (!$pratos): ?>
        <div class="empty-state">Nenhum prato disponível para os filtros informados.</div>
    <?php endif; ?>

    <?php foreach ($pratos as $prato): ?>
        <?php require __DIR__ . '/partials/_dish_card.php'; ?>
    <?php endforeach; ?>
</section>

<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
