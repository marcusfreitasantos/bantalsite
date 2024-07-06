<?php
/**
 * Content for a single resume.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-resumes/content-single-resume.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager - Resume Manager
 * @category    Template
 * @version     1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( resume_manager_user_can_view_resume( $post->ID ) ) : ?>
<?php do_action( 'single_resume_start' ); ?>
<!--<div class="btn-imprimir-wrap"><button class="btn-imprimir" id="btn-imprimir">Imprimir</button></div>-->


		<?php/*CABEÇALHO PADRÃO DO CURRÍCULO QUE VEIO COM O PLUGIN
		<div class="resume_description">
			<div class="resume-aside">
				<?php the_candidate_photo(); ?>				
				<?php the_resume_links(); ?>				
				<p class="job-title"><?php the_candidate_title(); ?></p>
				<p class="location"><?php the_candidate_location(); ?></p>
				<?php echo do_shortcode( '[printfriendly]' );
				 
			</div>			
		</div>
		*/?>
	
	<div class="single-resume-content">

		

		<div class="resume_description">
			<div class="cabecalho_do_curriculo">
				<div class = "nome_do_candidato"><?php the_title(); ?></div>
				<?php the_candidate_photo(); ?>
			</div>
			<?php echo apply_filters( 'the_resume_description', get_the_content() ); ?>
			<div class="categoria-de-curriculo-wrap">
				<p class="area-atuacao jmfe-custom-field-label">ÁREA DE ATUAÇÃO:</p>
				<p class="resume-category"><?php the_resume_category(); ?></p>
			</div>

		</div>

		<div class= "resume-mais-info">

			<ul class="meta">
				<?php do_action( 'single_resume_meta_start' ); ?>				
				
				
				<?php do_action( 'single_resume_meta_end' ); ?>
			</ul>

			<?php if ( ( $skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) ) ) && is_array( $skills ) ) : ?>
				<h2><?php _e( 'Skills', 'wp-job-manager-resumes' ); ?></h2>
				<ul class="resume-manager-skills">
					<?php echo '<li>' . implode( '</li><li>', $skills ) . '</li>'; ?>

				</ul>
			<?php endif; ?>

			<?php if ( $items = get_post_meta( $post->ID, '_candidate_education', true ) ) : ?>
				<h2 class="campo-educacao"><?php _e( 'Education', 'wp-job-manager-resumes' ); ?></h2>
				<dl class="resume-manager-education">
				<?php
					foreach( $items as $item ) : ?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<h3><?php printf( __( '%s at %s', 'wp-job-manager-resumes' ), '<strong class="qualification">' . esc_html( $item['qualification'] ) . '</strong>', '<strong class="location">' . esc_html( $item['location'] ) . '</strong>' ); ?></h3>
						</dt>
						<dd>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>

			<?php if ( $items = get_post_meta( $post->ID, '_candidate_experience', true ) ) : ?>
				<h2 class="campo-experiencia"><?php _e( 'Experience', 'wp-job-manager-resumes' ); ?></h2>
				<dl class="resume-manager-experience">
				<?php
					foreach( $items as $item ) : ?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<h3><?php printf( __( '%s at %s', 'wp-job-manager-resumes' ), '<strong class="job_title">' . esc_html( $item['job_title'] ) . '</strong>', '<strong class="employer">' . esc_html( $item['employer'] ) . '</strong>' ); ?></h3>
						</dt>
						<dd>
							<?php echo wpautop( wptexturize( $item['notes'] ) ); ?>
						</dd>

					<?php endforeach;
				?>
				</dl>
			<?php endif; ?>


		</div>


		

		<?php/* get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); */?>
		
	</div>
	<?php do_action( 'single_resume_end' ); ?>
<?php else : ?>

	<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>