<?php


function api_usuario_get($request){
    $creds['user_login'] = $request['usuario'];
    $creds['user_password'] =  $request['senha'];
        // $user = wp_authenticate_username_password(null, '02541471513', 'empresa');
        //( $creds, false );

    require_once('/var/www/html/wp-config.php');
    global $wpdb;

     $user_row = $wpdb->get_results(
     $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}usermeta 
	             WHERE ( meta_key = 'billing_cpf' AND meta_value = '%s') 
	             OR ( meta_key = 'billing_cnpj' AND meta_value = '%s' )",
     $creds['user_login'], $creds['user_login'] ) );

    if (!empty($user_row)) {
	$user = get_user_by('ID', $user_row[0]->user_id);
	if ($user && wp_check_password($creds['user_password'], $user->user_pass, $user_row[0]->user_id)) {
	  $user = $user;
	} else { 
          $user = null;
	} 
    }

    $response = array(
	//'user_id'=> $user_row[0]->user_id
        'user_email' => $user->user_email,
        'user_pass' =>  $user->user_pass
    );

    return rest_ensure_response($response);
}

function api_atualizar_password($request){
    $creds['user_id'] = $request['id'];
    $creds['user_password'] =  $request['senha'];
    $creds['user_new_password'] =  $request['newSenha'];

    $user = get_user_by('ID', $creds['user_id']);
    
	if ($user && wp_check_password($creds['user_password'], $user->user_pass, $creds['user_id'])) {
        wp_set_password($creds['user_new_password'], $creds['user_id']);
        $atualizou = true;
    } else { 
        $atualizou = false;
    }

    if ($atualizou) {
        $response = array(
            'user_pass' =>  'Atualzado com sucesso !'
        );
    } else {
        $response = array(
            'user_pass' =>  `Senha inválida !`
        );
    }
    

    return rest_ensure_response($response);
}

function registrar_api_usuario_get(){
    register_rest_route('api', '/usuario', array(
        array(
            'methods' => 'GET',
            'callback' => 'api_usuario_get',
        ),
    ));
    register_rest_route('api', '/usuario/atualizar', array(
        array(
            'methods' => 'GET',
            'callback' => 'api_atualizar_password',
        ),
    ));
}

add_action('rest_api_init', 'registrar_api_usuario_get');


?>
