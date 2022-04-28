<?php
/**
 *  Functions for adding custom Job fields in WP Job Manager
 *  4/25/2022
 * 
 *  Ronald Boutilier
 * 
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/****************************************************
 * Add Salary field with groupings for Search on Jobs Listing page
 ****************************************************/

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
/****************************************************
 * End of Salary Field
 ****************************************************/