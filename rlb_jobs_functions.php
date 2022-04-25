<?php
/**
 *  Functions for the WP Job Manager plugin modifications
 * 
 *  4/25/2022
 * 
 *  Ronald Boutilier
 * 
 * 
 */

 // Custom Links WooMyAccount
add_filter ( 'woocommerce_account_menu_items', 'matrix_more_links' );
function matrix_more_links( $menu_links ){
    $user_info = wp_get_current_user();
	$role = $user_info->roles[0];

	if ('VENUE' !== strtoupper($role) && 'ADMINISTRATOR' !== strtoupper($role)) {    
        $logout_index = array_search("subscriptions",array_keys($menu_links));
        array_splice($menu_links, $logout_index, 1);
		return $menu_links;
	}

	$new = array( 
        'submit-jobs' => 'Add New Job', 
        'job-dashboard' => 'My Jobs',
        'venue-portal' => 'Portal / Campaign Manager'
    );
    $logout_index = array_search("customer-logout",array_keys($menu_links));
	$menu_links = array_slice( $menu_links, 0, $logout_index, true ) 
	+ $new 
	+ array_slice( $menu_links, $logout_index, NULL, true );

    return $menu_links;
}