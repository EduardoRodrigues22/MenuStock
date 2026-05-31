<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Prato.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Ingrediente.php';

requireRole('admin');

$pratoModel = new Prato();
$ingredienteModel = new Ingrediente();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$prato = $pratoModel->buscarPorId($id);

if (!$prato) {
    flash('error', 'Prato não encontrado.');
    redirect('gerenciar_pratos.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    try {
        $pratoModel->editar($id, $_POST, $_FILES['imagem'] ?? null);
        flash('success', 'Prato atualizado.');
        redirect('gerenciar_pratos.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('editar_prato.php?id=' . $id);
    }
}

$categorias = (new Categoria())->listar();
$ingredientes = $ingredienteModel->listar();
$ingredientesDoPrato = $ingredienteModel->listarPorPrato($id);
$vinculados = [];
foreach ($ingredientesDoPrato as $item) {
    $vinculados[(int) $item['id']] = $item;
}

$pageTitle = 'Editar prato';
require_once __DIR__ . '/../config/header_admin.php';
?>
<div class="centered-container">
    <section class="page-heading">
        <h1>Editar prato</h1>
        <p>Atualize informações, disponibilidade e ingredientes.</p>
    </section>

<form method="post" enctype="multipart/form-data" class="panel stack-form wide-form">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= (int) $prato['id'] ?>">
    <label>
        Nome
        <input type="text" name="nome" required maxlength="100" value="<?= e($prato['nome']) ?>">
    </label>
    <label>
        Categoria
        <select name="categoria_id" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>" <?= (int) $prato['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>>
                    <?= e($categoria['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Descrição
        <textarea name="descricao" rows="4"><?= e($prato['descricao']) ?></textarea>
    </label>
    <div class="form-grid">
        <label>
            Preço
            <input type="number" name="preco" min="0" step="0.01" required value="<?= e($prato['preco']) ?>">
        </label>
        <label>
            Tempo de preparo (min)
            <input type="number" name="tempo_preparo" min="0" step="1" value="<?= (int) $prato['tempo_preparo'] ?>">
        </label>
        <label class="check-row">
            <input type="checkbox" name="disponivel" value="1" <?= (int) $prato['disponivel'] === 1 ? 'checked' : '' ?>>
            Disponível na vitrine
        </label>
    </div>
    <label>
        Nova imagem
        <input type="file" name="imagem" accept="image/png,image/jpeg,image/webp,image/gif">
    </label>
    <fieldset>
        <legend>Ingredientes</legend>
        <div class="ingredient-grid">
            <?php foreach ($ingredientes as $ingrediente): ?>
                <?php $marcado = isset($vinculados[(int) $ingrediente['id']]); ?>
                <label class="ingredient-row">
                    <input type="checkbox" name="ingredientes[]" value="<?= (int) $ingrediente['id'] ?>" <?= $marcado ? 'checked' : '' ?>>
                    <span><?= e($ingrediente['nome']) ?></span>
                    <input type="number" name="quantidades[<?= (int) $ingrediente['id'] ?>]" min="0" step="0.01" value="<?= e($marcado ? $vinculados[(int) $ingrediente['id']]['quantidade'] : 1) ?>" aria-label="Quantidade">
                    <small><?= e($ingrediente['unidade']) ?></small>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>
    <div class="form-actions">
        <a class="btn btn-ghost" href="gerenciar_pratos.php">Cancelar</a>
        <button class="btn btn-primary" type="submit">Salvar alterações</button>
    </div>
</form>
</div>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
