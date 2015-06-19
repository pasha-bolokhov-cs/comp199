<?php
/**
 * This script confirm the request to update personal information on database
 *
 */
require_once '../validate.php';
require_once 'auth.php';

$response = array();
$response["error"] = "change of password is not implemented";
goto quit;
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
$passwordNew = crypt($data->newPassword, $salt);
$passwordInput = base64_encode($passwordInput);
$passwordNew = base64_encode($passwordNew);

/* check the password */
if ($passwordInput != $password){
	$response["error"] =  "password-wrong";
	goto database_quit;
}

/* form the query */

$query = <<<"EOF"
	UPDATE customers
	SET password = $passwordNew
	WHERE customerId = $customerId;
EOF;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto quit;
}

/* hash the password */
$salt = base64_decode($salt);
$passwordInput = crypt($data->password, $salt);
$passwordInput = base64_encode($passwordInput);

/* generate a token */
$response["jwt"] = generate_jwt($data->name, $data->email);

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
