<?php
declare(strict_types=1);
/**
 * @var array $prato
 */
$isGarcom = hasRole('garcom');
$clienteId = getActiveWaiterClientId();
$precoAtual = $prato['preco_promocional'] !== null
    ? (float) $prato['preco_promocional']
    : (float) $prato['preco'];

$imgSrc = $isGarcom
    ? '../public/imagens/' . ($prato['imagem'] ?: 'placeholder.svg')
    : publicImagePath($prato['imagem'] ?? null);
$imgErrorSrc = $isGarcom
    ? '../public/imagens/placeholder.svg'
    : 'admin/public/imagens/placeholder.svg';
?>
<article class="card dish-card">
    <?php if (!$isGarcom): ?>
        <a href="ver_prato.php?id=<?= (int) $prato['id'] ?>" class="dish-image-link">
    <?php endif; ?>
        <img
            src="<?= e($imgSrc) ?>"
            onerror="this.src='<?= e($imgErrorSrc) ?>'"
            alt="<?= e($prato['nome']) ?>"
            class="dish-image"
        >
    <?php if (!$isGarcom): ?>
        </a>
    <?php endif; ?>

    <div class="card-content menu-card-content">
        <div class="dish-meta">
            <span><?= e($prato['categoria']) ?></span>
            <span><?= (int) $prato['tempo_preparo'] ?> min</span>
        </div>

        <h2 class="dish-title"><?= e($prato['nome']) ?></h2>
        <p class="dish-description"><?= e($prato['descricao']) ?></p>

        <?php if (!$isGarcom): ?>
            <div class="dish-card-footer">
                <div class="price-row dish-price-row">
                    <?php if ($prato['preco_promocional'] !== null): ?>
                        <span class="old-price"><?= formatMoney($prato['preco']) ?></span>
                    <?php endif; ?>
                    <strong><?= formatMoney($precoAtual) ?></strong>
                </div>

                <div class="card-actions dish-card-actions">
                    <?php if (hasRole('user')): ?>
                        <a class="btn btn-primary" href="ver_prato.php?id=<?= (int) $prato['id'] ?>">Adicionar</a>
                    <?php elseif (!isLoggedIn()): ?>
                        <a class="btn btn-primary" href="login.php">Entrar para pedir</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="price-row">
                <?php if ($prato['preco_promocional'] !== null): ?>
                    <span class="old-price"><?= formatMoney($prato['preco']) ?></span>
                <?php endif; ?>
                <strong><?= formatMoney($precoAtual) ?></strong>
            </div>

            <form method="post" class="stack-form">
                <?= csrfField() ?>
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="cliente_id" value="<?= (int) $clienteId ?>">
                <input type="hidden" name="prato_id" value="<?= (int) $prato['id'] ?>">

                <label>
                    Quantidade
                    <input class="small-input" type="number" name="quantidade" value="1" min="1" max="99">
                </label>

                <label>
                    Observação
                    <textarea name="obs_item" rows="2" placeholder="Ex: sem cebola"></textarea>
                </label>

                <button class="btn btn-primary" type="submit">Adicionar ao menu</button>
            </form>
        <?php endif; ?>
    </div>
</article>
