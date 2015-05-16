<?php
/**
 * This file adds a new order into the cart
 *
 */
require_once 'auth.php';
if (!($token = authenticate()))
	goto auth_error;

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!property_exists($token, "email")) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($token->email)) {
	$response["error"] = "email-wrong";
	goto quit;
}
if (!property_exists($data, "package")) {
	$response["error"] = "package-required";
	goto quit;
}
if (!validate($data->package)) {
	$response["error"] = "package-wrong";
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

/* form the query */
//to be updated, need to retrieve customerId and packageId by user email in advance
$query = <<<"EOF"
	INSERT INTO orders (customerId, packageId, status)
               VALUES (
			(SELECT customerId FROM customers WHERE LCASE(email) = LCASE("{$token->email}")),
			(SELECT packageId FROM packages WHERE UCASE(name) = UCASE("{$data->package}")),
			"Upaid"
	       );
EOF;
error_log("Albatross(TM) new order query = $query ");  //GG

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

database_quit:
/* close the database */
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
return;

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
