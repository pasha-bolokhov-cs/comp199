<?php
/**
 * This file checks the client token, and either returns
 * a valid token (renewed or the same one), or returns an error
 *
 * This program is the back-end for 'mildAuthenticate()'
 *
 */
require_once "auth.php";

if ($jwt = authenticate(true)) {
	/* supply a valid JWT (the same one currently) */
	$response["jwt"] = $jwt;
} else {
	/* indicate an error */
	$response["error"] = "authentication";
}

/* return the response */
echo json_encode($response);
?>
