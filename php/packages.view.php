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
	/** get the package description **/
	$sth = $dbh->prepare(
		"SELECT * FROM packages WHERE UCASE(name) = UCASE(:package) AND available > 0"
	);
	$sth->execute(array(":package" => $data->package));
	if (!($package_row = $sth->fetch())) {
		$response["error"] = "package-sold-out";
		goto database_quit;
	}
	/* check existence of all relevant fields */
	foreach (array("packageId", "segId", "name", "region", "origin", "price",
		       "description", "available", "imageName") as $field)
		if (!array_key_exists($field, $package_row)) {
			$response["error"] = "could not access package detail";
			goto database_quit;
		}

	/* add package data to the response */
	$response["package"] = $package_row;


	/** load data from segments, locations, transport, flights, hotels and activities **/
	/* load all from segments */
	$segments = array();
	foreach ($dbh->query("SELECT * FROM segments") as $row) {
		foreach (array("segId", "location", "transportId", "flightId", "hotelId", "activityId",
			       "duration", "nextSeg") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$segments[$row["segId"]] = $row;
	}

	/* load all from locations */
	$locations = array();
	foreach ($dbh->query("SELECT * FROM locations") as $row) {
		foreach (array("city", "region", "country") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$locations[$row["city"]] = $row;
	}

	/* load all from transport */
	$transport = array();
	foreach ($dbh->query("SELECT * FROM transport") as $row) {
		foreach (array("transportId", "type") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$transport[$row["transportId"]] = $row;
	}

	/* load all from flights */
	$flights = array();
	foreach ($dbh->query("SELECT * FROM flights") as $row) {
		foreach (array("flightId", "flightNo", "origin", "departDate", "destination", "arriveDate") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$flights[$row["flightId"]] = $row;
	}

	/* load all from hotels */
	$hotels = array();
	foreach ($dbh->query("SELECT * FROM hotels") as $row) {
		foreach (array("hotelId", "rank", "imageName", "description") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$hotels[$row["hotelId"]] = $row;
	}

	/* load all from activities */
	$activities = array();
	foreach ($dbh->query("SELECT * FROM activities") as $row) {
		foreach (array("activityId", "name") as $field)
			if (!array_key_exists($field, $row)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$activities[$row["activityId"]] = $row;
	}

	/** loop over segments **/
	$curr_location = $package_row["origin"];
	$curr_seg = $package_row["segId"];
	$response["segments"] = array();
	do {
		/* check that there is segment data */
		if (!array_key_exists($curr_seg, $segments)) {
			$response["error"] = "could not access package detail";
			goto database_quit;
		}

		$seg = array();

		/* if transport => create an extra row for transport solely */
		if ($segments[$curr_seg]["transportId"]) {
			$seg["transport"] = $transport["type"];
			/* GGGG - check existence of these fields */
			$seg["flight"] = $flights[$segments[$curr_seg]["flightId"]]; 
			$seg["origin"] = $locations[$curr_location];
			$seg["destination"] = $locations[$segments[$curr_seg]["location"]];

			$response["segments"][] = $seg;
			$seg = array();
		}

		/*  GGGG - implement */

		/* switch to the next segment */
		$curr_location = $segments["curr_seg"]["location"];
		$curr_seg = $segments[$curr_seg]["nextSeg"];
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
