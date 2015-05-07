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

/* form the query */
$query = <<<"EOF"
SELECT * FROM regions;
EOF;
        
/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error (' . $mysqli->error . ') ';
	$mysqli->close();
	goto quit;
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

/* close the database */
database_quit:
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>
