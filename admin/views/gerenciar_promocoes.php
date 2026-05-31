<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Promocao.php';
require_once __DIR__ . '/../models/Prato.php';

requireRole('admin');

$promocaoModel = new Promocao();
$pratoModel = new Prato();
$editarId = (int) ($_GET['editar'] ?? 0);
$promocaoEditando = $editarId > 0 ? $promocaoModel->buscarPorId($editarId) : null;

handleGenericPostCRUD(
    $promocaoModel,
    'Promoção cadastrada.',
    'Promoção atualizada.',
    'gerenciar_promocoes.php'
);

$promocoes = $promocaoModel->listar();
$pratos = $pratoModel->listar();
$pageTitle = 'Promoções';
require_once __DIR__ . '/../config/header_admin.php';
?>
<section class="page-heading">
    <h1>Promoções</h1>
    <p>Crie descontos com validade automática para pratos específicos.</p>
</section>

<section class="split-layout promocoes-layout">
    <form method="post" class="panel stack-form">
        <?= csrfField() ?>
        <input type="hidden" name="acao" value="<?= $promocaoEditando ? 'editar' : 'cadastrar' ?>">
        <?php if ($promocaoEditando): ?>
            <input type="hidden" name="id" value="<?= (int) $promocaoEditando['id'] ?>">
        <?php endif; ?>
        <h2><?= $promocaoEditando ? 'Editar promoção' : 'Nova promoção' ?></h2>
        <label>
            Prato
            <select name="prato_id" required>
                <?php foreach ($pratos as $prato): ?>
                    <option value="<?= (int) $prato['id'] ?>" <?= $promocaoEditando && (int) $promocaoEditando['prato_id'] === (int) $prato['id'] ? 'selected' : '' ?>>
                        <?= e($prato['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Nome
            <input type="text" name="nome" required value="<?= e($promocaoEditando['nome'] ?? '') ?>">
        </label>
        <div class="form-grid">
            <label>
                Tipo
                <select name="tipo">
                    <?php foreach (['desconto_percentual' => 'Percentual', 'desconto_fixo' => 'Valor fixo', 'combo' => 'Combo'] as $value => $label): ?>
                        <option value="<?= e($value) ?>" <?= ($promocaoEditando['tipo'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Valor
                <input type="number" name="valor" min="0" step="0.01" required value="<?= e($promocaoEditando['valor'] ?? '') ?>">
            </label>
        </div>
        <div class="form-grid">
            <label>
                Início
                <input type="date" name="data_inicio" required value="<?= e($promocaoEditando['data_inicio'] ?? date('Y-m-d')) ?>">
            </label>
            <label>
                Fim
                <input type="date" name="data_fim" required value="<?= e($promocaoEditando['data_fim'] ?? date('Y-m-d', strtotime('+30 days'))) ?>">
            </label>
        </div>
        <label class="check-row">
            <input type="checkbox" name="ativa" value="1" <?= !$promocaoEditando || (int) $promocaoEditando['ativa'] === 1 ? 'checked' : '' ?>>
            Ativa
        </label>
        <div class="form-actions">
            <?php if ($promocaoEditando): ?>
                <a class="btn btn-ghost" href="gerenciar_promocoes.php">Cancelar edição</a>
            <?php endif; ?>
            <button class="btn btn-primary" type="submit">Salvar promoção</button>
        </div>
    </form>

    <div class="panel table-wrap">
        <table class="promocoes-table">
            <thead>
                <tr>
                    <th>Promoção</th>
                    <th>Prato</th>
                    <th>Período</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promocoes as $promocao): ?>
                    <tr>
                        <td>
                            <strong><?= e($promocao['nome']) ?></strong>
                        </td>
                        <td><?= e($promocao['prato']) ?></td>
                        <td><?= date('d/m/Y', strtotime((string) $promocao['data_inicio'])) ?> a <?= date('d/m/Y', strtotime((string) $promocao['data_fim'])) ?></td>
                        <td><?= e($promocao['tipo']) ?> - <?= e($promocao['valor']) ?></td>
                        <td><span class="status <?= (int) $promocao['ativa'] === 1 ? 'status-confirmada' : 'status-cancelada' ?>"><?= (int) $promocao['ativa'] === 1 ? 'Ativa' : 'Inativa' ?></span></td>
                        <td>
                            <div class="inline-form promocao-actions">
                                <a class="btn btn-ghost" href="gerenciar_promocoes.php?editar=<?= (int) $promocao['id'] ?>">Editar</a>
                                <form method="post" action="../controllers/deletar_promocao.php" onsubmit="return confirm('Excluir esta promoção?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= (int) $promocao['id'] ?>">
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$promocoes): ?>
                    <tr><td colspan="6">Nenhuma promoção cadastrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once __DIR__ . '/../config/footer_admin.php'; ?>
