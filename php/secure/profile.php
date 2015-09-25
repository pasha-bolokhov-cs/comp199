<?php
/**
 * This script refer the customer information from the database
 *
 */
require_once 'auth.php';
require_once '../common.php';

$response = array();
if (!($token = authenticate()))
	goto auth_error;

/* connect to the database */
require_once AUTH_CONFIG_PATH . '/db_auth.php';
try {
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId($dbh, $token))) {
	goto auth_error_database;
}

try {

	/* form the query */
	$sth = $dbh->prepare(
		"SELECT name, birth, nationality, passportNo, passportExp, email, phone
			FROM customers
			WHERE customerId = :customerId"
	);

	/* do the query */
	$sth->execute(array(":customerId" => $customerId));
	if (!($resultArray = $sth->fetch(PDO::FETCH_ASSOC))) {
		$response["error"] = "login";
		goto database_quit;
	}
	$response["customer"] = array(
		'name' => $resultArray['name'],
		'birth' => $resultArray['birth'],
		'nationality' => $resultArray['nationality'],
		'passportNo' => $resultArray['passportNo'],
		'passportExp' => $resultArray['passportExp'],
		'email' => $resultArray['email'],
		'phone' => $resultArray['phone']
	);

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
