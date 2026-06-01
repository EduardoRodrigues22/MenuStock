<?php
declare(strict_types=1);

require_once __DIR__ . '/admin/config/auth.php';
require_once __DIR__ . '/admin/models/Prato.php';
require_once __DIR__ . '/admin/models/Categoria.php';

$pratoModel = new Prato();
$categoriaModel = new Categoria();

$busca = trim((string) ($_GET['q'] ?? ''));

$categoriaId = isset($_GET['categoria']) && $_GET['categoria'] !== ''
    ? (int) $_GET['categoria']
    : null;

$categorias = $categoriaModel->listar();

$pratos = $pratoModel->listarDisponiveis(
    $busca ?: null,
    $categoriaId
);

$pageTitle = 'Cardápio';

require_once __DIR__ . '/admin/config/public_header.php';
?>

<section class="hero">
    <div>
        <p class="eyebrow">Restaurante MenuStock</p>

        <h1>Menu Principal</h1>

        <p>Escolha seus pratos e monte o carrinho.</p>
    </div>

    <?php if (!$publicUser || ($publicUser['tipo'] ?? '') !== 'garcom'): ?>
        <a class="btn btn-secondary" href="reserva.php">
            Reservar mesa
        </a>
    <?php endif; ?>
</section>

<section class="toolbar">
    <form class="filters" method="get" action="index.php">

        <label>
            Buscar

            <input
                type="search"
                name="q"
                value="<?= e($busca) ?>"
                placeholder="Nome, categoria ou descrição"
            >
        </label>

        <label>
            Categoria

            <select name="categoria">
                <option value="">Todas</option>

                <?php foreach ($categorias as $categoria): ?>
                    <option
                        value="<?= (int) $categoria['id'] ?>"
                        <?= $categoriaId === (int) $categoria['id'] ? 'selected' : '' ?>
                    >
                        <?= e($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button class="btn btn-primary" type="submit">
            Filtrar
        </button>

        <a class="btn btn-ghost" href="index.php">
            Limpar
        </a>
    </form>
</section>

<section class="grid menu-grid menu-card-grid">

    <?php if (!$pratos): ?>
        <div class="empty-state">
            Nenhum prato disponível para os filtros informados.
        </div>
    <?php endif; ?>

    <?php foreach ($pratos as $prato): ?>
        <?php require __DIR__ . '/admin/views/partials/_dish_card.php'; ?>
    <?php endforeach; ?>

</section>

<?php require_once __DIR__ . '/admin/config/public_footer.php'; ?>
