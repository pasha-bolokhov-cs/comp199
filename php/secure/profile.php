<?php
/**
 * This script refer the customer information from the database
 *
 */
require_once 'auth.php';
require_once '../validate.php';

if (!($token = authenticate()))
	goto auth_error;

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

/* form the query */
$query = <<<"EOF"
	SELECT name, birth, nationality, passportNo, passportExp, email, phone
	FROM customers
	WHERE customerId = $customerId;
EOF;
/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}
if (($resultArray = $result->fetch_assoc()) == NULL) {
	$response["error"] = "login";
	goto database_quit;
}
$response["customer"] = array();
$response["customer"]['name'] = $resultArray['name'];
$response["customer"]['birth'] = $resultArray['birth'];
$response["customer"]['nationality'] = $resultArray['nationality'];
$response["customer"]['passportNo'] = $resultArray['passportNo'];
$response["customer"]['passportExp'] = $resultArray['passportExp'];
$response["customer"]['email'] = $resultArray['email'];
$response["customer"]['phone'] = $resultArray['phone'];

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
