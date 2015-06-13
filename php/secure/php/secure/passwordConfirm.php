 <?php
/**
 * This script confirm the request to update personal information on database
 *
 */
require_once '../validate.php';
require_once 'auth.php';

if (!($token = authenticate()))
	goto auth_error;

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!property_exists($data, "password")) {
	$response["error"] = "password-required";
	goto quit;
}

/* get customerId */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

if (!($customerId = get_customerId($mysqli, $token))) {
	goto auth_error_database;
}

/* hash the password 
$salt = file_get_contents("/dev/urandom", false, null, 0, 16);
$password = crypt($data->password, $salt);
$password2 = crypt($data->password2, $salt);
$salt = base64_encode($salt);
$password = base64_encode($password);
$password2 = base64_encode($password2);
*/


/* connect to the database */
/* require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB); */
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* form the query - case insensitive for email */
$query = <<<"EOF"
	SELECT name
	FROM customers
	WHERE customerId = $customerId;
EOF;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto auth_error_database;
}
if (($resultArray = $result->fetch_assoc()) == NULL) {
	$response["error"] = "login";
	goto auth_error_database;
}
$name = $resultArray['name'];
$email = $resultArray['email'];
$password = $resultArray['password'];
$salt = $resultArray['salt'];
    
/* hash the password */
$salt = base64_decode($salt);
$passwordInput = crypt($data->password, $salt);
$passwordNew = crypt($data->password2, $salt);
$passwordInput = base64_encode($passwordInput);
$passwordNew = base64_encode($passwordNew);

/* check the password */
if ($passwordInput != $password){
	$response["error"] =  'Current Passwords are different' . $mysqli->error;
	goto auth_error_database;
}




/* get current password 
$query = <<<"EOF"
    SELECT password 
	FROM customers
	WHERE customerId = $customerId;
EOF;

$oldpassword = $mysqli->query($query);
if ($password != $oldpassword){
    $response["error"] = 'Current Passwords are different' . $mysqli->error;
    goto quit;
}else{
    $password = $password2;
}
*/ 	
/* form the query */

$query = <<<"EOF"
	UPDATE customers
	SET password = $passwordNew
	WHERE customerId = $customerId;
EOF;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto quit;
}

/* hash the password */
$salt = base64_decode($salt);
$passwordInput = crypt($data->password, $salt);
$passwordInput = base64_encode($passwordInput);

/* generate a token */
$response["jwt"] = generate_jwt($data->name, $data->email);

quit:
/* return the response */
echo json_encode($response);
return;

/*** Normal execution does not go beyond this point ***/


auth_error_database:
/* close the database */
$mysqli->close();

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
