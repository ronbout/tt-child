<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<p><?php esc_html_e( 'We have finished processing your order.', 'woocommerce' ); ?></p>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
$GLOBALS['completed_email_flag'] = true;
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );


/**
 * check if any order item is bookable.  If it is, 
 * include the needed css for the display, including svg
 * 
 */
$booking_flg = false;
$items = $order->get_items();
foreach ( $items as $item_id => $item ) {
	$product       = $item->get_product();
	if ($product->get_meta('booking')) {
		$booking_flg = true;
		break;
	}
}
 
if ($booking_flg) {
	?>
	<style>
		.ot-powered-by {
			height: 24px;
			background-image:  url("https://www.thetaste.ie/wp-content/uploads/2022/05/ot-email-logo.png");

			background-position: center;
			background-repeat: no-repeat;
			background-size: 107px 24px;
			margin-top: 12px; 
		}

		a.booking-link {
			display: block;
			font-size: 16px;
			text-align: center;
			text-decoration: none;
			color: #ffffff;
			font-weight: bold;
			width: 100%;
			height: 100%;
			padding-top: 18px;
		}
		.qr-container table {
			width: 100%;
			height: 100%;
		}

		.qr-container table tr td {
			vertical-align: middle;
			text-align: center;
			height: 100%;
			padding: 0 !important;
		}

		.ot-button {
			color: #ffffff;
			background-color: rgb(218, 55, 67);
			border: 1px solid rgb(218, 55, 67);
			cursor: pointer;
			display: block;
			font-weight: bold;
			padding: 14px 0 15px;
			padding: 0;
			text-align: center;
			text-decoration: none;
			width: 100%; 
			height: 45px;
			border-radius: 0 0 2px 2px;
		}
		.ot-button:focus, .ot-button:hover {
			background-color:  rgb(225, 91, 100);
			border: 1px solid rgb(225, 91, 100);
			color:  #ffffff;
		}
	</style>
	<?php

}


/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
