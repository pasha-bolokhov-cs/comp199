<?php
/**
 * This file fetches the details of a package
 *
 */

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* validate data */
$response = array();
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
try {
	$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

try {
	/* get the package description */
	$sth = $dbh->prepare(
		"SELECT * FROM packages WHERE UCASE(name) = UCASE(:package) AND available > 0"
	);
	$sth->execute(array(":package" => $data->package));
	if (!($package_row = $sth->fetch())) {
		$response["error"] = "package-sold-out";
		goto database_quit;
	}
	if (!array_key_exists("origin", $package_row)) {
		$response["error"] = "could not access package detail";
		goto database_quit;
	}
	$curr_location = row["origin"];
	if (!array_key_exists("segId", $package_row)) {
		$response["error"] = "could not access package detail";
		goto database_quit;
	}
	$curr_seg = $package_row["segId"];

	/* loop over segments */
	$response["segments"] = array();
	do {
		$seg = array();
		/* get segment details */
		$sth = $dbh->prepare(
			"SELECT * FROM segments where segId = :segId"
		);
		$sth->execute(array(":segId" => $curr_seg));

		/* GGGG implement */

		$curr_seg = $next_seg;
	} while ($curr_seg);
	
	/* loop over segments */
	do {
	} while ($next);

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
?>
