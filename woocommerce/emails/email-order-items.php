<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$text_align  = is_rtl() ? 'right' : 'left';
$margin_side = is_rtl() ? 'left' : 'right';

foreach ( $items as $item_id => $item ) :
	$product       = $item->get_product();
	$sku           = '';
	$purchase_note = '';
	$image         = '';

	$booking 			 = '';


	if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
		continue;
	}

	if ( is_object( $product ) ) {
		$sku           = $product->get_sku();
		$purchase_note = $product->get_purchase_note();
		$image         = $product->get_image( $image_size );

		$booking			 = $product->get_meta('booking');
	}

	?>
	<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
		<?php

		// Show title/image etc.
		if ( $show_image ) {
			echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
		}

		// Product name.
		echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

		// SKU.
		if ( $show_sku && $sku ) {
			echo wp_kses_post( ' (#' . $sku . ')' );
		}

		// allow other plugins to add additional product information here.
		do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );

		wc_display_item_meta(
			$item,
			array(
				'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
			)
		);

		// allow other plugins to add additional product information here.
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );

		?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php
			$qty          = $item->get_quantity();
			$refunded_qty = $order->get_qty_refunded_for_item( $item_id );

			if ( $refunded_qty ) {
				$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
			} else {
				$qty_display = esc_html( $qty );
			}
			echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
			?>
		</td>
		<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
		</td>
	</tr>
	<?php

	if ( $show_purchase_note && $purchase_note ) {

		if (isset($GLOBALS['completed_email_flag'])) {
			/****
			 * 
			 *  if $booking, change the rows and display the button for Book Now.
			 * 
			 * 	otherwise, keep it in the original QR layout.
			 * 
			 * 	Make the Venue msg smaller, removing the strong tag and add italics
			 * 
			 * 
			 */
			?>
				<style>
					.ven-msg {
						font-size: 0.9em;
						font-style: italic;
					}
				</style>
			<?php
			if ($booking) {
				$booking_url = get_site_url(null, "/open-table-booking?order-item-id={$item_id}");
				?>
				<tr class="qr-container">
					<td style="text-align:center">
						<a class="booking-link" href="<?php echo $booking_url ?>" target="_blank">
							<button type="button" class="ot-button ot-dtp-picker-button en">Book Now
							</button>
						</a>
						<div title="Powered By OpenTable" class="ot-powered-by"></div>
					</td>
					<td colspan="2">
						<div  class="ven-msg" style="text-align:center">
					<?php
						$oi_url = get_site_url(null, "/order-item-info?order-item-id={$item_id}");
						echo do_shortcode( "[su_qrcode data='{$oi_url}' size='100' align='center' color='#F73F43' background='#ffffff'] ");
					?>
					</div>
					</td>
				</tr>
				<tr>
					<td class="ven-msg" colspan="3">Venues:  To redeem this item, scan the QR code and follow the directions or visit your Campaign Manager page.</td>
				</tr>
				<?php
			} else {
				?>
				<tr class="qr-container">
					<td class="ven-msg">Venues:  To redeem this item, scan the QR code and follow the directions or visit your Campaign Manager 			page.</td>
					<td colspan="2">
						<div style="text-align:center">
							<?php
								$oi_url = get_site_url(null, "/order-item-info?order-item-id={$item_id}");
								echo do_shortcode( "[su_qrcode data='{$oi_url}' size='100' align='center' color='#F73F43' background='#ffffff'] ");
							?>
						</div>
					</td>
				</tr>
			<?php
			}
		}
		?>
		<tr>
			<td colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php
				echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
				?>
			</td>
		</tr>
		<?php
	}
	?>

<?php endforeach; ?>