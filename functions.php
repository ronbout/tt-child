<?php
/**
 * MannaPress Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package MannaPress
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_MANNAPRESS_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'mannapress-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_MANNAPRESS_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/**
 * Include the functions related to the jobs board
 */
include get_stylesheet_directory().'/jobs_functions.php';

/**
 * Include the functions related to the booking engine
 */
include get_stylesheet_directory().'/booking_functions.php';

/* turn on the ability for the WC login page to redirect back to the referrer */
add_action( 'woocommerce_login_form_end', 'bbloomer_actual_referrer' );
function bbloomer_actual_referrer() {
	$referer = wp_get_referer();
	echo "<h1>Referer: ***", $referer, "***</h1>"; 
   if ( ! $referer ) return;
   echo '<input type="hidden" name="redirect" value="' . wp_validate_redirect( wc_get_raw_referer(), wc_get_page_permalink( 'myaccount' ) ) . '" />';
}