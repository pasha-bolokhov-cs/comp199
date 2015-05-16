<?php
/**
 * This file fetches the list of regions from the database
 * sends it to the website
 *
 */

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* form the query for all existing regions */
$query = <<<"EOF"
	SELECT * FROM regions;
EOF;
        
/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* fetch the results and put into response */
$response["regions"] = array();
while ($row = $result->fetch_assoc()) {
	// append the row
	$response["regions"][] = $row;

	// check how many lines we have
	if (count($response["regions"]) > MAX_RESPONSE_LINES) {
		$response["regions"] = NULL;
		$response["error"] = "response too large (over " . MAX_RESPONSE_LINES . " lines)";
		goto database_quit;
	}
}

/* find out which regions have available packages associated with them */
$query = <<<"EOF"
	SELECT region FROM packages
	       WHERE available > 0;
EOF;

/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* fetch the results */
$avail_regions = array();
while ($row = $result->fetch_assoc()) {
	// append the region
	if (!array_key_exists("region", $row)) {
		$response["error"] = 'Internal Error - row does not exist';
		goto database_quit;
	}
	$avail_regions[] = $row["region"];
}

/* now mark the available regions in the response */
foreach ($response["regions"] as $r => $v) {
	$response["regions"][$r]["available"] = in_array($response["regions"][$r]["region"], $avail_regions);
}


database_quit:
/* close the database */
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>
