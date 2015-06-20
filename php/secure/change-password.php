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
if ($data->currPassword == "") {
	$response["error"] = "curr-password-empty";
	goto quit;
}
if (!property_exists($data, "newPassword")) {
	$response["error"] = "new-password-required";
	goto quit;
}
if ($data->newPassword == "") {
	$response["error"] = "new-password-empty";
	goto quit;
}
if ($data->newPassword == $data->currPassword) {
	$response["error"] = "new-password-not-different";
	goto quit;
}
if (!property_exists($data, "rePassword")) {
	$response["error"] = "re-password-required";
	goto quit;
}
if ($data->rePassword == "") {
	$response["error"] = "re-password-empty";
	goto quit;
}
if ($data->newPassword != $data->rePassword) {
	$response["error"] = "new-passwords-do-not-match";
	goto quit;
}

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
try {
	$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId($dbh, $token))) {
	goto auth_error_database;
}

try {
	/* get user data */
	$sth = $dbh->prepare(
		"SELECT name, email, password, salt
			FROM customers
			WHERE customerId = :customerId"
	);
	$sth->execute(array(":customerId" => $customerId));

	if (!($record = $sth->fetch(PDO::FETCH_ASSOC))) {
		$response["error"] = "login";
		goto database_quit;
	}
	$name = $record['name'];
	$email = $record['email'];
	$password = $record['password'];
	$salt = $record['salt'];
    
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
	$sth = $dbh->prepare(
		"UPDATE customers
			SET password = :new_password, salt = :new_salt
			WHERE customerId = :customerId"
	);
	$sth->execute(array(":new_password" => $new_password, ":new_salt" => $new_salt,
			    ":customerId" => $customerId));

	/* GG re-create a token */
	//$response["jwt"] = generate_jwt($name, $email);

} catch (PDOException $e) {
	$response["error"] = 'Query Error - ' . $e->getMessage();
	goto database_quit;
}

database_quit:
/* close the database */
$dh = null;

quit:
/* return the response */
echo json_encode($response);
return;

/*** Normal execution does not go beyond this point ***/


auth_error_database:
/* close the database */
$dbh = null;

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
