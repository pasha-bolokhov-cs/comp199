<?php
/**
 * This script confirm the request to update personal information on database
 *
 */
require_once '../validate.php';
require_once 'auth.php';

$response = array();
if (!($token = authenticate()))
	goto auth_error;

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!property_exists($data, "currPassword")) {
	$response["error"] = "curr-password-required";
	goto quit;
}
if (!property_exists($data, "newPassword")) {
	$response["error"] = "new-password-required";
	goto quit;
}
if (!property_exists($data, "rePassword")) {
	$response["error"] = "re-password-required";
	goto quit;
}

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId($mysqli, $token))) {
	goto auth_error_database;
}

/* form the query - case insensitive for email */
$query = <<<"EOF"
	SELECT name, email, password, salt
	FROM customers
	WHERE customerId = $customerId;
EOF;

/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto auth_error_database;
}
if (($resultArray = $result->fetch_assoc()) == NULL) {
	$response["error"] = "login";
	goto auth_error_database;
}
$name = $resultArray['name'];
$email = $resultArray['email'];
$password = $resultArray['password'];
$salt = $resultArray['salt'];
    
/* hash the password */
$salt = base64_decode($salt);
$passwordInput = crypt($data->currPassword, $salt);
$passwordInput = base64_encode($passwordInput);

/* check the password */
if ($passwordInput != $password){
	$response["error"] =  "password-wrong";
	goto database_quit;
}


/* has the new password */
$new_salt = file_get_contents("/dev/urandom", false, null, 0, 16);
$new_password = crypt($data->newPassword, $new_salt);
$new_salt = base64_encode($new_salt);
$new_password = base64_encode($new_password);

/* form the query */
$query = <<<"EOF"
	UPDATE customers
	SET password = "$new_password", salt = "$new_salt"
	WHERE customerId = $customerId;
EOF;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto quit;
}

/* generate a token */
$response["jwt"] = generate_jwt($name, $email);

database_quit:
/* close the database */
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
return;

/*** Normal execution does not go beyond this point ***/


auth_error_database:
/* close the database */
$mysqli->close();

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
