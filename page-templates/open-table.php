<?php
/**
 * The template for displaying the Open Table booking page.
 *
 * Template Name: Open Table Booking Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Astra
 * @since 1.0.0
 */

defined('ABSPATH') or die('Direct script access disallowed.');

$order_item_id = isset($_GET['order-item-id']) ? $_GET['order-item-id'] : null;
$order_id = isset($_GET['order-id']) ? $_GET['order-id'] : null;

// if (!$order_item_id && !$order_id) {
//   die("Invalid URL");
// }
if (!$order_item_id) {
  die("Invalid URL");
}

/*
if ( !is_user_logged_in()) {
	// if the user is not logged in, give them a msg w/ link. 
  //  have made change, through functions.php, so that the 
  // login will send them back to this page (or any referer)

  $non_user_disp = "
    <h3>
      You must be logged in to view this page.  
    </h3>
    <div class='field account-sign-in'>
      <a class='button' href='" . get_site_url(null, '/my-taste-account') . "'>Sign in</a>
    </div>
  ";
	$user_info = null;
} else {

	$user_info = get_user_venue_info();
	$user = $user_info['user'];
	$role = $user_info['role'];
	$admin = ('ADMINISTRATOR' === strtoupper($role));
 
   // the code for retrieving order info, including customer id, 
   // must be above here.  Then, check for admin OR the correct customer
   // instead of the test for the Venue role

	if ('VENUE' !== strtoupper($role) && !$admin) {
		echo "<h2>Role: $role </h2>";
		die('You must be logged in as a Venue to access this page.');
	}
	if (!$admin) {
		$venue_name = $user_info['venue_name'];
		$venue_type = $user_info['venue_type'];
		$use_new_campaign = $user_info['use_new_campaign'];
		$venue_voucher_page = $user_info['venue_voucher_page'];
		$type_desc = $venue_type;
	}

}
*/

// $order_item_id = 169746;
if ($order_item_id) {
  $order_item_rows = retrieve_order_booking($order_item_id, "order_item_id");
} else {
  $order_item_rows = retrieve_order_booking($order_id, "order_id");
}

// wp_safe_redirect( 'my-taste-account' );
// exit();

get_header(); ?>

<?php if ( astra_page_layout() == 'left-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

	<div id="primary" <?php astra_primary_class(); ?>>

		<?php astra_primary_content_top(); ?>

      <?php 
        if (!$order_item_rows) {
          $invalid_msg = $order_item_id ? "Invalid Order Item" : "Invalid Order";
          ?>
          <h2>
            <?php echo $invalid_msg ?>
          </h2>
          <?php
        } else {
          foreach($order_item_rows as $cur_order_item_id => $order_item_info) {
            if ($cur_order_item_id == $order_item_id) {
              $booking_id = $order_item_info['booking_id'];
              $venue_name = $order_item_info['venue_name'];
              $booking_name = $order_item_info['booking_name'];
              $rest_name = $booking_name ? $booking_name : $venue_name;
              display_order_booking_article($order_item_info, $booking_id, $rest_name);
            }
          }
        }

      ?>

		<?php astra_primary_content_bottom(); ?>

	</div><!-- #primary -->

<?php if ( astra_page_layout() == 'right-sidebar' ) : ?>

	<?php get_sidebar(); ?>

<?php endif ?>

<?php get_footer(); ?>

<?php

function display_order_booking_article($order_item_info, $booking_id, $rest_name) {
  ?>
    <article class="ast-article-single order-booking-article">
      <div id="booking-order-item-card-container">
        <?php echo get_order_item_card_booking($order_item_info) ?>
      </div>
      <div id="booking-order-item-widget-container">
        <?php 
          if ($booking_id) {
            $ot_full_url = OT_URL_BASE . '?' . "rid=$booking_id" . OT_URL_QUERY;
            ?>
              <script>
                let tasteBooking = {
                  rid: <?php echo $booking_id ?>,
                  restName: "<?php echo $rest_name ?>",
                }
              </script>
              <script type='text/javascript' src='<?php echo $ot_full_url ?>'></script>
            <?php
          } else {
            ?>
              <h3>This order item is not Bookable.</h3>
            <?php
          }
        ?>
      </div>
    </article>
  <?php
}

?>
