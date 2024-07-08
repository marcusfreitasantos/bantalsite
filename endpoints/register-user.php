<?php

function generateUniqueUsername($username) {

	$username = sanitize_title( $username );

	static $i;
	if ( null === $i ) {
		$i = 1;
	} else {
		$i ++;
	}
	if ( ! username_exists( $username ) ) {
		return $username;
	}
	$new_username = sprintf( '%s-%s', $username, $i );
	if ( ! username_exists( $new_username ) ) {
		return $new_username;
	} else {
		return call_user_func( __FUNCTION__, $username );
	}
}


function registerUsersFromApp(){
    $req = file_get_contents('php://input');
    $req = json_decode($req, true);
	$userFirstName = $req['first_name'];
	$username = generateUniqueUsername($req['username']);
	$userPassword = $req['password'];
	$userEmail = $req['email'];
    $userCpf = $req["cpf"];
    $userCnpj = $req["cnpj"];


	if($username){;
		$newUserId = wp_create_user($username, $userPassword, $userEmail);
	
		if(is_wp_error( $newUserId )){
			$errorMsg = $newUserId->get_error_message();
			return new WP_Error( 'not_found', $errorMsg, array( 'status' => 401 ) );
		
		}else{
            $newUser = get_user_by("ID", $newUserId);
			wp_update_user( array( 'ID' => $newUserId, 'first_name' => $userFirstName ) );
			update_user_meta($newUserId, "billing_first_name", $userFirstName);

            if($userCpf){
			    update_user_meta($newUserId, "billing_cpf", $userCpf);
                $newUser->set_role("candidate");
            }elseif($userCnpj){
			    update_user_meta($newUserId, "billing_cnpj", $userCnpj);
                $newUser->set_role("employer");
            }else{
                $newUser->set_role("subscriber");
            }
			return "Success: User registered";
		}
	}

}


add_action( 'rest_api_init', function () {
  register_rest_route( 'bantalapp/v2', '/users', array(
    'methods' => 'POST',
    'callback' => 'registerUsersFromApp',
  ) );
} );


?>