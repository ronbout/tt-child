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