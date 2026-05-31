<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Categoria.php';

requireRole('admin');

$categoriaModel = new Categoria();

handleGenericPostCRUD(
    $categoriaModel,
    'Categoria cadastrada.',
    'Categoria atualizada.',
    'gerenciar_categorias.php'
);

$categorias = $categoriaModel->listar();
$pageTitle = 'Categorias';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Categorias</h1>
    <p>Organize a ordem de exibição do cardápio.</p>
</section>

<section class="split-layout categorias-layout">
    <form method="post" class="panel stack-form categoria-create-form">
        <?= csrfField() ?>

        <input type="hidden" name="acao" value="cadastrar">

        <h2>Nova categoria</h2>

        <label>
            Nome
            <input type="text" name="nome" required maxlength="100">
        </label>

        <label>
            Descrição
            <textarea name="descricao" rows="3"></textarea>
        </label>

        <label>
            Ordem
            <input type="number" name="ordem_exibicao" value="0" min="0">
        </label>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Cadastrar categoria</button>
        </div>
    </form>

    <div class="panel table-wrap">
        <table class="categorias-table">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Ordem</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <?php $formId = 'categoria-form-' . (int) $categoria['id']; ?>
                    <tr>
                        <td>
                            <form id="<?= e($formId) ?>" method="post"></form>

                            <input form="<?= e($formId) ?>" type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input form="<?= e($formId) ?>" type="hidden" name="acao" value="editar">
                            <input form="<?= e($formId) ?>" type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">

                            <input form="<?= e($formId) ?>" type="text" name="nome" value="<?= e($categoria['nome']) ?>" required>
                        </td>

                        <td>
                            <input form="<?= e($formId) ?>" type="text" name="descricao" value="<?= e($categoria['descricao']) ?>">
                        </td>

                        <td>
                            <input
                                form="<?= e($formId) ?>"
                                type="number"
                                name="ordem_exibicao"
                                value="<?= (int) $categoria['ordem_exibicao'] ?>"
                                min="0"
                                class="small-input"
                            >
                        </td>

                        <td>
                            <div class="categoria-actions">
                                <button form="<?= e($formId) ?>" class="btn btn-secondary" type="submit">Salvar</button>

                                <form method="post" action="../controllers/deletar_categoria.php" onsubmit="return confirm('Excluir esta categoria?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (!$categorias): ?>
                    <tr>
                        <td colspan="4">Nenhuma categoria cadastrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
