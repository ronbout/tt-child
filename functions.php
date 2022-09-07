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
define( 'CHILD_THEME_MANNAPRESS_VERSION', '1.1.0' );
define( 'MANNA_PRESS_CHILD_THEME_DIR', trailingslashit( get_stylesheet_directory()) );
define( 'MANNA_PRESS_CHILD_THEME_URL', trailingslashit( get_stylesheet_directory_uri()) );

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
include MANNA_PRESS_CHILD_THEME_DIR . 'jobs_functions.php';

/**
 * create mechanism for taste account login to redirect to referrer, if present
 */
add_action( 'woocommerce_login_form_end', 'taste_theme_actual_referrer' );
function taste_theme_actual_referrer() {
   if ( ! wc_get_raw_referer() ) return;
   echo '<input type="hidden" name="redirect" value="' . wp_validate_redirect( wc_get_raw_referer(), wc_get_page_permalink( 'my-taste-account' ) ) . '" />';
}

/*
function taste_ads_add_shortcodes() {
	add_shortcode("AD-DISPLAY", "display_header_ad");
}
add_action("init", "taste_ads_add_shortcodes");

function display_header_ad() {
	global $wp;
	$page_slug = add_query_arg( array(), $wp->request );
	switch ($page_slug) {
		case "jobs-listing":
			?>
				<div style="width: 628px; height: 100px" >
					<a href="https://dingledistillery.ie/" target="_blank">
						<img width="628" height="100" style="height: 100px"
						 src="https://www.thetaste.ie/wp-content/uploads/2021/09/dinlge-banner-1400.jpg" alt="">
					</a>
				</div>
			<?php
			break;
		default:
			?>
			<div>
				<div id='div-gpt-ad-1650539070276-0' style='min-width: 728px; min-height: 90px;'>
					<script>
						googletag.cmd.push(function() { googletag.display('div-gpt-ad-1650539070276-0'); });
					</script>
				</div>
			</div>
			<?php
	}
	?>
	<?php	
}
*/