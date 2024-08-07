<?php
/**
 * Email content when notifying admin of a new job listing.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/emails/admin-new-job.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager
 * @category    Template
 * @version     1.31.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var WP_Post $job
 */
$job = $args['job'];
?>
	<p><?php
		echo wp_kses_post(
			sprintf(
				__( 'Uma nova vaga foi criada em <a href="%s">%s</a>.', 'wp-job-manager' ),
				home_url(),
				get_bloginfo( 'name' )
			)
		);
		switch ( $job->post_status ) {
			case 'publish':
				printf( ' ' . esc_html__( 'A vaga foi publicada e já está disponível para os usuários.', 'wp-job-manager' ) );
				break;
			case 'pending':
				echo wp_kses_post(
					sprintf(
						' ' . __( 'Está aguardando aprovação do administrador em <a href="%s">WordPress admin</a>.','wp-job-manager' ),
						esc_url( admin_url( 'edit.php?post_type=job_listing' ) )
					)
				);
				break;
		}
		?></p>
<?php

/**
 * Show details about the job listing.
 *
 * @param WP_Post              $job            The job listing to show details for.
 * @param WP_Job_Manager_Email $email          Email object for the notification.
 * @param bool                 $sent_to_admin  True if this is being sent to an administrator.
 * @param bool                 $plain_text     True if the email is being sent as plain text.
 */
do_action( 'job_manager_email_job_details', $job, $email, true, $plain_text );
