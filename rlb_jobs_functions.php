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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**********************************************
 * Test code for hooks/filters goes here
 **********************************************/

// function test_get_job_listings($query_args, $args) {


//   $file1 = "C:/Users/ronbo/Documents/jim-stuff/tmp/write_test_query_" . time() . ".txt";

//   $msg1 = serialize($query_args);
  
//   file_put_contents($file1, $msg1);

//   $file2 = "C:/Users/ronbo/Documents/jim-stuff/tmp/write_test_" . time() . ".txt";

//   $msg2 = serialize($args);
  
//   file_put_contents($file2, $msg2);


// }
// add_action('before_get_job_listings', 'test_get_job_listings', 10, 2);


/**********************************************
 * Include the code for adding custom job fields
 **********************************************/
include get_stylesheet_directory().'/rlb_custom_job_fields.php';
include get_stylesheet_directory().'/rlb_jobs_api.php';

/****************************************************
 * Add Links to the My Taste Account menu
 ****************************************************/
add_filter ( 'woocommerce_account_menu_items', 'matrix_more_links' );
function matrix_more_links( $menu_links ){
  $user_info = wp_get_current_user();
	$role = $user_info->roles[0];


/*****
 * 
 * just a place to put unrelated test code
 */
/*
  $args = array(
    'post_type' => 'job_listing',
    'posts_per_page' => '20',
    'tax_query' => array(
        array(
            'taxonomy' => 'job_listing_type',
            'terms'    => array( '144386', '144327'),
        ),
    ),
    'meta_key' => '_company_name',
    'meta_value' => array('Jims Bar and Grill', 'Sauron Inc.' )
  );
  // the query
  $query = new WP_Query( $args );
  echo "**Job count:   ", count($query->posts), "   ";
  print_r($query->posts);
  */



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

/****************************************************
 * Modify Labels and Placeholders in the Job Entry page
 ****************************************************/
add_filter( 'submit_job_form_fields', 'custom_submit_job_form_fields' );

// This is your function which takes the fields, modifies them, and returns them
// You can see the fields which can be changed here: https://github.com/mikejolley/WP-Job-Manager/blob/master/includes/forms/class-wp-job-manager-form-submit-job.php
function custom_submit_job_form_fields( $fields ) {

    // Here we target one the job fields and change its placeholder etc
    $fields['job']['job_title']['label'] = 'Job Title <span class="required">*</span>';
    $fields['company']['company_name']['label'] = 'Company name <span class="required">*</span>';
    $fields['job']['job_category']['label'] = 'Job category <span class="required">*</span>';
    $fields['job']['job_description']['label'] = 'Description <span class="required">*</span>';
    $fields['job']['job_type']['label'] = 'Choose Job Type <span class="required">*</span>';
    $fields['job']['job_salary']['label'] = 'Salary (€) <span class="required">*</span>';
    $fields['job']['job_location']['placeholder'] = 'e.g. "Dublin"';
    $fields['job']['application']['placeholder'] = 'Enter an email address or website URL were applications will be sent to';
    $fields['company']['company_logo']['label'] = 'Logo (.jpg, .png or .gif)';
    return $fields;
}

/**************************************************************
 * Make sure that the login/logout is done w/in TheTaste site
 **************************************************************/
add_filter( 'submit_job_form_login_url', 'wpjms_redirect_login_url' );
function wpjms_redirect_login_url() {
  return get_site_url(null, '/my-taste-account');
}

add_filter( 'submit_job_form_logout_url', function() {
  return wp_logout_url(get_bloginfo('url'));
});

/**************************************************************
 * Make sure Job Packages do not show up on Offers page
 **************************************************************/
function custom_pre_get_posts_query( $q ) {
  $tax_query = (array) $q->get( 'tax_query' );

  $tax_query[] = array(
         'taxonomy' => 'product_cat',
         'field' => 'slug',
         'terms' => array( 'job-package' ), 
         'operator' => 'NOT IN'
  );

  $q->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );