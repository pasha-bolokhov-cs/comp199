<?php
/**
 * This file fetches the details of a package
 *
 */
require_once 'common.php';

$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

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
require_once '../../../comp199-www/db_auth.php';
try {
	$dbh = db_connect();
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
	if (!($package_row = $sth->fetch(PDO::FETCH_ASSOC))) {
		$response["error"] = "package-sold-out";
		goto database_quit;
	}
	/* check existence of all relevant fields */
	foreach (array("packageId", "segId", "name", "region", "origin", "price",
		       "description", "available", "imageName") as $field)
		if (!array_key_exists($field, $package_row)) {
			error_log("packages.view.php: field $field does not exist in package {$data->package}");
			$response["error"] = "could not access package detail";
			goto database_quit;
		}

	/* get image data */
	$sth = $dbh->prepare(
		"SELECT * FROM images WHERE imageName = :imageName"
	);
	$sth->execute(array(":imageName" => $package_row["imageName"]));
	if (!($image_row = $sth->fetch(PDO::FETCH_ASSOC))) {
		error_log("packages.view.php: table \"images\" has no entries");
		$response["error"] = "could not access package detail";
		goto database_quit;
	}
	foreach (array("imageName", "fileName", "type") as $field)
		if (!array_key_exists($field, $image_row)) {
			error_log("packages.view.php: field $field does not exist for image {$package_row['imageName']}");
			$response["error"] = "could not access package detail";
			goto database_quit;
		}
	$package_row["image"] = $image_row;

	/* add package data to the response */
	$response["package"] = $package_row;


	/** load data from segments, locations, transport, flights, hotels and activities **/
	/* load all from segments */
	$segments = array();
	foreach ($dbh->query("SELECT * FROM segments", PDO::FETCH_ASSOC) as $row) {
		foreach (array("segId", "location", "transportId", "flightId", "hotelId", "activityId",
			       "duration", "nextSeg") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"segments\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$segments[$row["segId"]] = $row;
	}

	/* load all from locations */
	$locations = array();
	foreach ($dbh->query("SELECT * FROM locations", PDO::FETCH_ASSOC) as $row) {
		foreach (array("city", "region", "country") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"locations\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$locations[$row["city"]] = $row;
	}

	/* load all from transport */
	$transport = array();
	foreach ($dbh->query("SELECT * FROM transport", PDO::FETCH_ASSOC) as $row) {
		foreach (array("transportId", "type") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"transport\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$transport[$row["transportId"]] = $row;
	}

	/* load all from flights */
	$flights = array();
	foreach ($dbh->query("SELECT * FROM flights", PDO::FETCH_ASSOC) as $row) {
		foreach (array("flightId", "flightNo", "origin", "departDate", "destination", "arriveDate") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"flights\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$flights[$row["flightId"]] = $row;
	}

	/* load all from hotels */
	$hotels = array();
	foreach ($dbh->query("SELECT * FROM hotels", PDO::FETCH_ASSOC) as $row) {
		foreach (array("hotelId", "rank", "imageName", "description") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"hotels\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$hotels[$row["hotelId"]] = $row;
	}

	/* load all from activities */
	$activities = array();
	foreach ($dbh->query("SELECT * FROM activities", PDO::FETCH_ASSOC) as $row) {
		foreach (array("activityId", "name") as $field)
			if (!array_key_exists($field, $row)) {
				error_log("packages.view.php: field $field does not exist in table \"activities\"");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
		$activities[$row["activityId"]] = $row;
	}

	/** loop over segments **/
	$response["segments"] = array();
	for ($curr_location = $package_row["origin"], $curr_seg_id = $package_row["segId"];
	     $curr_seg_id;
	     $curr_location = $curr_seg["location"], $curr_seg_id = $curr_seg["nextSeg"]) { /* switch to the next segment */
		/* check that there is segment data */
		if (!array_key_exists($curr_seg_id, $segments)) {
			error_log("packages.view.php: segId $curr_seg_id is invalid");
			$response["error"] = "could not access package detail";
			goto database_quit;
		}
		$curr_seg = $segments[$curr_seg_id];

		/* reset user-friendly segment information */
		$seg = array();

		/* if transport => create an extra row for transport solely */
		if ($curr_seg["transportId"]) {
			if (!array_key_exists($curr_seg["transportId"], $transport)) {
				error_log("packages.view.php: segment $curr_seg_id has " .
					  "an invalid transportId = {$curr_seg['transportId']}");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["transport"] = $transport[$curr_seg["transportId"]]["type"];

			if ($curr_seg["flightId"]) {
				if (!array_key_exists($curr_seg["flightId"], $flights)) {
					error_log("packages.view.php: segment $curr_seg_id has " .
						  "an invalid flightId = {$curr_seg['flightId']}");
					$response["error"] = "could not access package detail";
					goto database_quit;
				}
				$seg["flight"] = $flights[$curr_seg["flightId"]];
			} else {
				$seg["flight"] = "n/a";
			}
			if (!array_key_exists($curr_location, $locations)) {
				error_log("packages.view.php: location $curr_location is invalid");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["origin"] = $locations[$curr_location];
			if (!array_key_exists($curr_seg["location"], $locations)) {
				error_log("packages.view.php: segment $curr_seg_id has " .
					  "an invalid location = {$curr_seg['location']}");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["destination"] = $locations[$curr_seg["location"]];

			$response["segments"][] = $seg;
			$seg = array();
		}

		/* get location information */
		if ($curr_seg["location"]) {
			if (!array_key_exists($curr_seg["location"], $locations)) {
				error_log("packages.view.php: segment $curr_seg_id has " .
					  "an invalid location = {$curr_seg['location']}");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["location"] = $locations[$curr_seg["location"]];
		}

		/* get hotel information */
		if ($curr_seg["hotelId"]) {
			if (!array_key_exists($curr_seg["hotelId"], $hotels)) {
				error_log("packages.view.php: segment $curr_seg_id has " .
					  "an invalid hotelId = {$curr_seg['hotelId']}");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["hotel"] = $hotels[$curr_seg["hotelId"]];
		} else {
			$seg["hotel"] = NULL;
		}

		/* get activity information */
		if ($curr_seg["activityId"]) {
			if (!array_key_exists($curr_seg["activityId"], $activities)) {
				error_log("packages.view.php: segment $curr_seg_id has " .
					  "an invalid activityId = {$curr_seg['activityId']}");
				$response["error"] = "could not access package detail";
				goto database_quit;
			}
			$seg["activity"] = $activities[$curr_seg["activityId"]];
		} else {
			$seg["activity"] = NULL;
		}

		/* pass on, if not hotel or activity information (just transportation, perhaps) */
		if (!($curr_seg["hotelId"] || $curr_seg["activityId"])) 
			continue;		// drop this segment and continue

		/* get duration */
		$seg["duration"] = $curr_seg["duration"];

		/* append segment information to the response */
		$response["segments"][] = $seg;

		/* check how many lines we have */
		if (count($response["segments"]) > MAX_RESPONSE_LINES) {
			$response["segments"] = NULL;
			$response["error"] = "response too large (over " . MAX_RESPONSE_LINES . " lines)";
			goto database_quit;
		}
	} /* for-loop over segments */
	
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
