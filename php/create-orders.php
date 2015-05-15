<?PHP
/**
 * This file creates the orders into the database
 *
 */

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* form the query */
//to be updated, need to retrieve customerId and packageId by user email in advance
$query = <<<"EOF"
	INSERT INTO orders (customerId, packageId, status)
               VALUES ("{$data->customerId}", "{$data->packageId}",, "Upaid");
EOF;
error_log("Albatross(TM) query = $query ");  //GG

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/* close the database */
database_quit:
$mysqli->close();
quit:

/* return the response */
echo json_encode($response);
?>
