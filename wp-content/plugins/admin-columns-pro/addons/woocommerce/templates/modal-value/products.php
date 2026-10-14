<table class="ac-table-items -clean -products">
	<thead>
	<tr>
		<th class="col-image"><span class="dashicons dashicons-format-image"></span></th>
		<th class="col-product"><?= __('Product', 'codepress-admin-columns') ?></th>
		<th class="col-sku"><?= __('SKU', 'codepress-admin-columns') ?></th>
		<th class="col-sku"><?= __('Stock', 'codepress-admin-columns') ?></th>
		<th class="col-price"><?= __('Price', 'codepress-admin-columns') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
    foreach ($this->items as $item) : ?>

		<tr>
			<td class="col-image">
				<?= $item['image'] ?>
			</td>
			<td class="col-product">
				<div class="col-product__name">
					<?= $item['title'] ?>
				</div>
			</td>
			<td class="col-sku">
				<?= $item['sku'] ?: '-'; ?>
			</td>
			<td class="col-stock">
				<?= $item['stock'] ? sprintf('× %d', $item['stock']) : '-'; ?>
			</td>
			<td class="col-price">
				<?= $item['price'] ?: '-'; ?>
			</td>
		</tr>

	<?php
    endforeach; ?>
	</tbody>
	<?php
    if ($this->message) : ?>
		<tfoot>
		<tr>
			<td colspan="5">
				<?= $this->message ?>
			</td>
		</tr>
		</tfoot>
	<?php
    endif; ?>
</table>