<?php

/**
 * Customer information table for email.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/addify/rfq/quote/quote-totals-table.php.
 *
 * @package addify-request-a-quote
 * @version 1.6.0
 */

defined('ABSPATH') || exit;

if (!isset($af_quote)) {
	$af_quote = new AF_R_F_Q_Quote();
}

$price_display    = 'yes' === get_option('afrfq_enable_pro_price') ? true : false;
$of_price_display = 'yes' === get_option('afrfq_enable_off_price') ? true : false;
$tax_display      = 'yes' === get_option('afrfq_enable_tax') ? true : false;

$quote_totals = $af_quote->get_calculated_totals(wc()->session->get('quotes'));

$quote_subtotal = isset($quote_totals['_subtotal']) ? $quote_totals['_subtotal'] : 0;
$vat_total      = isset($quote_totals['_tax_total']) ? $quote_totals['_tax_total'] : 0;
$quote_total    = isset($quote_totals['_total']) ? $quote_totals['_total'] : 0;
$offered_total  = isset($quote_totals['_offered_total']) ? $quote_totals['_offered_total'] : 0;

if ($price_display || $of_price_display) : ?>



	<table cellspacing="0" class="shop_table shop_table_responsive table_quote_totals">




		<!-- <?php if ($price_display) : ?>
			<tr class="cart-subtotal">
				<th><?php esc_html_e('Subtotal(standard)', 'addify_rfq'); ?></th>
				<td data-title="<?php esc_attr_e('Subtotal(standard)', 'addify_rfq'); ?>"><?php echo wp_kses_post(wc_price($quote_subtotal)); ?></td>
			</tr>
		<?php endif; ?> -->

		<?php if ($of_price_display) : ?>
			<tr class="cart-subtotal offered">
				<th><?php esc_html_e('Offered Price Subtotal', 'addify_rfq'); ?></th>
				<td data-title="<?php esc_attr_e('Offered Price Subtotal', 'addify_rfq'); ?>"><?php echo wp_kses_post(wc_price($offered_total)); ?></td>
			</tr>
		<?php endif; ?>



		<?php
		if (wc_tax_enabled() && $tax_display) :
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
				/* translators: %s location. */
				$estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
			}

		?>
			<tr class="tax-rate">
				<th><?php echo esc_html__('Vat(standard)', 'addify_rfq') . wp_kses_post($estimated_text); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?></th>
				<td data-title="<?php echo esc_html__('Vat(standard)', 'addify_rfq'); ?>"><?php echo wp_kses_post(wc_price($vat_total)); ?></td>
			</tr>
		<?php endif; ?>




		<td style="	 height: 120px; margin-bottom:100px; display:flex">
			<table style="left:15%; width:70vw; position:absolute; border-spacing: 1;border-collapse: collapse;background: white;border-radius: 6px;overflow: hidden;">
				<thead>
					<tr style="background: rgba(221,51,51,0.6) ;font-size: 16px;height: 60px;">
						<th style="padding-left: 8px;text-align: left;">Tipo de arrendamiento</th>
						<th style="padding-left: 8px;text-align: left;">12 meses</th>
						<th style="padding-left: 8px;text-align: left;">36 meses</th>
						<th style="padding-left: 8px;text-align: left;">48 meses</th>
						<th style="padding-left: 8px;text-align: left;">total a pagar</th>
					</tr>
					<thead>
					<tbody>
						<tr style="border-bottom: 1px solid #E3F1D5;height: 48px;">
							<td style="padding-left: 8px;text-align: left;">Full Pay Out</td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 12, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 36, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 48, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . $quote_total . " + intereses" ?></td>
						</tr>
						<tr style="border-bottom: 1px solid #E3F1D5;height: 48px;">
							<td style="padding-left: 8px;text-align: left;">Justo a valor de mercado</td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 12, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 36, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . round($quote_total / 48, 2) ?></td>
							<td style="padding-left: 8px;text-align: left;"><?php echo "$" . $quote_total . " + intereses" ?></td>
						</tr>
					</tbody>
			</table>
		</td>



		<!-- 
		<?php if ($price_display) : ?>
			<tr class="order-total">
			 <th><?php esc_html_e('Total(standard)', 'addify_rfq'); ?></th> 
				<td data-title="<?php esc_attr_e('Total', 'addify_rfq'); ?>">Total(standard)<?php echo wp_kses_post(wc_price($quote_total)); ?></td>
			</tr>
		<?php endif; ?>
 -->
	</table>

<?php endif; ?>