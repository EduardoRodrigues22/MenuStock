<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Ingrediente.php';

requireRole('admin');

$ingredienteModel = new Ingrediente();

handleGenericPostCRUD(
    $ingredienteModel,
    'Ingrediente cadastrado.',
    'Ingrediente atualizado.',
    'gerenciar_ingredientes.php'
);

$ingredientes = $ingredienteModel->listar();
$pageTitle = 'Ingredientes';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Ingredientes</h1>
    <p>Controle os ingredientes comuns usados nos pratos.</p>
</section>

<section class="split-layout ingredientes-layout">
    <form method="post" class="panel stack-form">
        <?= csrfField() ?>
        <input type="hidden" name="acao" value="cadastrar">
        <h2>Novo ingrediente</h2>
        <label>
            Nome
            <input type="text" name="nome" required maxlength="100">
        </label>
        <label>
            Unidade
            <input type="text" name="unidade" value="g" maxlength="20">
        </label>
        <button class="btn btn-primary" type="submit">Cadastrar</button>
    </form>

    <div class="panel table-wrap">
        <table class="ingredientes-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Unidade</th>
                    <th style="width: 1%; white-space: nowrap;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingredientes as $ingrediente): ?>
                    <?php $formId = 'ingrediente-form-' . (int) $ingrediente['id']; ?>
                    <tr>
                        <td>
                            <form id="<?= e($formId) ?>" method="post"></form>
                            <input form="<?= e($formId) ?>" type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input form="<?= e($formId) ?>" type="hidden" name="acao" value="editar">
                            <input form="<?= e($formId) ?>" type="hidden" name="id" value="<?= (int) $ingrediente['id'] ?>">
                            <input form="<?= e($formId) ?>" type="text" name="nome" value="<?= e($ingrediente['nome']) ?>" required>
                        </td>
                        <td><input form="<?= e($formId) ?>" type="text" name="unidade" value="<?= e($ingrediente['unidade']) ?>" class="unidade-input"></td>
                        <td style="width: 1%; white-space: nowrap;">
                            <div class="inline-form" style="flex-wrap: nowrap;">
                                <button form="<?= e($formId) ?>" class="btn btn-secondary" type="submit">Salvar</button>
                                <form method="post" action="../controllers/deletar_ingrediente.php" onsubmit="return confirm('Excluir este ingrediente?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $ingrediente['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
