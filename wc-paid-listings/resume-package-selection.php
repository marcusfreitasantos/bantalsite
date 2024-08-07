<?php if ( $packages || $user_packages ) :
	$checked = 1;
	?>
	<ul class="resume_packages">
	<script>
	window.onload = function(){
		document.getElementById("enviar-curriculo").click();
	}

	
	</script>
		<?php if ( $user_packages ) : ?>
			<li class="package-section"><?php _e( 'Suas assinaturas:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $user_packages as $key => $package ) :
				$package = wc_paid_listings_get_package( $package );
				?>
				<li class="user-resume-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="resume_package" value="user-<?php echo $key; ?>" id="user-package-<?php echo $package->get_id(); ?>" />
					<label for="user-package-<?php echo $package->get_id(); ?>"><?php echo $package->get_title(); ?></label><br/>
					<?php
					if ( $package->get_limit() ) {
						printf( _n( '%1$s resume posted out of %2$d', '%1$s resumes posted out of %2$s', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count(), $package->get_limit() );
					} else {
						printf( _n( '%s currículos publicados', '%s currículos publicados', $package->get_count(), 'wp-job-manager-wc-paid-listings' ), $package->get_count() );
					}

					if ( $package->get_duration() ) {
						printf( ' ' . _n( 'Assinatura por %s dias', 'Assinatura por %s dias', $package->get_duration(), 'wp-job-manager-wc-paid-listings' ), $package->get_duration() );
					}

						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if ( $packages ) : ?>
			<li class="package-section"><?php _e( 'Comprar assinatura:', 'wp-job-manager-wc-paid-listings' ); ?></li>
			<?php foreach ( $packages as $key => $package ) :
				$product = wc_get_product( $package );
				if ( ! $product->is_type( array( 'resume_package', 'resume_package_subscription' ) ) ) {
					continue;
				}
				/* @var $product WC_Product_Resume_Package|WC_Product_Resume_Package_Subscription */
				if ( $product->is_type( 'variation' ) ) {
					$post = get_post( $product->get_parent_id() );
				} else {
					$post = get_post( $product->get_id() );
				}
				?>
				<li class="resume-package">
					<input type="radio" <?php checked( $checked, 1 ); ?> name="resume_package" value="<?php echo $product->get_id(); ?>" id="package-<?php echo $product->get_id(); ?>" />
					<label for="package-<?php echo $product->get_id(); ?>"><?php echo $product->get_title(); ?></label><br/>
					<?php
					if ( ! empty( $post->post_excerpt ) ) {
						echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
					} else {
						printf( _n( '%1$s para publicar %2$d currículos', '%1$s para publicar %2$s currículos', $product->get_limit(), 'wp-job-manager-wc-paid-listings' ) . ' ', $product->get_price_html(), $product->get_limit() ? $product->get_limit() : __( 'ilimitado', 'wp-job-manager-wc-paid-listings' ) );

						if ( $product->get_duration() ) {
							printf( ' ' . _n( 'assinatura de %s dias', 'assinatura de %s dias', $product->get_duration(), 'wp-job-manager-wc-paid-listings' ), $product->get_duration() );
						}
					}
					$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
<?php else : ?>

	<p><?php _e( 'Nenhuma assinatura encontrada', 'wp-job-manager-wc-paid-listings' ); ?></p>

<?php endif; ?>
