<?php
/**
 * Child theme functions
 *
 * When using a child theme (see https://codex.wordpress.org/Theme_Development
 * and https://codex.wordpress.org/Child_Themes), you can override certain
 * functions (those wrapped in a function_exists() call) by defining them first
 * in your child theme's functions.php file. The child theme's functions.php
 * file is included before the parent theme's file, so the child theme
 * functions would be used.
 *
 * Text Domain: oceanwp
 * @link https://codex.wordpress.org/Plugin_API
 *
 */

/**
 * Load the parent style.css file
 *
 * @link https://codex.wordpress.org/Child_Themes
 */
function oceanwp_child_enqueue_parent_style() {
	// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
	$theme   = wp_get_theme( 'OceanWP' );
	$version = $theme->get( 'Version' );
	// CSS
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'oceanwp-style' ), $version );
    wp_enqueue_style( 'owl-carousel-css', get_stylesheet_directory_uri() . '/libs/owl-carousel/css/owl.carousel.min.css', $version );
	wp_enqueue_style( 'owl-carousel-theme-default-css', get_stylesheet_directory_uri() . '/libs/owl-carousel/css/owl.theme.default.min.css', $version );
    
    //JS
    wp_enqueue_script('custom-jquery', get_stylesheet_directory_uri() . '/libs/jquery/jquery.js', $version);
    wp_enqueue_script('owl-carousel-js', get_stylesheet_directory_uri() . '/libs/owl-carousel/js/owl.carousel.min.js', $version);
    wp_enqueue_script('customScript', get_stylesheet_directory_uri() . '/js/customScript.js', $version );
	
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );


add_filter( 'wp_is_large_user_count', function() { return 50000; }, 10, 0 );


//END POINTS DA REST API PARA APLICAÇÃO EXTERNA
$template_diretorio = get_stylesheet_directory();
require_once('endpoints/user-get.php');
require_once('endpoints/register-user.php');


/*ROTAS PERSONALIZADAS PARA AS PÁGINAS DE EMPRESAS*/
add_action('init', function(){
    add_rewrite_rule( 'empresa/([a-z0-9._-]+)[/]?$', 'index.php?empresa_slug=$matches[1]&vaga_id=$matches[2]', 'top' );
});

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'empresa_slug';
    return $query_vars;
} );

add_filter( 'query_vars', function( $query_vars ) {
    $query_vars[] = 'vaga_id';
    return $query_vars;
} );



add_filter( 'template_include', function( $template ) {
    if ( get_query_var( 'vaga_id' ) ) {
        require_once('templates/vaga/vaga_template.php');
    }
    else if ( get_query_var( 'empresa_slug' ) ) {
         require_once('templates/empresa/empresa_template.php');        
    }else{
        return $template;
    }
} );

//************************REDIRECIONAR USUÁRIOS PARA O SEU RESPECTIVO PAINEL DE CONTROLE APÓS O LOGIN
function wc_custom_user_redirect( $redirect, $user ) {
	
	$role = $user->roles[0];
	$dashboard = admin_url();
	$myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
	if( $role == 'administrator' ) {
        wp_redirect( 'https://bantal.com.br/wp-admin' );

    } elseif ( $role == 'administrador_bantal' ) {
        wp_redirect( 'https://bantal.com.br/wp-admin' );	

	} else {
        wp_redirect( 'https://recrutamento.bantal.com.br/auth/login' );
	}
	
}
add_filter( 'woocommerce_login_redirect', 'wc_custom_user_redirect', 99, 2 );





//************************CRIAR SHORTCODE PARA ALTERAR SENHA
function shortcode_alterar_senha( $atts ) {

    // Attributes
    extract( shortcode_atts( array(
                'text' => 'Edit Account' ), $atts ) );

    return wc_get_template_html( 'myaccount/form-edit-account.php', array( 'user' => get_user_by( 'id', get_current_user_id() ) ) );;

}
add_shortcode( 'wc_alterar_senha', 'shortcode_alterar_senha' );



//************************REDIRECIONAR USUÁRIOS APÓS O LOGOUT
add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
wp_redirect( 'https://bantal.com.br/conta-criada-com-sucesso/' );
exit();
}



//************************APÓS O CADASTRO DO USUÁRIO, FAZ LOGOUT E REDIRECIONA PARA A HOMEPAGE
function custom_registration_redirect() {
    $redirect = 'https://bantal.com.br/conta-criada-com-sucesso';    
    return $redirect;
}
add_action('woocommerce_registration_redirect', 'custom_registration_redirect', 2);

//LOGOUT DO USUÁRIO APÓS O CHECKOUT
add_action( 'template_redirect', 'order_received_logout_redirect' );
function order_received_logout_redirect() {
    if( is_wc_endpoint_url('order-received') ) {
        wp_logout();

        $redirect_url = 'https://bantal.com.br/conta-criada-com-sucesso';

        wp_redirect($redirect_url);

        exit();
    }
}



//************************REGISTRAR USER COMO EMPLOYER
add_role( 'employer', __('Employer' ),array(
'read' => true, 
)
);

add_filter('woocommerce_new_customer_data', 'bantal_assign_custom_role', 10, 1);
 
function bantal_assign_custom_role($args) {
  $args['role'] = 'employer';
  return $args;
}



//************************ADICIONAR UM CHECKBOX NA PÁGINA DE CHECKOUT PARA QUE O USUÁRIO SEJA CADASTRADO COMO CLIENTE
add_action( 'woocommerce_after_order_notes', 'custom_checkout_field_with_candidate_option' );

function custom_checkout_field_with_candidate_option( $checkout ) {
	$checked = $checkout->get_value( 'candidate_checkbox' ) ? $checkout->get_value( 'candidate_checkbox' ) : 1;

    if( current_user_can( 'customer' ) ) return; // exit if it is "candidate customer"

    echo '<div id="candidate_checkbox_wrap" onclick="pegarPreCadastro()">';

    woocommerce_form_field('candidate_checkbox', array(
        'type' => 'checkbox',
        'class' => array('input-checkbox'),
        'label' => __('Assinar'),
        'placeholder' => __('candidate'),
        'required' => false,
    ), $checked);
    echo '</div>';

}


//************************ADD BOTÃO DE PCD DE ACORDO COM O PRODUTO NO CARRINHO
add_action( 'woocommerce_after_order_notes', 'woo_add_conditional_checkout_fields' );

function woo_add_conditional_checkout_fields( $fields ) {
    $cart = WC()->cart->get_cart();


	foreach ( $cart as $item_key => $values ) {
        $product = $values['data'];        

			if( $product->id == 2418 || $product->id == 2417 || $product->id == 1858 ) {
            
                echo '<div id="checkbox_pcd_wrap">';                
            
                woocommerce_form_field('checkbox_pcd', array(
                    'type' => 'checkbox',
                    'class' => array('input-checkbox'),
                    'label' => __('Sou PCD / Reabilitado'),
                    'placeholder' => __('candidate'),
                    'required' => false,
                ), $checked);
                echo '</div>';
			}


	}

	// Return checkout fields.
    return $fields;
}

//CONDICIONAL PRA TROCAR USER ROLE DE EMPLOYER PARA CLIENTE
/*
add_action( 'woocommerce_subscription_status_active', 'candidato_option_update_user_meta' );
function candidato_option_update_user_meta( $order_id ) {
    if ( isset($_POST['candidate_checkbox']) ) {
        $user_id = get_post_meta( $order_id, '_customer_user', true ); // Get user ID
        if( $user_id > 0 ){
            $user = new WP_User($user_id);
            $user->remove_role('employer');
            $user->add_role('customer');
        }
    }
}
*/


//REGISTRAR USUÁRIO COMO CANDIDATO
add_action( 'woocommerce_register_form_end', 'wc_extra_registation_fields' );
function wc_extra_registation_fields() {
    ?>
    <?php if (is_page('2559') ):?>
        <p class="form-row form-row-first">
            <label for="reg_role"><?php _e( 'Privat or commercial?', 'woocommerce' ); ?></label>
            <select class="input-text" name="role" id="cadastrar-candidato">
            <option <?php if ( ! empty( $_POST['role'] ) && $_POST['role'] == 'candidate') esc_attr_e( 'selected' ); ?> value="candidate">candidate</option>
            </select>
        </p>
    <?php endif; ?>
        
    <?php
}


add_action( 'woocommerce_created_customer', 'wc_save_registration_form_fields' );
function wc_save_registration_form_fields( $customer_id ) {
    if ( isset($_POST['role']) ) {
        if( $_POST['role'] == 'candidate' ){
            $user = new WP_User($customer_id);
            $user->set_role('candidate');
        }
    }
}



//************************TRANSFORMAR USUÁRIO EM CANDIDATO APENAS APÓS A CONFIRMAÇÃO DO PAGSEGURO
add_action( 'woocommerce_order_status_processing', 'change_role_on_purchase' );
function change_role_on_purchase( $order_id ) {
    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    $products_to_check_candidate = array('1858', '2417', '2418') ;

    foreach ( $items as $item ) {
        //TRANSFORMAR USER EM CANDIDATO
        if ( $order->user_id > 0 && in_array( $item['product_id'], $products_to_check_candidate ) ) {
        	$user = new WP_User( $order->user_id );

        	// Change role
            $user->remove_role(array('customer', 'employer')  );
        	$user->set_role( 'candidate' );

            // Exit the loop
            break;
        }
        else{
            $user = new WP_User( $order->user_id );

        	// Change role
            $user->remove_role(array('customer', 'candidate' ) );
        	$user->set_role( 'employer' );

            // Exit the loop
            break;
        }

    }
}


//************************TRANSFORMAR USUÁRIO EM CLIENTE APÓS CANCELAMENTO DO PLANO
/*
add_action( 'woocommerce_subscription_status_cancelled', 'change_role_on_cancel' );
function change_role_on_cancel( $order_id ) {
    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        //TRANSFORMAR USER EM CANDIDATO
        if ( $order->user_id > 0 ) {
        	$user = new WP_User( $order->user_id );

        	// Change role
            $user->remove_role(array('candidate', 'employer')  );
        	$user->set_role( 'customer' );

            // Exit the loop
            break;
        }

    }
}
add_action( 'woocommerce_subscription_status_expired', 'change_role_on_cancel' );
*/

//TRANSFORMAR USUÁRIO EM CLIENTE APÓS PEDIDO SER DELETADO DO PAINEL
add_action( 'woocommerce_trash_order', 'change_role_on_trash' );
function change_role_on_trash( $order_id ) {
    $order = wc_get_order( $order_id );
    $items = $order->get_items();

    foreach ( $items as $item ) {
        //TRANSFORMAR USER EM CLIENTE
        if ( $order->user_id <= 0) {
        	$user = new WP_User( $order->user_id );

        	// Change role
            $user->remove_role(array('candidate', 'employer')  );
        	$user->set_role( 'customer' );

            // Exit the loop
            break;
        }

    }
    
}

//************************LOGIN POR CPF E CNPJ
add_filter('authenticate', 'login_cpf_cnpj', 10, 3);
function login_cpf_cnpj($user, $username, $password){

	if ($username == '' || $password == '') return;

   global $wpdb;
   if ($user) {
     return $user;
   }
    $user_row = $wpdb->get_results(
    $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}usermeta 
                     WHERE ( meta_key = 'billing_cpf' AND meta_value = '%s') 
                     OR ( meta_key = 'billing_cnpj' AND meta_value = '%s' )",
        $username, $username ) );

   if (!empty($user_row)) {
     $user = get_user_by('ID', $user_row[0]->user_id);
     if ($user && wp_check_password($password, $user->user_pass, $user_row[0]->user_id)) {
        return $user;
     } else { ?>
          <script>
              alert('Senha Inválida!');
              window.location = "<?php get_permalink(); ?>";
          </script>
        <?php
     }
  }
  return $user;
}

//CONFERIR DURANTE O CHECKOUT SE O CPF OU CNPJ JÁ EXISTEM
add_action('woocommerce_checkout_process', 'check_if_cnpj_exists');
function check_if_cnpj_exists() {

	if ( ! is_user_logged_in() ){
		if( isset( $_POST['billing_cpf'] ) ){
			$label = "CPF";
			$args = array(
				'meta_key' => 'billing_cpf',
				'meta_value' => $_POST['billing_cpf']
			); 
        }
        elseif( isset( $_POST['billing_cnpj'] ) ){
			$label = "CNPJ";
			$args = array(
				'meta_key' => 'billing_cnpj',
				'meta_value' => $_POST['billing_cnpj']
			); 
		}
		$user_cpf_cnpj_exists = get_users( $args );
	
		if ( $user_cpf_cnpj_exists )
			wc_add_notice( 'Já existe uma conta cadastrada com o ' . $label . ' informado.', 'error' );
	}
}


//REDIRECIONAR PARA PÁGINA DE OBRIGADO
add_action( 'woocommerce_thankyou', 'confirmacao_do_pedido');  
function confirmacao_do_pedido( $order_id ){
    $order = wc_get_order( $order_id );    
    $url = 'https://bantal.com.br/conta-criada-com-sucesso/';
    if ( ! $order->has_status( 'failed' ) ) {
        wp_safe_redirect( $url );
        exit;
    }    
}


//LOGOUT SEM CONFIRMAÇÃO
add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : 'https://bantal.com.br';
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
        header("Location: $location");
        die;
    }
}

//************************REDIRECIONAR USER PARA PÁGINA DE ACESSO LIMITADO
add_action( 'template_redirect', 'role_based_redirect' );
function role_based_redirect() {
    if( is_page( array( 468, 448 ) ) ) { //check the list of "corporate" pages
        $user = wp_get_current_user();
        $valid_roles = [ 'administrator', 'candidate', 'employer' ];

        $the_roles = array_intersect( $valid_roles, $user->roles );

        // The current user does not have any of the 'valid' roles.
        if ( empty( $the_roles ) ) {
            wp_redirect( home_url( '/acesso-limitado/' ) );
            exit;
        }
    }
}

//************************REMOVER OPÇÕES DO CARRINHO DO WOOCOMMERCE
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

function custom_override_checkout_fields( $fields ) {
    //unset($fields['billing']['billing_first_name']); 
    //unset($fields['billing']['billing_last_name']); 
    //unset($fields['billing']['billing_company']); 
    //unset($fields['billing']['billing_phone']); 
    //unset($fields['billing']['billing_email']);
    unset($fields['billing']['billing_address_1']); 
    unset($fields['billing']['billing_address_2']); 
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']); 
    unset($fields['billing']['billing_country']); 
    unset($fields['billing']['billing_state']); 
    unset($fields['order']['order_comments']);
    unset($fields['billing']['billing_number']); 
    unset($fields['billing']['billing_birthdate']); 
    unset($fields['billing']['billing_sex']);
    unset($fields['billing']['billing_neighborhood']); 
    return $fields;
}

remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );



//************************MUDAR TEXTO DO BOTÃO NO CHECKOUT
add_filter( 'woocommerce_order_button_text', 'botao_checkout' );
 
function botao_checkout( $button_text ) {
   return 'Finalizar Cadastro'; // new text is here 
}

//PERMITIR APENAS 1 ITEM NO CARRINHO

add_filter( 'woocommerce_add_cart_item_data', 'woo_custom_add_to_cart' );
 
function woo_custom_add_to_cart( $cart_item_data ) {
global $woocommerce;
$woocommerce->cart->empty_cart();
 
return $cart_item_data;
}

//FAZER O MESMO PRODUTO SER ADICIONADO AO CARRINHO MAIS DE UMA VEZ
/*
function namespace_force_individual_cart_items( $cart_item_data, $product_id ) {
    $unique_cart_item_key = md5( microtime() . rand() );
    $cart_item_data['unique_key'] = $unique_cart_item_key;
  
    return $cart_item_data;
  }
  add_filter( 'woocommerce_add_cart_item_data', 'namespace_force_individual_cart_items', 10, 2 );

  */


 //REMOVER PREVIEW DO CURRICULO, INDO DIRETO PARA A PUBLICAÇÃO
add_filter( 'submit_resume_steps', function( $steps ) {
	unset( $steps['preview'] );
	return $steps;
} );

/**
 * Change button text.
 */
add_filter( 'submit_resume_form_submit_button_text', function() {
	return __( 'Submit Resume', 'wp-job-manager-resumes' );
} );

/**
 * Since we removed the preview step and it's handler, we need to manually publish resumes.
 * @param  int $resume_id
 */
add_action( 'resume_manager_update_resume_data', function( $resume_id ) {
	$resume = get_post( $resume_id );
	if ( in_array( $resume->post_status, array( 'preview', 'expired' ), true ) ) {
		// Reset expirey.
		delete_post_meta( $resume->ID, '_resume_expires' );

		// Update resume listing.
		$update_resume                  = array();
		$update_resume['ID']            = $resume->ID;
		$update_resume['post_status']   = get_option( 'resume_manager_submission_requires_approval' ) ? 'pending' : 'publish';
		$update_resume['post_date']     = current_time( 'mysql' );
		$update_resume['post_date_gmt'] = current_time( 'mysql', 1 );
		wp_update_post( $update_resume );
	}
} );


//REDIRECIONAR USER APÓS RESETAR A SENHA
function woocommerce_new_pass_redirect( $user ) {
    wp_redirect( 'https://recrutamento.bantal.com.br/auth/login');
    exit;
  }
  
  add_action( 'woocommerce_customer_reset_password', 'woocommerce_new_pass_redirect' );



  //ADD UM NOVO CAMPO PARA CONFIRMAR A SENHA
add_filter( 'woocommerce_checkout_fields', 'wc_add_confirm_password_checkout', 10, 1 );
function wc_add_confirm_password_checkout( $checkout_fields ) {
    if ( get_option( 'woocommerce_registration_generate_password' ) == 'no' ) {
        $checkout_fields['account']['account_password2'] = array(
                'type'              => 'password',
                'label'             => __( 'Confirmar Senha', 'woocommerce' ),
                'required'          => true,
                'placeholder'       => _x( 'Confirmar Senha', 'placeholder', 'woocommerce' )             
        );
    }

    return $checkout_fields;
}

// Check the password and confirm password fields match before allow checkout to proceed.
add_action( 'woocommerce_after_checkout_validation', 'wc_check_confirm_password_matches_checkout', 10, 2 );
function wc_check_confirm_password_matches_checkout( $posted ) {
    $checkout = WC()->checkout;
    if ( ! is_user_logged_in() && ( $checkout->must_create_account || ! empty( $posted['createaccount'] ) ) ) {
        if ( strcmp( $posted['account_password'], $posted['account_password2'] ) !== 0 ) {
            wc_add_notice( __( 'As senhas não coincidem', 'woocommerce' ), 'error' );
        }
    }
}



//DESATIVAR CPF, NOME E SOBRENOME APENAS PARA EMPRESA
add_filter( 'woocommerce_checkout_fields', 'conditionally_remove_checkout_fields', 25, 1 );
function conditionally_remove_checkout_fields( $fields ) {

    // HERE the defined product Categories
    $categories = array('empresa');

    $found = false;

    // CHECK CART ITEMS: search for items from our defined product category
    foreach ( WC()->cart->get_cart() as $cart_item ){
        if( has_term( $categories, 'product_cat', $cart_item['product_id'] ) ) {
            $found = true;
            break;
        }
    }
    // If a special category is in the cart, remove some shipping fields
    if ( $found ) {

        // hide the billing fields
        //unset($fields['billing']['billing_first_name']); 
        unset($fields['billing']['billing_last_name']); 
        unset($fields['billing']['billing_cpf']);
        unset($fields['billing']['billing_company']);  


        // hide the additional information section
        add_filter('woocommerce_enable_order_notes_field', '__return_false');
        add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
    }else{
        unset($fields['billing']['billing_cnpj']); 
        unset($fields['billing']['billing_company']); 
        unset($fields['billing']['billing_last_name']);
        
    }
    return $fields;
}


add_filter( 'woocommerce_billing_fields', 'wc_unrequire_wc_phone_field');
function wc_unrequire_wc_phone_field( $fields ) {
$fields['billing_persontype']['required'] = false;
return $fields;
}




?>