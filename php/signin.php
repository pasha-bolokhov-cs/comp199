<?php
/**
 * Sign the user in
 *
 */
require_once 'validate.php';
require_once 'secure/auth.php';

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
$response = array();
if (!property_exists($data, "email")) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($data->email)) {
	$response["error"] = "email-wrong";
	goto quit;
}
if (!property_exists($data, "password")) {
	$response["error"] = "password-required";
	goto quit;
}

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
try {
	$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

try {
	/* make a query - case insensitive for email */
	$sth = $dbh->prepare(
		"SELECT name, email, password, salt
			FROM customers
			WHERE LCASE(email) = LCASE(:email)"
	);
	$sth->execute(array(":email" => $data->email));
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
	$passwordInput = crypt($data->password, $salt);
	$passwordInput = base64_encode($passwordInput);

	/* check the password */
	if ($passwordInput != $password){
		$response["error"] = "login";
		goto database_quit;
	}

	/* generate a token */
	$response["jwt"] = generate_jwt($name, $email);
} catch (PDOException $e) {
	$response["error"] = 'Query Error - ' . $e->getMessage();
	goto database_quit;
}

database_quit:
/* close the database */
$dbh = null;

quit:
/* return the response */
echo json_encode($response);
?>
