<?php
/**
 * This script refer the customer information from the database
 *
 */
require_once 'validate.php';

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);
{$data->email} = "taylor.kitty24.swift@gmail.com";
/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}


/* form the query */
$query = <<<"EOF"
	SELECT name, birth, nationality, passportNo, passportExp, email, phone, password, salt
	       FROM customers
		   WHERE email = "{$data->email}";
EOF;
error_log("Albatross(TM) query = $query ");  //GG

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
console.log($resultArray);
$rootScope.customerName = $resultArray['name'];
$rootScope.birth = $resultArray['birth'];
$rootScope.nationality = $resultArray['nationality'];
$rootScope.passportNo = $resultArray['passportNo'];
$rootScope.passportExp = $resultArray['passportExp'];
$rootScope.email = $resultArray['email'];
$rootScope.phone = $resultArray['phone'];
$response['name'] = $resultArray['name'];

$password = $resultArray['password'];
$salt = $resultArray['salt'];
console.log("Got result = ", $resultArray);  //GG
/* close the database */
database_quit:
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>
