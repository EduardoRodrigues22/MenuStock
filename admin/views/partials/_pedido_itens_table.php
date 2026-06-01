<?php
declare(strict_types=1);
/**
 * @var array $itens
 */
?>
<div class="table-wrap">
    <table class="pedidos-card-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qtd.</th>
                <th>Preço</th>
                <th>Obs.</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= e($item['nome']) ?></td>
                    <td><?= (int) $item['quantidade'] ?></td>
                    <td><?= formatMoney($item['preco_unit']) ?></td>
                    <td><?= e($item['obs_item'] ?: '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
