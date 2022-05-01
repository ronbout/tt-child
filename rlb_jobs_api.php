<?php
/**
 *  Functions for adding api customization
 *  related to WP Job Manager
 *  4/28/2022
 * 
 *  Ronald Boutilier
 * 
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'create_joblisting_api');
function create_joblisting_api () {
  $api_array = array( 
    'methods' => 'GET',
    'callback' => 'get_filtered_jobs',
  );

  register_rest_route( 'rlbjobs/v1', '/jobfilter', $api_array );
}

function get_filtered_jobs($request) {
  global $wpdb;

  $output = array();
  $parameters = $request->get_query_params();

  $keyword_flag = array_key_exists('keywords', $parameters);

  $select_clause = "
    SELECT job_p.ID AS job_id, job_p.post_title AS job_title, job_p.post_date AS job_date, job_p.post_content AS job_desc,
      job_p.post_status AS job_status,
      MAX(CASE WHEN job_meta.meta_key = '_application' then job_meta.meta_value ELSE NULL END) as application,
      MAX(CASE WHEN job_meta.meta_key = '_company_name' then job_meta.meta_value ELSE NULL END) as company_name,
      MAX(CASE WHEN job_meta.meta_key = '_company_tagline' then job_meta.meta_value ELSE NULL END) as company_tagline,
      MAX(CASE WHEN job_meta.meta_key = '_company_twitter' then job_meta.meta_value ELSE NULL END) as company_twitter,
      MAX(CASE WHEN job_meta.meta_key = '_company_video' then job_meta.meta_value ELSE NULL END) as company_video,
      MAX(CASE WHEN job_meta.meta_key = '_company_website' then job_meta.meta_value ELSE NULL END) as company_website,
      MAX(CASE WHEN job_meta.meta_key = '_job_salary' then job_meta.meta_value ELSE NULL END) as job_salary,
      MAX(CASE WHEN job_meta.meta_key = '_job_location' then job_meta.meta_value ELSE NULL END) as job_location
  ";

  $from_clause = "
    FROM wp_posts job_p 
      LEFT JOIN wp_postmeta job_meta ON job_meta.post_id = job_p.ID
      LEFT JOIN wp_term_relationships terms ON terms.object_id = job_p.ID
  ";

  $where_clause = "
    WHERE job_p.post_type = 'job_listing'
      AND job_p.post_status IN ('publish', 'expired')
  ";

  if ($keyword_flag) {
    $keywords = $parameters['keywords'];
    $where_clause .= "
    AND (((job_p.post_title LIKE '%" . $keywords . "%')
      OR (job_p.post_excerpt LIKE '%" . $keywords . "%')
      OR (job_p.post_content LIKE '%" . $keywords . "%')))
    ";
  }

  if (array_key_exists('job_types', $parameters)) {
    $jobs_types = $parameters['job_types'];
    $where_clause .= " AND terms.term_taxonomy_id IN ($jobs_types) ";
  }

  $group_clause = " GROUP BY job_p.ID ";

  $having_clause = "";

  if (array_key_exists('company', $parameters)) {
    $company = $parameters['company'];
    $having_clause .= $having_clause ? " AND " : " HAVING ";
    $having_clause .= "MAX(CASE WHEN job_meta.meta_key = '_company_name' then job_meta.meta_value ELSE NULL END) = '$company'";
  }

  $order_clause = "ORDER BY ";
  $order_clause .= $keyword_flag ? "job_p.post_title LIKE '%programmer%' DESC, job_p.post_date DESC " : "job_p.post_date DESC";

  $sql = $select_clause . $from_clause . $where_clause . $group_clause . $having_clause . $order_clause;

  $prepared_sql = $wpdb->prepare($sql);

  // return array( 
  //   'content' =>  $sql
  // );

  $jobs_array = $wpdb->get_results($prepared_sql, ARRAY_A);



  $data = array(
    'jobs' =>  $jobs_array,
  );

  return new WP_REST_Response( $data, 200 );

}