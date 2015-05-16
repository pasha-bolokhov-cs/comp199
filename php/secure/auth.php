<?php
require_once 'JWT.php';
require_once '../../../../comp199-www/jwt-auth.php';


/**
 * This function checks the token in the "Authorization" header
 *
 * @params	$return_jwt	whether to return JWT or the decoded token
 *
 * @returns	JWT/token	on successful authentication
 *		NULL		on authentication failure
 *
 */
function authenticate($return_jwt = false) {
	/* get JWT from 'Authorization' */
	$headers = apache_request_headers();
	if (!array_key_exists("Authorization", $headers))
		return NULL;

	// Extract the token
	$jwt = preg_replace('/^Bearer /', '', $headers["Authorization"]);

	try {
		$token = (array) JWT::decode($jwt, JWT_KEY, array('HS256'));
	} catch (DomainException $domain_x) {
		return NULL;
	} catch (UnexpectedValueException $value_x) {
		return NULL;
	} catch (SignatureInvalidException $sign_x) {
		return NULL;
	} catch (BeforeValidException $before_x) { 
		return NULL;
	} catch (ExpiredException $expired_x) {
		return NULL;
	}
	
	return $return_jwt ? $jwt : $token;
}


/**
 * This function generates a JWT token based on $name and $email
 *
 * @params	$name		Full customer's name
 * 		$email		Customer's email
 *
 * @returns			A JWT token
 *
 */
function generate_jwt($name, $email) {
	$token = array(
		"iss"	=>	"Albatross Travel",
		"iat"	=>	time(),
		"name"	=>	$name,
		"email"	=>	$email
	);
	return JWT::encode($token, JWT_KEY, 'HS256');
}

?>
