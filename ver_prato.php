<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Prato.php';
require_once __DIR__ . '/admin/models/Ingrediente.php';
require_once __DIR__ . '/admin/models/Carrinho.php';

$pratoModel = new Prato();
$ingredienteModel = new Ingrediente();
$carrinhoModel = new Carrinho();
$id = (int) ($_GET['id'] ?? $_POST['prato_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'adicionar') {
    verifyCsrfOrFail();
    requireLogin('login.php');

    if (!hasRole('user')) {
        flash('error', 'Apenas clientes podem comprar pela vitrine.');
        redirect('ver_prato.php?id=' . $id);
    }

    $ok = $carrinhoModel->adicionar(
        currentUserId() ?? 0,
        $id,
        (int) ($_POST['quantidade'] ?? 1),
        (string) ($_POST['obs_item'] ?? '')
    );

    flash($ok ? 'success' : 'error', $ok ? 'Item adicionado ao carrinho.' : 'Prato indisponível.');
    redirect($ok ? 'index.php' : 'ver_prato.php?id=' . $id);
}

$prato = $pratoModel->buscarPorId($id, true);

if (!$prato) {
    http_response_code(404);
    $pageTitle = 'Prato não encontrado';
    require_once __DIR__ . '/admin/config/public_header.php';
    echo '<div class="empty-state">Prato não encontrado ou indisponível.</div>';
    require_once __DIR__ . '/admin/config/public_footer.php';
    exit;
}

$ingredientes = $ingredienteModel->listarPorPrato($id);
$precoAtual = $prato['preco_promocional'] !== null ? (float) $prato['preco_promocional'] : (float) $prato['preco'];
$pageTitle = $prato['nome'];

require_once __DIR__ . '/admin/config/public_header.php';
?>
<section class="detail-layout">
    <div>
        <img
            src="<?= e(publicImagePath($prato['imagem'] ?? null)) ?>"
            onerror="this.src='admin/public/imagens/placeholder.svg'"
            alt="<?= e($prato['nome']) ?>"
            class="detail-image"
        >
    </div>
    <article class="panel">
        <p class="eyebrow"><?= e($prato['categoria']) ?></p>
        <h1><?= e($prato['nome']) ?></h1>
        <p><?= e($prato['descricao']) ?></p>
        <div class="price-row large">
            <strong><?= formatMoney($precoAtual) ?></strong>
            <span><?= (int) $prato['tempo_preparo'] ?> min</span>
        </div>

        <h2>Ingredientes</h2>
        <?php if ($ingredientes): ?>
            <ul class="tag-list">
                <?php foreach ($ingredientes as $ingrediente): ?>
                    <li>
                        <?= e($ingrediente['nome']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Ingredientes ainda não vinculados.</p>
        <?php endif; ?>

        <?php if (hasRole('user')): ?>
            <form method="post" class="stack-form">
                <?= csrfField() ?>
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="prato_id" value="<?= (int) $prato['id'] ?>">
                <label>
                    Quantidade
                    <input type="number" name="quantidade" value="1" min="1" max="99">
                </label>
                <label>
                    Observação do item
                    <textarea name="obs_item" rows="3" placeholder="Ex: sem cebola, molho à parte"></textarea>
                </label>
                <button class="btn btn-primary" type="submit">Adicionar ao carrinho</button>
            </form>
        <?php elseif (!isLoggedIn()): ?>
            <a class="btn btn-primary" href="login.php">Entrar para adicionar ao carrinho</a>
        <?php endif; ?>
    </article>
</section>
<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
