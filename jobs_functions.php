<?php
// Custom WP Job Manager
// Keywords
function register_jo_manager_tax() {
 
    $labels = array(
        'name'              => _x( 'Job Keywords', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Job Keyword', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Job Keywords', 'textdomain' ),
        'all_items'         => __( 'All Job Keywords', 'textdomain' ),
        'view_item'         => __( 'View Job Keyword', 'textdomain' ),
        'parent_item'       => __( 'Parent Job Keyword', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Job Keyword:', 'textdomain' ),
        'edit_item'         => __( 'Edit Job Keyword', 'textdomain' ),
        'update_item'       => __( 'Update Job Keyword', 'textdomain' ),
        'add_new_item'      => __( 'Add New Job Keyword', 'textdomain' ),
        'new_item_name'     => __( 'New Job Keyword Name', 'textdomain' ),
        'not_found'         => __( 'No Job Keywords Found', 'textdomain' ),
        'back_to_items'     => __( 'Back to Job Keywords', 'textdomain' ),
        'menu_name'         => __( 'Job Keywords', 'textdomain' ),
    );
 
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'job_keywords' ),
        'show_in_rest'      => true,
    );
 
 
    register_taxonomy('job_keywords', 'job_listing', $args);
 
}
// add_action('init', 'register_jo_manager_tax');


// Custom fields
add_filter( 'submit_job_form_fields', 'frontend_add_salary_field' );
function frontend_add_salary_field( $fields ) {
	$fields['job']['job_salary'] = array(
		'label'       => __( 'Salary (€) *', 'job_manager' ),
		'type'        => 'text',
		'required'    => true,
		'placeholder' => 'e.g. 20000',
		'priority'    => 7
	);
	return $fields;

}


add_filter( 'job_manager_job_listing_data_fields', 'admin_add_salary_field' );
function admin_add_salary_field( $fields ) {
	$fields['_job_salary'] = array(
		'label'       => __( 'Salary (€) *', 'job_manager' ),
		'type'        => 'text',
		'placeholder' => 'e.g. 20000',
		'description' => '',
        'show_in_rest' => true,
	);
	return $fields;
}

add_action( 'single_job_listing_meta_end', 'display_job_salary_data' );
function display_job_salary_data() {
    global $post;

    $salary = get_post_meta( $post->ID, '_job_salary', true );

    if ( $salary ) {
        echo '<li>' . __( 'Salary:' ) . ' €' . esc_html( $salary ) . '</li>';
    }
}

// Add Google structured data

add_filter( 'wpjm_get_job_listing_structured_data', 'add_basesalary_data');

function add_basesalary_data( $data ) {
    global $post;

    $data['baseSalary'] = [];
    $data['baseSalary']['@type'] = 'MonetaryAmount';
    $data['baseSalary']['currency'] = 'EUR';
    $data['baseSalary']['value'] = [];
    $data['baseSalary']['value']['@type'] = 'QuantitativeValue';
    $data['baseSalary']['value']['value'] = get_post_meta( $post->ID, '_job_salary', true );
    $data['baseSalary']['value']['unitText'] = 'YEAR';

    return $data;
}

/**
 * This can either be done with a filter (below) or the field can be added directly to the job-filters.php template file!
 *
 * job-manager-filter class handling was added in v1.23.6
 */
add_action( 'job_manager_job_filters_search_jobs_start', 'filter_by_salary_field' );

function filter_by_salary_field() {
    ?>
<div class="search_categories">
  <label for="search_categories"><?php _e( 'Salary', 'wp-job-manager' ); ?></label>
  <select name="filter_by_salary" class="job-manager-filter">
    <option value=""><?php _e( 'Any Salary', 'wp-job-manager' ); ?></option>
    <option value="upto20"><?php _e( 'Up to €20,000', 'wp-job-manager' ); ?></option>
    <option value="20000-40000"><?php _e( '€20,000 to €40,000', 'wp-job-manager' ); ?></option>
    <option value="40000-60000"><?php _e( '€40,000 to €60,000', 'wp-job-manager' ); ?></option>
    <option value="over60"><?php _e( '€60,000+', 'wp-job-manager' ); ?></option>
  </select>
</div>
<?php
}

/**
 * This code gets your posted field and modifies the job search query
 */
add_filter( 'job_manager_get_listings', 'filter_by_salary_field_query_args', 10, 2 );

function filter_by_salary_field_query_args( $query_args, $args ) {
    if ( isset( $_POST['form_data'] ) ) {
        parse_str( $_POST['form_data'], $form_data );

        // If this is set, we are filtering by salary
        if ( ! empty( $form_data['filter_by_salary'] ) ) {
            $selected_range = sanitize_text_field( $form_data['filter_by_salary'] );
            switch ( $selected_range ) {
                case 'upto20' :
                    $query_args['meta_query'][] = array(
                        'key'     => '_job_salary',
                        'value'   => '20000',
                        'compare' => '<',
                        'type'    => 'NUMERIC'
                    );
                break;
                case 'over60' :
                    $query_args['meta_query'][] = array(
                        'key'     => '_job_salary',
                        'value'   => '60000',
                        'compare' => '>=',
                        'type'    => 'NUMERIC'
                    );
                break;
                default :
                    $query_args['meta_query'][] = array(
                        'key'     => '_job_salary',
                        'value'   => array_map( 'absint', explode( '-', $selected_range ) ),
                        'compare' => 'BETWEEN',
                        'type'    => 'NUMERIC'
                    );
                break;
            }

            // This will show the 'reset' link
            add_filter( 'job_manager_get_listings_custom_filter', '__return_true' );
        }
    }
    return $query_args;
}

// Custom Links WooMyAccount
add_filter ( 'woocommerce_account_menu_items', 'tastejobs_more_links' );
function tastejobs_more_links( $menu_links ){
  $user_info = wp_get_current_user();

	$role = strtoupper($user_info->roles[0]);

  $job_roles = array('VENUE', 'ADMINISTRATOR', 'CANDIDATE');

	if (!in_array($role, $job_roles)) {
		$logout_index = array_search("subscriptions",array_keys($menu_links));
		array_splice($menu_links, $logout_index, 1);
		return $menu_links;
	}

	$user_caps = $user_info->allcaps;
	$employer_flag = in_array('browse_resumes', array_keys($user_caps));
	$cm_flag = in_array($role, array('VENUE', 'ADMINISTRATOR'));

	$new = array();
	if ($employer_flag) {
		$new = $new + array( 
			'submit-jobs' => 'Add New Job', 
			'job-dashboard' => 'My Jobs',
			'browse-resumes' => 'Browse Resumes',
			'jobs-listing' => "Jobs Listing",
		);
	}
	if ('CANDIDATE' == $role) {
		$new = $new + array( 
			'candidate-dashboard' => 'Candidate Dashboard',
			'Submit Resume' => 'Add New Resume',
			'jobs-listing' => "Jobs Listing",
		);
	}
	if ($cm_flag) {
		$new = $new + array('venue-portal' => 'Portal / Campaign Manager');
	}
	$logout_index = array_search("customer-logout",array_keys($menu_links));
	$menu_links = array_slice( $menu_links, 0, $logout_index, true ) 
	+ $new 
	+ array_slice( $menu_links, $logout_index, NULL, true );

	return $menu_links;
}

//Remove Preview Step
function custom_submit_job_steps( $steps ) {
	unset( $steps['preview'] );
	return $steps;
}
add_filter( 'submit_job_steps', 'custom_submit_job_steps' );

/**
 * Change button text (won't work until v1.16.2)
 */
function change_preview_text() {
	return __( 'Submit Job' );
}
add_filter( 'submit_job_form_submit_button_text', 'change_preview_text' );

/**
 * Since we removed the preview step and it's handler, we need to manually publish jobs
 * @param  int $job_id
 */
function done_publish_job( $job_id ) {
	$job = get_post( $job_id );

	if ( in_array( $job->post_status, array( 'preview', 'expired' ) ) ) {
		// Reset expirey
		delete_post_meta( $job->ID, '_job_expires' );

		// Update job listing
		$update_job                  = array();
		$update_job['ID']            = $job->ID;
		$update_job['post_status']   = get_option( 'job_manager_submission_requires_approval' ) ? 'pending' : 'publish';
		$update_job['post_date']     = current_time( 'mysql' );
		$update_job['post_date_gmt'] = current_time( 'mysql', 1 );
		wp_update_post( $update_job );
	}
}
add_action( 'job_manager_job_submitted', 'done_publish_job' );


// Add your own function to filter the fields
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
    // $fields['company']['company_logo']['multiple'] = true;
    // And return the modified fields
    return $fields;
}

add_filter( 'submit_job_form_login_url', 'wpjms_redirect_login_url' );
function wpjms_redirect_login_url() {
	return '/my-taste-account/';
}

add_filter( 'submit_job_form_logout_url', function() {
    return wp_logout_url(get_bloginfo('url'));
});

function custom_pre_get_posts_query( $q ) {
    $tax_query = (array) $q->get( 'tax_query' );

    $tax_query[] = array(
           'taxonomy' => 'product_cat',
           'field' => 'slug',
           'terms' => array( 'job-package' ), // Don't display products in the clothing category on the shop page.
           'operator' => 'NOT IN'
    );

    $q->set( 'tax_query', $tax_query );
}
add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );

/**
 * change the "Preview" Resume submit button to "Sign Up and Preview" IF not user is logged in
 */

 add_filter('submit_resume_form_submit_button_text', function($btn_txt) {
    if (!is_user_logged_in(  )) {
        return "Sign Up and Preview  &rarr;";
    }
    return $btn_txt;
 });

 /**
  * Add a "browse_resumes" capability to admins and venues
  * Can be used to test for other employer capabilities
  */

 function taste_jobs_add_capability() {
    $role = get_role( 'administrator' );
    // Add a new capability.
    $role->add_cap( 'browse_resumes', true );

    $role = get_role( 'venue' );
    // Add a new capability.
    $role->add_cap( 'browse_resumes', true );
}
 
// Add simple_role capabilities, priority must be after the initial role definition.
add_action( 'init', 'taste_jobs_add_capability', 11 );