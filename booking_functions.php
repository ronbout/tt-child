<?php
/**
 *  booking_functions.php
 * 
 *  Various functions for the booking engine functionality
 * 
 *  Date: 5/26/2022
 *  Author:  Ron Boutilier
 *   
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define('OT_URL_BASE', '//www.opentable.com/widget/reservation/loader');
define('OT_URL_QUERY', '&type=multi&theme=standard&color=1&domain=com&lang=en-US&newtab=false&ot_source=Other&iframe=false');

//* Enqueue booking page Script
add_action('wp_enqueue_scripts', 'booking_load_resources');
function booking_load_resources() {
		wp_enqueue_script( 'booking-js', get_stylesheet_directory_uri() . '/js/open-table.js', array( 'jquery' ), false, true);
}


function get_order_item_card_booking($order_item_info) {
  $product_id = $order_item_info['product_id'];
  $prod_desc = $order_item_info['prod_desc'];
  $order_id = $order_item_info['order_id'];
  $order_item_id = $order_item_info['order_item_id'];
  $redeemed = $order_item_info['redeemed'];
  $status = $order_item_info['order_status'];
  //$payment_id = $order_item_info['payment_id'];
  $redeemable = true;
  if ('wc-completed' != $status || '1' == $redeemed['redeemed'] ) {
		$redeemable = false;
	}
  if ($redeemable) {
    $redeem_status = "Redeemable";
    $status_color = "text-primary";
  } else {
    if ($redeemed) {
      $redeem_status = "Previously Redeemed";
    } else {
      $redeem_status = "Refunded<br><strong>NOT</strong> Redeemable";
    }

    $status_color = "text-danger";
  }
  ob_start();
  ?>
<div class="order-item-tsd-card tsd-card">
  <div class="tsd-card-body">
    <?php echo get_the_post_thumbnail($product_id, array( 200, 200), array('class'=>"tsd-card-img-top booking-card-img")) ?>
    <h5 class="tsd-card-title">Order ID: <?php echo $order_id ?></h5>
    <h6 class="tsd-card-subtitle text-muted"><?php echo $prod_desc ?></h6>
    <p class="tsd-card-text">
      <h4 id="redeem-status-<?php echo $order_item_id ?>" class="<?php echo $status_color ?>"><?php echo $redeem_status ?></h4>
    </p>
  </div>
</div>
<?php
  return ob_get_clean(); 
}


function retrieve_order_booking($order_item_id) {
  global $wpdb;

  $sql = "
    SELECT plook_o.order_id, plook_o.order_item_id, plook_o.product_id, plook_o.product_qty,
      plook_o.date_created AS order_date,	ord_p.post_status AS order_status, 
      prod_p.post_title as prod_desc, ven.venue_id, ven.name AS venue_name, poix.payment_id, 
      wcoi.downloaded AS redeemed, prod_pm.meta_value as booking_id
    FROM {$wpdb->prefix}wc_order_product_lookup plook_oi
    JOIN {$wpdb->prefix}wc_order_product_lookup plook_o ON plook_o.order_id = plook_oi.order_id
    JOIN {$wpdb->prefix}posts prod_p ON prod_p.ID = plook_o.product_id
    JOIN {$wpdb->prefix}taste_venue_products vprods ON vprods.product_id = plook_o.product_id
    JOIN {$wpdb->prefix}taste_venue ven ON ven.venue_id = vprods.venue_id
    JOIN {$wpdb->prefix}posts ord_p ON ord_p.ID = plook_oi.order_id
    LEFT JOIN {$wpdb->prefix}woocommerce_order_items wcoi ON wcoi.order_item_id = plook_o.order_item_id
    LEFT JOIN {$wpdb->prefix}taste_venue_payment_order_item_xref poix ON poix.order_item_id = plook_o.order_item_id
    LEFT JOIN {$wpdb->prefix}postmeta prod_pm ON prod_pm.post_id = plook_o.product_id
      AND prod_pm.meta_key = 'booking'
    WHERE plook_oi.order_item_id = %d
  ";

  $orig_item_rows = $wpdb->get_results($wpdb->prepare($sql, $order_item_id), ARRAY_A); 
  if (!$orig_item_rows) {
    return false;
  }
  $order_item_rows = array_column($orig_item_rows, null, 'order_item_id');

  return $order_item_rows;
}
