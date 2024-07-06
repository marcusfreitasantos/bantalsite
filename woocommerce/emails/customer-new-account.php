<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>


<?php /* translators: %s: Customer username */
$user = get_user_by('login', $user_login );

get_post_meta($order->ID,'billing_first_name', true); ?>
<p><?php printf( esc_html__( 'Bem vindo %s,', 'woocommerce' ), esc_html( $user->first_name )  ); ?></p>
<?php /* translators: %1$s: Site title, %2$s: Username, %3$s: My account link */ ?>
<p><?php echo '<p>Muito obrigado por finalizar o seu cadastro na Bantal. Você já pode acessar nossa plataforma utilizando seu CPF ou CNPJ e Senha para utilizar os nossos serviços em:<a href="https://recrutamento.bantal.com.br/auth/login"> Plataforma B-Talent</a></p>' /* printf( esc_html__( 'Muito obrigado por finalizar o seu cadastro na %1$s. Você já pode acessar nossa plataforma utilizando seu CPF ou CNPJ e Senha para utilizar os nossos serviços em: "https://bantal.com.br/recrutamento"', 'woocommerce' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>', make_clickable( esc_url( wc_get_page_permalink( 'myaccount' ) ) ) );  */// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && $password_generated ) : ?>
	<?php /* translators: %s: Auto generated password */ ?>
	<p><?php printf( esc_html__( 'Senha criada pelo nosso sistema: %s', 'woocommerce' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>
<?php endif; ?>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

do_action( 'woocommerce_email_footer', $email );
