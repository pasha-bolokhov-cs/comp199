<?php
/**
 * This file fetches the packages from the database and
 * sends their information to the website
 *
 */
require_once 'validate.php';

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validation */
$response["data"] = array();
if (!validate($data->region)) {
	$response["error"] = "Validation error";
	goto quit;
}

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
try {
	$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

try {

	/* form the query */
	switch ($data->region) {
	case "All":
		$sth = $dbh->prepare(
			"SELECT p.name, p.region, p.origin, p.price, p.description, p.available, i.fileName
				FROM packages p LEFT OUTER JOIN images i
				USING (imageName)
				WHERE p.available > 0"
		);
		$sth->execute();
		break;

	default:
		$sth = $dbh->prepare(
			"SELECT p.name, p.region, p.origin, p.price, p.description, p.available, i.fileName
				FROM packages p LEFT OUTER JOIN images i
				USING (imageName)
				WHERE (p.region = :region) AND (p.available > 0)"
		);
		$sth->execute(array(":region" => $data->region));
		break;
	}
        
	/* fetch the results and put into response */
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

} catch (PDOException $e) {
	$response["error"] = 'Query Error - ' . $e->getMessage();
	goto database_quit;
}

/* close the database */
database_quit:
$dbh = null;

quit:
/* return the response */
echo json_encode($response);
?>
