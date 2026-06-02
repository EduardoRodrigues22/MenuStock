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

if (isset($_GET['clear_mesa'])) {
    clearActiveWaiterTable();
    redirect('menu_garcom.php');
}

if (isset($_POST['mesa']) || isset($_GET['mesa'])) {
    $mesaAtiva = (int) ($_POST['mesa'] ?? $_GET['mesa'] ?? 0);
    if ($mesaAtiva >= 1 && $mesaAtiva <= 20) {
        setActiveWaiterTable($mesaAtiva);
    } else {
        clearActiveWaiterTable();
    }
} else {
    $mesaAtiva = getActiveWaiterTable();
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

            flash($ok ? 'success' : 'error', $ok ? 'Item adicionado ao Menu.' : 'Não foi possível adicionar o item.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'atualizar') {
            foreach (($_POST['quantidades'] ?? []) as $itemId => $quantidade) {
                $carrinhoModel->atualizarQuantidade($garcomId, (int) $itemId, (int) $quantidade);
            }

            flash('success', 'Menu atualizado.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'remover') {
            $carrinhoModel->remover($garcomId, (int) ($_POST['item_id'] ?? 0));
            flash('success', 'Item removido.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'limpar') {
            $carrinhoModel->limpar($garcomId);
            flash('success', 'Menu limpo.');
            redirect(menuGarcomUrl());
        }

        if ($acao === 'confirmar') {
            if ($mesaAtiva < 1 || $mesaAtiva > 20) {
                flash('error', 'Selecione a mesa antes de confirmar o pedido.');
                redirect('menu_garcom.php');
            }

            $pedidoId = $pedidoModel->criarDoCarrinhoParaUsuario(
                $garcomId,
                null,
                $mesaAtiva,
                (string) ($_POST['obs_geral'] ?? '')
            );

            if ($pedidoId) {
                clearActiveWaiterTable();
            }

            flash(
                $pedidoId ? 'success' : 'error',
                $pedidoId
                    ? 'Pedido confirmado para a Mesa ' . $mesaAtiva . '.'
                    : 'O Menu está vazio.'
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

$pageTitle = 'Menu';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Menu</h1>
    <p>Monte o menu e confirme o pedido em nome de um cliente.</p>
</section>

<section class="toolbar section-head">
    <form method="get" class="filters">
        <label style="min-width: 160px;">
            Mesa do pedido
            <select name="mesa" onchange="this.form.submit()">
                <option value="">Selecionar mesa</option>
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <option value="<?= $i ?>" <?= $mesaAtiva === $i ? 'selected' : '' ?>>Mesa <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </label>

        <a class="btn btn-ghost" href="menu_garcom.php?clear_mesa=1">Limpar mesa</a>
    </form>
</section>

<?php if ($mesaAtiva >= 1 && $mesaAtiva <= 20): ?>
    <div class="alert alert-success">
        Pedido será atribuído à **Mesa <?= $mesaAtiva ?>**.
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Selecione a mesa antes de confirmar o pedido.
    </div>
<?php endif; ?>

<?php if (!$itens): ?>
    <div class="empty-state">
        Menu vazio. Adicione pratos abaixo.
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

        <button class="btn btn-primary" type="submit" <?= ($mesaAtiva >= 1 && $mesaAtiva <= 20) ? '' : 'disabled' ?>>Confirmar pedido para a Mesa</button>
    </form>
<?php endif; ?>

<section class="toolbar">
    <form class="filters" method="get" action="menu_garcom.php">
        <label style="min-width: 150px;">
            Buscar
            <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Nome, categoria ou descrição">
        </label>

        <label style="min-width: 130px;">
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

        <div class="inline-form">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-ghost" href="<?= e(menuGarcomUrl()) ?>">Limpar</a>
        </div>
    </form>
</section>

<section class="grid menu-grid menu-card-grid">
    <?php if (!$pratos): ?>
        <div class="empty-state">Nenhum prato disponível para os filtros informados.</div>
    <?php endif; ?>

    <?php foreach ($pratos as $prato): ?>
        <?php require __DIR__ . '/partials/_dish_card.php'; ?>
    <?php endforeach; ?>
</section>

<script>
(function() {
  document.querySelectorAll('.menu-card-grid .qty-control').forEach(function(control) {
    var minusBtn = control.querySelector('.qty-minus');
    var plusBtn = control.querySelector('.qty-plus');
    var input = control.querySelector('.qty-input');

    minusBtn.addEventListener('click', function() {
      var val = parseInt(input.value) || 1;
      if (val > 1) { input.value = val - 1; }
    });

    plusBtn.addEventListener('click', function() {
      var val = parseInt(input.value) || 1;
      if (val < 99) { input.value = val + 1; }
    });
  });
})();
</script>

<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
