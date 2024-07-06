<?php
/**
 * Email content when notifying employer of a new application with a resume.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-resumes/emails/apply-with-resume.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-resumes
 * @category    Template
 * @version     1.18.0
 *
 * @var WP_Job_Manager_Email $email          Email object for the notification.
 * @var bool                 $sent_to_admin  True if this is being sent to an administrator.
 * @var bool                 $plain_text     True if the email is being sent as plain text.
 * @var array                $args           Arguments used to generate the email notification.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resume post object.
 *
 * @var WP_Post $resume
 */
$resume = $args['resume'];

/**
 * Job post object.
 *
 * @var WP_Post $job
 */
$job = $args['job'];

echo '<p>';
echo wp_kses_post(
	sprintf(
		// translators: %1$s is the URL for the site; %2$s is the name of the site; %3$s is the job listing permalink; %4$s is the job listing title.
		__( 'Um candidato acabou de enviar o currículo para a vaga de <a href="%3$s">%4$s</a>.', 'wp-job-manager-resumes' ),
		esc_url( home_url() ),
		get_bloginfo( 'name' ),
		esc_url( get_the_job_permalink( $job ) ),
		get_the_title( $job )
	)
);

echo '<p>';
echo esc_html__( 'A seguinte mensagem foi anexada ao currículo: ', 'wp-job-manager-resumes' );
echo '<br />';
echo '<br />';
echo esc_html( $args['message'] );
echo '</p>';


echo '<p>';
echo '<div id=linha-email></div>';
if ( ! empty( $args['resume_link'] ) ) {
	// translators: Placeholder is the URL to the resume.
	echo wp_kses_post( sprintf( __( 'Você pode <a href="https://bantal.com.br/recrutamento">acessar e imprimir o currículo</a> dos candidatos na plataforma a qualquer momento.', 'wp-job-manager-resumes' ), $args['resume_link'] ) );
}

$candidate_email = get_post_meta( $args['resume']->ID, '_candidate_email', true );
if ( ! empty( $candidate_email ) ) {
	// translators: Placeholder is the candidate email address.
	echo wp_kses_post( make_clickable( sprintf( __( ' Também é possível entrar em contato direto com o candidato pelo email: %s.', 'wp-job-manager-resumes' ), $candidate_email ) ) );
}
echo '<div id=linha-email></div>';
echo '</p>';


if ( $email->show_resume_details() ) {
	/**
	 * Show details about the resume.
	 *
	 * @param WP_Post              $resume         The resume to show details for.
	 * @param WP_Job_Manager_Email $email          Email object for the notification.
	 * @param bool                 $sent_to_admin  True if this is being sent to an administrator.
	 * @param bool                 $plain_text     True if the email is being sent as plain text.
	 */
	do_action( 'resume_manager_email_resume_details', $resume, $email, false, $plain_text );
}
