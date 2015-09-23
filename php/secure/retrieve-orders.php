<?php
/**
 * This file fetches the orders from the database and
 * sends the information to the website
 *
 */
require_once 'auth.php';
require_once '../common.php';

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
if (!($customerId = get_customerId($dbh, $token))) {
	goto auth_error_database;
}

/*
 * If a package name is supplied, we must attempt to add a new order first
 */
try {

	if (property_exists($data, "package")) {
	/* form the query for getting the packageId */
		$sth = $dbh->prepare(
			"SELECT packageId, available FROM packages
			        WHERE UCASE(name) = UCASE(:package)"
		);
		/* do the query */
		$sth->execute(array(":package" => $data->package));

		/* store the new order if there was a package with this name */
		if (($row = $sth->fetch(PDO::FETCH_ASSOC)) && 
		    (!array_key_exists("available", $row) || $row["available"] > 0)) {
			$packageId = $row["packageId"];

			/*
			 * let us see if the customer hasn't yet booked this trip
			 */
			$sth = $dbh->prepare(
				"SELECT packageId FROM orders
				       WHERE customerId = :customerId
				       AND packageId = :packageId"
			);
			/* do the query */
			$sth->execute(array(":customerId" => $customerId, ":packageId" => $packageId));

			/* only proceed with the insert request if there is no record with this package name yet */
			if (!$sth->fetch(PDO::FETCH_ASSOC)) {
				$sth = $dbh->prepare(
					"INSERT INTO orders (customerId, packageId, status)
					       VALUES (
							:customerId, :packageId, 'Unpaid'
					       )"
				);
				/* do the query */
				$sth->execute(array(":customerId" => $customerId, ":packageId" => $packageId));
			}
		} // if ("available" > 0)
	} // if (package name was supplied)


	/*
	 * Now retrieve all orders
	 */
	/* form the query for fetching the orders */
	$sth = $dbh->prepare(	
		"SELECT p.name AS package, i.fileName AS fileName, 
			o.status, o.purchaseDate, o.receiptId
			FROM orders o, packages p, images i
			WHERE o.customerId = :customerId
			AND p.packageId = o.packageId
			AND i.imageName = p.imageName"
	);
        
	/* do the query */
	$sth->execute(array(":customerId" => $customerId));

	/* fetch the results and put into response */
	$response["data"] = array();
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		// append the row
		$response["data"][] = $row;
		
		// check how many lines we have
		if (count($response["data"]) > MAX_RESPONSE_LINES) {
			$response["data"] = NULL;
			$response["error"] = "response too large (over " . MAX_RESPONSE_LINES . " lines)";
			goto database_quit;
		}
	}

	/* put Merchant Id into the response */
	if (count($response["data"])) {
		require_once '../../../../comp199-www/paypal-credentials.php';
		$response["merchant_id"] = PayPal_MerchantID;
	}

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
