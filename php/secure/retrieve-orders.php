<?php
/**
 * This file fetches the orders from the database and
 * sends the information to the website
 *
 */
require_once 'auth.php';

if (!($token = authenticate()))
	goto auth_error;

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') ' .
			     $mysqli->connect_error;
	goto quit;
}

/* form the query */
$query = <<<"EOF"
	SELECT customers.name, packages.name, status, purchaseDate, receiptId
	       FROM customers, orders, packages
	       WHERE customers.customerId = orders.customerId
	       AND packages.packageId = orders.packageId
	       AND LCASE(customers.email) = LCASE("{$token->email}");
EOF;
error_log(" query = $query ");  //GG

        
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
?>
