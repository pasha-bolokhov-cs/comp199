<?php
/**
 * This file checks the client token, and either returns
 * a valid token (renewed or the same one), or returns an error
 *
 */
require_once 'JWT.php';

/* get JWT from 'Authorization' */
$headers = apache_request_headers();
if (!array_key_exists("Authorization", $headers))
	goto auth_error;

// Extract the token
$jwt = preg_replace('/^Bearer /', '', $headers["Authorization"]);

$key = "super secret";		//GG change it and move it away

try {
	$token = (array) JWT::decode($jwt, $key, array('HS256'));
} catch (DomainException $domain_x) {
	goto auth_error;
} catch (UnexpectedValueException $value_x) {
	goto auth_error;
} catch (SignatureInvalidException $sign_x) {
	goto auth_error;
} catch (BeforeValidException $before_x) { 
	goto auth_error;
} catch (ExpiredException $expired_x) {
	goto auth_error;
}

if (!isset($token))
	goto auth_error;

/* supply a valid JWT (the same one currently) */
$response["jwt"] = $jwt;
goto quit;

auth_error:
/* indicate an error */
$response["error"] = "authentication";

quit:
/* return the response */
echo json_encode($response);
?>
