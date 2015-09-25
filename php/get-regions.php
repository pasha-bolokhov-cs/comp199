<?php
/**
 * This file fetches the list of regions from the database
 * sends it to the website
 *
 */
require_once 'common.php';

/* connect to the database */
require_once AUTH_CONFIG_PATH . '/db_auth.php';
try {
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

try {
	/* make a query for all existing regions */
	$response["regions"] = array();
	$sth = $dbh->prepare("SELECT * FROM regions");
	$sth->execute();
	$response["regions"] = $sth->fetchAll(PDO::FETCH_ASSOC);
	
	// check how many lines we have
	if (count($response["regions"]) > MAX_RESPONSE_LINES) {
		$response["regions"] = NULL;
		$response["error"] = "response too large (over " . MAX_RESPONSE_LINES . " lines)";
		goto database_quit;
	}

	/* find out which regions have available packages associated with them */
	$avail_regions = array();
	$query = <<<"EOF"
		SELECT region FROM packages
		       WHERE available > 0;
EOF;
	foreach ($dbh->query($query, PDO::FETCH_ASSOC) as $row) {
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
