<?php
/**
 * Notice shown when a user has already applied for a job.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-applications/applied-notice.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Applications
 * @category    Template
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="job-manager-applications-applied-notice">
	<?php  _e( 'You have successfully applied to this job -- good luck.', 'wp-job-manager-applications' ); ?>
</div>
