<?php

/**
 * Quote details in Meta box
 *
 * It shows the details of quotes items in meta box.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined('ABSPATH') || exit;

?>
<div id="addify_quote_items_container">
	<table cellpadding="0" cellspacing="0" id="addify_quote_items_table" class="woocommerce_order_items addify_quote_items">

		<thead>
			<tr>
				<th class="thumb sortable" data-sort="string-ins"><?php esc_html_e('Thumbnail', 'addify_rfq'); ?></th>
				<th class="item sortable" data-sort="string-ins"><?php esc_html_e('Item', 'addify_rfq'); ?></th>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e('Cost', 'addify_rfq'); ?></th>
				<th class="item_cost sortable" data-sort="float"><?php esc_html_e('Off. Price', 'addify_rfq'); ?></th>
				<th class="quantity sortable" data-sort="int"><?php esc_html_e('Qty', 'addify_rfq'); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e('Subtotal', 'addify_rfq'); ?></th>
				<th class="line_cost sortable" data-sort="float"><?php esc_html_e('Off. Subtotal', 'addify_rfq'); ?></th>
				<th class="line_actions"></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action('addify_rfq_order_details_before_order_table_items', $post);

			$offered_price_subtotal = 0;
			foreach ((array) $quote_contents as $item_id => $item) {

				if (isset($item['data'])) {

					$product = $item['data'];
				} else {

					continue;
				}

				if (!is_object($product)) {
					continue;
				}

				$price         = empty($item['addons_price']) ? $product->get_price() : $item['addons_price'];
				$price         = empty($item['role_base_price']) ? $price : $item['role_base_price'];
				$qty_display   = $item['quantity'];
				$offered_price = isset($item['offered_price']) ? floatval($item['offered_price']) : $price;
				$product_link  = $product ? admin_url('post.php?post=' . $product->get_id() . '&action=edit') : '';
				$thumbnail     = $product ? apply_filters('woocommerce_admin_order_item_thumbnail', $product->get_image('thumbnail', array('title' => ''), false), $item_id, $item) : '';

				$offered_price_subtotal += floatval($offered_price) * intval($qty_display);

			?>
				<tr class="item" data-order_item_id="<?php echo esc_attr($item_id); ?>">
					<td class="thumb">
						<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post($thumbnail) . '</div>'; ?>
					</td>

					<td class="woocommerce-table__product-name product-name">
						<?php
						$is_visible        = $product && $product->is_visible();
						$product_permalink = apply_filters('addify_rfq_order_item_permalink', $is_visible ? $product->get_permalink($item) : '', $item, $post);

						echo wp_kses_post(apply_filters('addify_rfq_order_item_name', $product_permalink ? sprintf('<a href="%s">%s</a>', $product_permalink, $product->get_name()) : $product->get_name(), $item, $is_visible)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						do_action('addify_rfq_order_item_meta_start', $item_id, $item, $post, false);

						// Meta data.
						echo wp_kses_post(wc_get_formatted_cart_item_data($item)); // phpcs:ignore WordPress.Security.EscapeOutput

						do_action('addify_rfq_order_item_meta_end', $item_id, $item, $post, false);
						?>
						<br>
						<?php
						echo wp_kses_post('<div class="wc-quote-item-sku"><strong>' . esc_html__('SKU:', 'addify_rfq') . '</strong> ' . esc_html($product->get_sku()) . '</div>');
						?>
					</td>
					<td class="woocommerce-table__product-total product-total">
						<?php echo wp_kses_post(wc_price($price)); ?>
					</td>
					<td class="woocommerce-table__product-total product-total">
						<input type="number" class="input-text offered-price-input text" step="any" name="offered_price[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($offered_price); ?>">
					</td>

					<td>
						<input type="number" class="input-text quote-qty-input text" min="1" name="quote_qty[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item['quantity']); ?>">
					</td>

					<td class="woocommerce-table__product-total product-total">
						<?php echo wp_kses_post(wc_price($price * $qty_display)); ?>
					</td>

					<td class="woocommerce-table__product-total product-total">
						<?php echo wp_kses_post(wc_price($offered_price * $qty_display)); ?>
					</td>
					<td>
						<a class="delete-quote-item tips" title="Delete <?php echo esc_html($product->get_name()); ?>" data-quote_item_id="<?php echo esc_attr($item_id); ?>"></a>
					</td>

				</tr>
			<?php
			}
			?>

			<?php

			do_action('addify_rfq_order_details_after_order_table_items', $post);
			?>
		</tbody>
	</table>
	<table cellpadding="0" cellspacing="0" id="addify_quote_total_table" class="woocommerce_order_items addify_quote_items_total">
		<?php
		foreach ($quote_totals as $key => $total) {

			$label = '';
			switch ($key) {
				case '_subtotal':
					$label = 'Subtotal (Standard)';
					break;
				case '_tax_total':
					$label = 'Vat (Standard)';
					break;
				case '_total':
					$label = 'Total (Standard)';
					break;
				case '_offered_total':
					$label = 'Offered Subtotal';
					$total = $offered_price_subtotal;
					break;
				case '_shipping_total':
					$label = 'Shipping Cost';
					$total = $total;
					break;
				default:
					$label = '';
					break;
			}

			if (empty($label)) {
				continue;
			}

			if ('_tax_total' == $key) {
				continue;
			}
			if ('_shipping_total' == $key) {
		?>
				<tr>
					<td colspan=""><?php echo esc_html__('Shipping Cost', 'addify_rfq'); ?></td>
					<td colspan="2" class="afrfq_shipping_cost">
						<input type="number" step="any" min="0" name="afrfq_shipping_cost" value="<?php echo esc_html($total); ?>">
						</th>
				</tr>
			<?php
				continue;
			}

			?>
			<tr>
				<td scope="row"><?php echo esc_html($label); ?></td>
				<th colspan="2"><?php echo wp_kses_post(wc_price($total)); ?></th>
			</tr>
		<?php
		}
		?>

		<!-- Aqui se inserta los calculos del tipo de arrendamiento de todos los productos -->
		<!-- Por el momento, solo divide el total entre la cantida de meses a arrendar, el resultado se redondea a 2 decimas-->

		<!-- Calculo de Full Pay Out -->
		<tr>
			<td scope="row">Full Pay Out</td>
			<th colspan="2"></th>
		</tr>

		<tr>
			<td scope="row">12 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 12, 2) ?></th>
		</tr>

		<tr>
			<td scope="row">36 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 36, 2) ?></th>
		</tr>

		<tr>
			<td scope="row">48 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 48, 2) ?></th>
		</tr>

		<!-- Calculo de justo a valor de mercado -->
		<tr>
			<td scope="row">Justo a valor de mercado</td>
			<th colspan="2"></th>
		</tr>

		<tr>
			<td scope="row">12 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 12, 2) ?></th>
		</tr>

		<tr>
			<td scope="row">36 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 36, 2) ?></th>
		</tr>

		<tr>
			<td scope="row">48 meses</td>
			<th colspan="2"><?php echo "$" . round($total / 48, 2) ?></th>
		</tr>
		<tr>
			<td colspan="3"><?php echo esc_html__('Note: Tax/Vat will be calculated on quote conversion to order but it is visible to customers.', 'addify_rfq'); ?></th>
		</tr>

	</table>
</div>