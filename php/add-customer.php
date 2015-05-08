<?php
/**
 * This script adds a new customer to the database
 *
 */

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

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


/* validate data */
error_log(" data = " . print_r($data, true));  //GG
//GGGG validate and test existence !!!

/* hash the password */
//

/* form the query */
$query = <<<"EOF"
	INSERT INTO customers (name, birth, nationality, passportNo, passportExp, email, phone, password)
               VALUES ("$data->name", $data->birth, "$data->nationality",
                       "$data->passportNo", $data->passportExp,
                       "$data->email", "$data->phone",
		       "$data->password");
EOF;
error_log(" query = $query ");  //GG

/* do the query */
$response = array();
////if (($result = $mysqli->query($query)) === FALSE) {
////	$response["error"] = 'Query Error (' . $mysqli->error . ') ';
////	$mysqli->close();
////	goto quit;
////}

/* close the database */
database_quit:
$mysqli->close();

quit:
/* return the response */
echo json_encode($response);
?>
