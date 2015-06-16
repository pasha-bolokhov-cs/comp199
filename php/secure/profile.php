<?php
/**
 * This script refer the customer information from the database
 *
 */
require_once 'auth.php';
require_once '../validate.php';

if (!($token = authenticate()))
	goto auth_error;

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

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
$response['name'] = $resultArray['name'];
$response['birth'] = $resultArray['birth'];
$response['nationality'] = $resultArray['nationality'];
$response['passportNo'] = $resultArray['passportNo'];
$response['passportExp'] = $resultArray['passportExp'];
$response['email'] = $resultArray['email'];
$response['phone'] = $resultArray['phone'];
$name = $resultArray['name'];
$email = $resultArray['email'];

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
