<?php
/**
 * This file fetches the packages from the database and
 * sends their information to the website
 *
 */
require_once 'validate.php';

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validation */
if (!validate($data->region)) {
	$response["error"] = "Validation error";
	goto quit;
}

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') ' .
			     $mysqli->connect_error;
	goto quit;
}

/* form the query */
switch ($data->region) {
case "All":
	$query = <<<"EOF_QUERY_ALL"
		SELECT p.name, p.region, p.origin, p.price, p.description, p.available, i.fileName
		       FROM packages p LEFT OUTER JOIN images i
		       USING (imageName)
		       WHERE p.available > 0;
EOF_QUERY_ALL;
	break;

default:
	$query = <<<"EOF_QUERY_SPECIFIC"
		SELECT p.name, p.region, p.origin, p.price, p.description, p.available, i.fileName
		       FROM packages p LEFT OUTER JOIN images i
		       USING (imageName)
		       WHERE (p.region = "{$data->region}") AND (p.available > 0);
EOF_QUERY_SPECIFIC;
	break;
}
        
/* do the query */
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

/* close the database */
database_quit:
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>