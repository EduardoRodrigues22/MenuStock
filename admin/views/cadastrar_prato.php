<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Prato.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Ingrediente.php';

requireRole('admin');

$pratoModel = new Prato();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfOrFail();
    try {
        $pratoModel->cadastrar($_POST, $_FILES['imagem'] ?? null);
        flash('success', 'Prato cadastrado.');
        redirect('gerenciar_pratos.php');
    } catch (Throwable $exception) {
        flash('error', $exception->getMessage());
        redirect('cadastrar_prato.php');
    }
}

$categorias = (new Categoria())->listar();
$ingredientes = (new Ingrediente())->listar();
$pageTitle = 'Novo prato';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Novo prato</h1>
    <p>Cadastre informações, imagem e ingredientes do prato.</p>
</section>

<form method="post" enctype="multipart/form-data" class="panel stack-form wide-form">
    <?= csrfField() ?>
    <label>
        Nome
        <input type="text" name="nome" required maxlength="100">
    </label>
    <label>
        Categoria
        <select name="categoria_id" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= (int) $categoria['id'] ?>"><?= e($categoria['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Descrição
        <textarea name="descricao" rows="4"></textarea>
    </label>
    <div class="form-grid">
        <label>
            Preço
            <input type="number" name="preco" min="0" step="0.01" required>
        </label>
        <label>
            Tempo de preparo (min)
            <input type="number" name="tempo_preparo" min="0" step="1" value="0">
        </label>
        <label class="check-row">
            <input type="checkbox" name="disponivel" value="1" checked>
            Disponível na vitrine
        </label>
    </div>
    <label>
        Imagem
        <input type="file" name="imagem" accept="image/png,image/jpeg,image/webp,image/gif">
    </label>
    <fieldset>
        <legend>Ingredientes</legend>
        <div class="ingredient-grid">
            <?php foreach ($ingredientes as $ingrediente): ?>
                <label class="ingredient-row">
                    <input type="checkbox" name="ingredientes[]" value="<?= (int) $ingrediente['id'] ?>">
                    <span><?= e($ingrediente['nome']) ?></span>
                    <input type="number" name="quantidades[<?= (int) $ingrediente['id'] ?>]" min="0" step="0.01" value="1" aria-label="Quantidade">
                    <small><?= e($ingrediente['unidade']) ?></small>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>
    <div class="form-actions">
        <a class="btn btn-ghost" href="gerenciar_pratos.php">Cancelar</a>
        <button class="btn btn-primary" type="submit">Salvar prato</button>
    </div>
</form>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
