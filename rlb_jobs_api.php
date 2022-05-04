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


/***********************************************
 * API for main job filter/search 
 ***********************************************/
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
      job_p.post_status AS job_status, terms.name AS job_type,
      MAX(CASE WHEN job_meta.meta_key = '_application' then job_meta.meta_value ELSE NULL END) as application,
      MAX(CASE WHEN job_meta.meta_key = '_company_name' then job_meta.meta_value ELSE NULL END) as company_name,
      MAX(CASE WHEN job_meta.meta_key = '_company_tagline' then job_meta.meta_value ELSE NULL END) as company_tagline,
      MAX(CASE WHEN job_meta.meta_key = '_company_twitter' then job_meta.meta_value ELSE NULL END) as company_twitter,
      MAX(CASE WHEN job_meta.meta_key = '_company_video' then job_meta.meta_value ELSE NULL END) as company_video,
      MAX(CASE WHEN job_meta.meta_key = '_company_website' then job_meta.meta_value ELSE NULL END) as company_website,
      MAX(CASE WHEN job_meta.meta_key = '_job_salary' then job_meta.meta_value ELSE NULL END) as job_salary,
      MAX(CASE WHEN job_meta.meta_key = '_job_class' then job_meta.meta_value ELSE NULL END) as job_class,
      MAX(CASE WHEN job_meta.meta_key = '_job_location' then job_meta.meta_value ELSE NULL END) as job_location,
      logo_p.guid AS logo_img
  ";

  // to get the count of all qualifying jobs, must do select of select
  $select_count_clause = "
  SELECT count(distinct_id) AS jobs_count
    FROM (
      SELECT COUNT( distinct job_p.ID) AS distinct_id
  ";

  $from_clause = "
    FROM {$wpdb->prefix}posts job_p 
      LEFT JOIN {$wpdb->prefix}postmeta job_meta ON job_meta.post_id = job_p.ID
      LEFT JOIN {$wpdb->prefix}postmeta logo_meta ON logo_meta.post_id = job_p.ID AND logo_meta.meta_key = '_thumbnail_id'
      LEFT JOIN {$wpdb->prefix}posts logo_p ON logo_p.ID = logo_meta.meta_value
      LEFT JOIN {$wpdb->prefix}term_relationships term_rel ON term_rel.object_id = job_p.ID
      LEFT JOIN {$wpdb->prefix}terms terms ON terms.term_id = term_rel.term_taxonomy_id
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

  /**
   * 
   * Adjust for job type coming in from Brook-like form
   * 
   */

  if (array_key_exists('jobtype', $parameters)) {
    $job_types = array(
      "Full Time" => 144327,
      "Part Time" => 144386,
      "Temporary" => 144336,
    );

    $job_type_list = '';
    $get_job_types_array = explode(',', $parameters['jobtype']);
    foreach($get_job_types_array as $job_type) {
      $job_type_list .= $job_type_list ? ',' : '';
      $job_type_list .= $job_types[$job_type];
    }

    $where_clause .= " AND term_rel.term_taxonomy_id IN ($job_type_list) ";
  }

  $group_clause = " GROUP BY job_p.ID ";

  $having_clause = "";

  if (array_key_exists('company', $parameters)) {

    $company_list = '';
    $get_company_array = explode(',', $parameters['company']);
    foreach($get_company_array as $company) {
      $company_list .= $company_list ? ',' : '';
      $company_list .= "'$company'";
    }

    $having_clause .= $having_clause ? " AND " : " HAVING ";
    $having_clause .= "MAX(CASE WHEN job_meta.meta_key = '_company_name' then job_meta.meta_value ELSE NULL END) in ($company_list)";
  }

  if (array_key_exists('jobclass', $parameters)) {

    $job_class_list = '';
    $get_job_class_array = explode(',', $parameters['jobclass']);
    foreach($get_job_class_array as $job_class) {
      $job_class_list .= $job_class_list ? ',' : '';
      $job_class_list .= "'$job_class'";
    }

    $having_clause .= $having_clause ? " AND " : " HAVING ";
    $having_clause .= "MAX(CASE WHEN job_meta.meta_key = '_job_class' then job_meta.meta_value ELSE NULL END) in ($job_class_list)";
  }

  $order_clause = "ORDER BY ";
  $order_clause .= $keyword_flag ? "job_p.post_title LIKE '%programmer%' DESC, job_p.post_date DESC " : "job_p.post_date DESC";

  $sql = $select_clause . $from_clause . $where_clause . $group_clause . $having_clause . $order_clause;
  $count_sql = $select_count_clause . $from_clause . $where_clause . $group_clause . $having_clause . $order_clause . ") jobrows ";

  $prepared_sql = $wpdb->prepare($sql);
  $prepared_count_sql = $wpdb->prepare($count_sql);

  $jobs_array = $wpdb->get_results($prepared_sql, ARRAY_A);

  if (! count($jobs_array)) {
    $data = array(
      'error' => 'no results'
    );
    return new WP_REST_Response( $data, 200 );
  }
  $jobs_count = $wpdb->get_results($prepared_count_sql, ARRAY_A)[0]['jobs_count'];

  $data = array(
    'jobs_count' => $jobs_count,
    'jobs' =>  $jobs_array,
  );

  return new WP_REST_Response( $data, 200 );

}

/***********************************************
 * API to return company listing with job count
 ***********************************************/

add_action( 'rest_api_init', 'create_job_companies_api');
function create_job_companies_api () {
  $api_array = array( 
    'methods' => 'GET',
    'callback' => 'get_job_companies',
  );

  register_rest_route( 'rlbjobs/v1', '/jobcompanies', $api_array );
}

function get_job_companies($request) {
  global $wpdb;

  $sql = "
    SELECT job_meta.meta_value AS company_name, COUNT(job_p.ID) AS company_job_cnt
    FROM {$wpdb->prefix}posts job_p 
      LEFT JOIN {$wpdb->prefix}postmeta job_meta ON job_meta.post_id = job_p.ID AND job_meta.meta_key = '_company_name'
    WHERE job_p.post_type = 'job_listing'
      AND job_p.post_status IN ('publish', 'expired')
    GROUP BY job_meta.meta_value
    ORDER BY count(job_p.ID) DESC 
  ";

  $prepared_sql = $wpdb->prepare($sql);

  $companies_array = $wpdb->get_results($prepared_sql, ARRAY_A);

  $data = array(
    'companies' =>  $companies_array,
  );

  return new WP_REST_Response( $data, 200 );
}