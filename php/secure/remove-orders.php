<?php
/**
 * This file removes the orders from the database and
 * sends the information to the website
 *
 */
require_once 'auth.php';
require_once '../validate.php';

if (!($token = authenticate()))
	goto auth_error;
	
/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!array_key_exists("email", $token)) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($token["email"])) {
	$response["error"] = "email-wrong";
	goto quit;
}
if (property_exists($data, "package") && !validate($data->package)) {
	$response["error"] = "package-wrong";
	goto quit;
}

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') ' .
			     $mysqli->connect_error;
	goto quit;
}

/* form the query for delete the orders */
$query = <<<"EOF_DELETE"
  DELETE FROM orders
  WHERE packageId = (SELECT packageId FROM packages
                     WHERE LCASE(name) = LCASE("{$data->package}")) 
  AND customerId = 
	                (SELECT customerId FROM customers
	                 WHERE LCASE(email) = LCASE("{$data->email}"));
EOF_DELETE;
        
/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* fetch the results and put into response */
$response["data"] = array();
while ($row = $result->fetch_assoc()) {
	// append the row
	$response["data"][] = $row;
	
	// check how many lines we have
	if (count($response["data"]) > MAX_RESPONSE_LINES) {
		$response["data"] = NULL;
		$response["error"] = "response too large (over " . MAX_RESPONSE_LINES . " lines)";
		goto database_quit;
	}
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
