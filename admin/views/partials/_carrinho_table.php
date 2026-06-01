<?php
declare(strict_types=1);
/**
 * @var array $itens
 * @var float $total
 */
$isGarcom = hasRole('garcom');
?>
<form method="post" class="panel">
    <?= csrfField() ?>
    <input type="hidden" name="acao" value="atualizar">

    <div class="table-wrap">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Prato</th>
                    <th>Obs.</th>
                    <th>Preço</th>
                    <th>Qtd.</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e($item['nome']) ?></strong>
                        </td>
                        <td><?= e($item['obs_item'] ?: '-') ?></td>
                        <td class="preco-unit-cell" data-preco="<?= (float) $item['preco_unit'] ?>"><?= formatMoney($item['preco_unit']) ?></td>
                        <td>
                            <div class="qty-control">
                                <button type="button" class="qty-btn qty-minus">-</button>
                                <input class="qty-input" type="number" min="0" max="99" name="quantidades[<?= (int) $item['id'] ?>]" value="<?= (int) $item['quantidade'] ?>" readonly>
                                <button type="button" class="qty-btn qty-plus">+</button>
                            </div>
                        </td>
                        <td class="subtotal-cell"><?= formatMoney($item['subtotal']) ?></td>
                        <td>
                            <button
                                class="btn btn-danger"
                                type="submit"
                                name="item_id"
                                value="<?= (int) $item['id'] ?>"
                                <?php if (!$isGarcom): ?>
                                    formaction="carrinho.php"
                                    formmethod="post"
                                <?php endif; ?>
                                onclick="this.form.acao.value='remover'"
                            >Remover</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="cart-summary" style="justify-content:space-between;">
        <strong>Valor Total</strong>
        <strong class="total-value"><?= formatMoney($total) ?></strong>

        <?php if ($isGarcom): ?>
            <button class="btn btn-danger" type="submit" onclick="this.form.acao.value='limpar'">Limpar menu</button>
        <?php endif; ?>
    </div>
</form>
<script>
(function() {
  function formatMoney(value) {
    return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('tbody tr').forEach(function(row) {
      const precoCell = row.querySelector('.preco-unit-cell');
      const input = row.querySelector('.qty-input');
      if (precoCell && input) {
        const preco = parseFloat(precoCell.getAttribute('data-preco')) || 0;
        const qty = parseInt(input.value) || 0;
        const subtotal = preco * qty;
        
        const subtotalCell = row.querySelector('.subtotal-cell');
        if (subtotalCell) {
          subtotalCell.textContent = formatMoney(subtotal);
        }
        total += subtotal;
      }
    });

    const totalEl = document.querySelector('.total-value');
    if (totalEl) {
      totalEl.textContent = formatMoney(total);
    }
  }

  let debounceTimer = null;
  function triggerUpdate(input) {
    const qty = parseInt(input.value) || 0;
    
    if (qty === 0) {
      clearTimeout(debounceTimer);
      sendUpdateRequest(input, true);
      return;
    }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function() {
      sendUpdateRequest(input, false);
    }, 500);
  }

  function sendUpdateRequest(input, shouldReload) {
    const form = input.closest('form');
    const formData = new FormData(form);
    formData.set('acao', 'atualizar');
    
    fetch(form.action || '', {
      method: 'POST',
      body: formData,
    }).then(function(response) {
      if (shouldReload) {
        location.reload();
      }
    });
  }

  document.querySelectorAll('.qty-control').forEach(function(control){
    const minusBtn = control.querySelector('.qty-minus');
    const plusBtn = control.querySelector('.qty-plus');
    const input = control.querySelector('.qty-input');

    minusBtn.addEventListener('click', function(){
      let val = parseInt(input.value) || 0;
      if (val > 0) {
        input.value = val - 1;
        updateTotal();
        triggerUpdate(input);
      }
    });

    plusBtn.addEventListener('click', function(){
      let val = parseInt(input.value) || 0;
      if (val < 99) {
        input.value = val + 1;
        updateTotal();
        triggerUpdate(input);
      }
    });
  });
})();
</script>
