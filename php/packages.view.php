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
	$curr_seg_id = $package_row["segId"];
	$response["segments"] = array();
	do {
		/* check that there is segment data */
		if (!array_key_exists($curr_seg_id, $segments)) {
			$response["error"] = "could not access package detail";
			goto database_quit;
		}
		$curr_seg = $segments[$curr_seg_id];

		/* reset user-friendly segment information */
		$seg = array();

		/* if transport => create an extra row for transport solely */
		if ($curr_seg["transportId"]) {
			$seg["transport"] = $transport["type"];
			if (!array_key_exists($curr_seg["flightId"], $flights)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["flight"] = $flights[$curr_seg["flightId"]];
			if (!array_key_exists($curr_location, $locations)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["origin"] = $locations[$curr_location];
			if (!array_key_exists($curr_seg["location"], $locations)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["destination"] = $locations[$curr_seg["location"]];

			$response["segments"][] = $seg;
			$seg = array();
		}

		/* get hotel information */
		if ($curr_seg["hotelId"]) {
			if (!array_key_exists($curr_seg["hotelId"], $hotels)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["hotel"] = $curr_seg["hotelId"];
			$seg["hotel_description"] = $hotel[$curr_seg["hotelId"]]["description"];
		} else {
			$seg["hotel"] = NULL;
		}

		/* get activity information */
		if ($curr_seg["activityId"]) {
			if (!array_key_exists($curr_seg["activityId"], $activities)) {
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["acvitity"] = $activities[$curr_seg["activityId"]];
		} else {
			$seg["activity"] = NULL;
		}

		/* get duration */
		$seg["duration"] = $curr_seg["duration"];

		/* append segment information to the response */
		$response["segments"][] = $seg;

		/* switch to the next segment */
		$curr_location = $segments["curr_seg"]["location"];
		$curr_seg_id = $curr_seg["nextSeg"];
	} while ($curr_seg_id);
	
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
