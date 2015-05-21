<?php
/**
 * This file fetches the orders from the database and
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


/*
 * If a package name is supplied, we must attempt to add a new order first
 */
if (property_exists($data, "package")) {
	/* form the query for getting the packageId */
	$query = <<<"EOF_PACKAGE_QUERY"
		SELECT packageId, available FROM packages
		       WHERE UCASE(name) = UCASE("{$data->package}");
EOF_PACKAGE_QUERY;

	/* do the query */
	if (($result = $mysqli->query($query)) === FALSE) {
		$response["error"] = 'Query Error - ' . $mysqli->error;
		goto database_quit;
	}

	/* store the new order if there was a package with this name */
	if (($row = $result->fetch_assoc()) && 
	    (!array_key_exists("available", $row) || $row["available"] > 0)) {
		$packageId = $row["packageId"];

		/*
		 * let us see if the customer hasn't yet booked this trip
		 */
		$query = <<<"EOF_CHECK_QUERY"
			SELECT customerId, packageId FROM orders
			       WHERE customerId = (SELECT customerId FROM customers WHERE LCASE(email) = LCASE('{$token["email"]}'))
			       AND packageId = $packageId;
EOF_CHECK_QUERY;
		/* do the query */
		if (($result = $mysqli->query($query)) === FALSE) {
			$response["error"] = 'Query Error - ' . $mysqli->error;
			goto database_quit;
		}

		/* only proceed with the insert request if there is no record with this package name yet */
		if (!$result->fetch_assoc()) {
			$query = <<<"EOF_INSERT_QUERY"
				INSERT INTO orders (customerId, packageId, status)
				       VALUES (
						(SELECT customerId FROM customers WHERE LCASE(email) = LCASE('{$token["email"]}')),
						$packageId, "Unpaid"
				       );
EOF_INSERT_QUERY;

			/* do the query */
			if ($mysqli->query($query) !== TRUE) {
				$response["error"] = 'Query Error - ' . $mysqli->error;
				goto database_quit;
			}
		}
	}
} // if (package name was supplied)


/*
 * Now retrieve all orders
 */
/* form the query for fetching the orders */
$query = <<<"EOF_RETRIEVE_QUERY"
	SELECT p.name AS package, i.fileName AS fileName, 
	       o.status, o.purchaseDate, o.receiptId
	       FROM customers c, orders o, packages p, images i
	       WHERE c.customerId = o.customerId
	       AND LCASE(c.email) = LCASE("{$token['email']}")
	       AND p.packageId = o.packageId
	       AND i.imageName = p.imageName;
EOF_RETRIEVE_QUERY;
        
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
