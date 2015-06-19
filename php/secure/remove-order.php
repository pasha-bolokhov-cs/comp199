<?php
/**
 * This file removes the orders from the database and
 * sends the information to the website
 *
 */
require_once 'auth.php';
require_once '../validate.php';

$response = array();
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
if (!($customerId = get_customerId_PDO($dbh, $token))) {
	goto auth_error_database;
}

try {
	/* silently drop the request if the order is not in "Unpaid" status */
	$sth = $dbh->prepare(
		"SELECT status FROM orders 
			WHERE packageId = (SELECT packageId FROM packages
						  WHERE UCASE(name) = UCASE(:package)) 
			AND customerId = :customerId"
	);
	$sth->execute(array(":package" => $data->package, ":customerId" => $customerId));
	if (!($row = $sth->fetch(PDO::FETCH_ASSOC)) || !array_key_exists("status", $row)) {
		$response["error"] = "could not get order status";
		goto database_quit;
	}
	if ($row["status"] != "Unpaid") {
		goto database_quit;		// drop the request
	}

	/* form the query to delete the order */
	$sth = $dbh->prepare(
		"DELETE FROM orders
			WHERE packageId = (SELECT packageId FROM packages
						  WHERE UCASE(name) = UCASE(:package))
			AND customerId = :customerId"
	);
        
	/* do the query */
	$sth->execute(array(":package" => $data->package, ":customerId" => $customerId));
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
return;

/*** Normal execution does not go beyond this point ***/


auth_error_database:
/* close the database */
$dbh = null;

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
