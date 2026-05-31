<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Carrinho.php';
require_once __DIR__ . '/admin/models/Pedido.php';

requireRole('user', 'login.php');

$usuarioId = currentUserId() ?? 0;
$carrinhoModel = new Carrinho();
$pedidoModel = new Pedido();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    $acao = (string) ($_POST['acao'] ?? '');

    if ($acao === 'atualizar') {
        foreach (($_POST['quantidades'] ?? []) as $itemId => $quantidade) {
            $carrinhoModel->atualizarQuantidade($usuarioId, (int) $itemId, (int) $quantidade);
        }
        flash('success', 'Carrinho atualizado.');
        redirect('carrinho.php');
    }

    if ($acao === 'remover') {
        $carrinhoModel->remover($usuarioId, (int) ($_POST['item_id'] ?? 0));
        flash('success', 'Item removido.');
        redirect('carrinho.php');
    }

    if ($acao === 'confirmar') {
        $pedidoId = $pedidoModel->criarDoCarrinho($usuarioId, (string) ($_POST['obs_geral'] ?? ''));
        flash($pedidoId ? 'success' : 'error', $pedidoId ? 'Pedido confirmado.' : 'Seu carrinho está vazio.');
        redirect($pedidoId ? 'meus_pedidos.php' : 'carrinho.php');
    }
}

$estadoCarrinho = $carrinhoModel->obterEstadoCarrinho($usuarioId);
$itens = $estadoCarrinho['itens'];
$total = $estadoCarrinho['total'];
$pageTitle = 'Carrinho';
require_once __DIR__ . '/admin/config/public_header.php';
?>
<section class="page-heading">
    <h1>Carrinho</h1>
    <p>Revise as quantidades antes de confirmar o pedido.</p>
</section>

<?php if (!$itens): ?>
    <div class="empty-state" style="display: flex; justify-content: space-between; align-items: center;">
        <span>Seu carrinho está vazio.</span>
        <a class="btn btn-primary" href="index.php">Ver cardápio</a>
    </div>
<?php else: ?>
    <div class="cart-panel">
    <?php require __DIR__ . '/admin/views/partials/_carrinho_table.php'; ?>

    <form method="post" class="panel stack-form" style="margin-top:18px;">
        <?= csrfField() ?>
        <input type="hidden" name="acao" value="confirmar">
        <label>
            Observação geral do pedido
            <textarea name="obs_geral" rows="3" placeholder="Ex: Ponto da carne, restrições ou instruções gerais"></textarea>
        </label>
        <button class="btn btn-primary" type="submit">Confirmar pedido</button>
    </form>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
