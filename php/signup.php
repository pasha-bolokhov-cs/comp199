<?php
/**
 * This script adds a new customer to the database
 *
 */
require_once 'validate.php';
require_once 'secure/auth.php';

sleep(4); //GG
/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

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
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* test if email already exists */
$query = <<<"EOF_SELECT"
	SELECT email FROM customers
	       WHERE LCASE(email) = LCASE("{$data->email}");
EOF_SELECT;
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}
if ($result->fetch_assoc()) {
	$response["error"] = "email-exists";
	goto database_quit;
}

/* form the query */
$query = <<<"EOF_INSERT"
	INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password, salt)
               VALUES ("{$data->name}", STR_TO_DATE("$birth", "%Y-%m-%d"), "{$data->nationality}",
                       "{$data->passportNo}", STR_TO_DATE("$passportExp", "%Y-%m-%d"),
                       "{$data->email}", "{$data->phone}",
		       "$password", "$salt");
EOF_INSERT;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* generate a token */
$response["jwt"] = generate_jwt($data->name, $data->email);

database_quit:
/* close the database */
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>
