<?php
function registerUsersFromApp(){
    $req = file_get_contents('php://input');
    $req = json_decode($req, true);
	$userFirstName = $req['first_name'];
	$userPassword = $req['password'];
	$userEmail = $req['email'];
    $userCpf = $req["cpf"];
    $userCnpj = $req["cnpj"];

	$newUserId = wp_create_user($userFirstName, $userPassword, $userEmail);

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


add_action( 'rest_api_init', function () {
  register_rest_route( 'bantalapp/v2', '/users', array(
    'methods' => 'POST',
    'callback' => 'registerUsersFromApp',
  ) );
} );


?>