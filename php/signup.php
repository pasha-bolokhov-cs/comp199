<?php
/**
 * This script adds a new customer to the database
 *
 */
require_once 'common.php';
require_once 'secure/auth.php';

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!property_exists($data, "name")) {
	$response["error"] = "name-required";
	goto quit;
}
if (!validate($data->name)) {
	$response["error"] = "name-wrong";
	goto quit;
}
if (!property_exists($data, "birth")) {
	$response["error"] = "birth-required";
	goto quit;
}
$birth_arr = date_parse($data->birth);
$birth = $birth_arr["year"] . '-' . $birth_arr["month"] . '-' . $birth_arr["day"];
if (!property_exists($data, "nationality")) {
	$response["error"] = "nationality-required";
	goto quit;
}
if (!validate($data->nationality)) {
	$response["error"] = "nationality-wrong";
	goto quit;
}
if (!property_exists($data, "passportNo")) {
	$response["error"] = "passportNo-required";
	goto quit;
}
if (!preg_match("/^([0-9]|[a-z])*$/i", $data->passportNo)) {
	$response["error"] = "passportNo-wrong";
	goto quit;
}
if (!property_exists($data, "passportExp")) {
	$response["error"] = "passportExp-required";
	goto quit;
}
$passportExp_arr = date_parse($data->passportExp);
$passportExp = $passportExp_arr["year"] . '-' . $passportExp_arr["month"] . '-' . $passportExp_arr["day"];
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

/* convert 'email' to lower case */
$data->email = strtolower($data->email);

/* hash the password */
$salt = file_get_contents("/dev/urandom", false, null, 0, 16);
$password = crypt($data->password, $salt);
$salt = base64_encode($salt);
$password = base64_encode($password);

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

	/* test if email already exists */
	$sth = $dbh->prepare(
		"SELECT email FROM customers
			WHERE LCASE(email) = LCASE(:email)"
	);
	$sth->execute(array(":email" => $data->email));
	if ($sth->fetch(PDO::FETCH_ASSOC)) {
		$response["error"] = "email-exists";
		goto database_quit;
	}

	/* perform the query to insert a new customer */
	$sth = $dbh->prepare(
		"INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password, salt)
	        	VALUES (:name, STR_TO_DATE(:birth, '%Y-%m-%d'), :nationality,
				:passportNo, STR_TO_DATE(:passportExp, '%Y-%m-%d'),
				:email, :phone,
				:password, :salt)"
	);
	$sth->execute(array(":name" => $data->name, ":birth" => $birth, ":nationality" => $data->nationality,
			    ":passportNo" => $data->passportNo, ":passportExp" => $passportExp,
			    ":email" => $data->email, ":phone" => $data->phone,
			    ":password" => $password, ":salt" => $salt));

	/* generate a token */
	$response["jwt"] = generate_jwt($data->name, $data->email);

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
