<?php
/**
 * Header shown below a job application.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-applications/job-application-header.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Applications
 * @category    Template
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$application_meta = get_post_custom( $application->ID );
$resume_title = '';
$resume_location = '';
if (isset($application_meta['Title'])) {
	$resume_title = "{$application_meta['Title'][0]}";
}
if (isset($application_meta['Location'])) {
	$resume_location = "{$application_meta['Location'][0]}";
}

?>
<div class="app-dashboard-header-container">
	<div class="app-dashboard-header-1">
		<div class="app-dashboard-header-avatar">
			<?php	echo get_job_application_avatar( $application->ID ) ?>
		</div>
		<div class="app-dashboard-header-name-rating">
			<div class="app-dashboard-header-name-meta">			
				<h3>
					<?php if ( ( $resume_id = get_job_application_resume_id( $application->ID ) ) && 'publish' === get_post_status( $resume_id ) && function_exists( 'get_resume_share_link' ) && ( $share_link = get_resume_share_link( $resume_id ) ) ) : ?>
						<a href="<?php echo esc_attr( $share_link ); ?>"><?php echo $application->post_title; ?></a>
					<?php else : ?>
						<?php echo $application->post_title; ?>
					<?php endif; ?>
				</h3>
				<div class="app-dashboard-header-resume-title">
					<div style="width: 360px;"><?php echo $resume_title ?></div>
					<div><?php echo $resume_location ?></div>
				</div>
			</div>

			<span class="job-application-rating">
				<span style="width: <?php echo ( get_job_application_rating( $application->ID ) / 5 ) * 100; ?>%;"></span>
			</span>
		</div>
	</div>


</div>
