<?php
/**
 * This file fetches the details of a package
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
// GGGG not implemented yet
$query = <<<"EOF"
	SELECT * FROM packages;
EOF;
        
/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* fetch the results and put into response */
$response["details"] = array();
while ($row = $result->fetch_assoc()) {
	// append the row
	$response["details"][] = $row;

	// check how many lines we have
	if (count($response["details"]) > MAX_RESPONSE_LINES) {
		$response["details"] = NULL;
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
?>
