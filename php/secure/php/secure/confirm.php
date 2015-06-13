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
if (!property_exists($data, "name")) {
	$response["error"] = "name-required";
	goto quit;
}
if (!validate($data->name)) {
	$response["error"] = "name-wrong";
	goto quit;
}
if (!property_exists($data, "birth")) {
	$response["error"] = "birth-required";
	goto quit;
}
$birth_arr = date_parse($data->birth);
$birth = $birth_arr["year"] . '-' . $birth_arr["month"] . '-' . $birth_arr["day"];
if (!property_exists($data, "nationality")) {
	$response["error"] = "nationality-required";
	goto quit;
}
if (!validate($data->nationality)) {
	$response["error"] = "nationality-wrong";
	goto quit;
}
if (!property_exists($data, "passportNo")) {
	$response["error"] = "passportNo-required";
	goto quit;
}
if (!preg_match("/^([0-9]|[a-z])*$/i", $data->passportNo)) {
	$response["error"] = "passportNo-wrong";
	goto quit;
}
if (!property_exists($data, "passportExp")) {
	$response["error"] = "passportExp-required";
	goto quit;
}
$passportExp_arr = date_parse($data->passportExp);
$passportExp = $passportExp_arr["year"] . '-' . $passportExp_arr["month"] . '-' . $passportExp_arr["day"];
if (!property_exists($data, "email")) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($data->email)) {
	$response["error"] = "email-wrong";
	goto quit;
}
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

/* convert 'email' to lower case */
$data->email = strtolower($data->email);

/* hash the password */
$salt = file_get_contents("/dev/urandom", false, null, 0, 16);
$password = crypt($data->password, $salt);
$salt = base64_encode($salt);
$password = base64_encode($password);

/* connect to the database */
/* require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB); */
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}
/* form the query */
// currently remove , password = "$password", salt = "$salt"
$query = <<<"EOF"
	UPDATE customers
	SET name = "{$data->name}", birth = STR_TO_DATE("$data->birth", "%Y-%m-%d"), nationality = "{$data->nationality}",
	    passportNo = "{$data->passportNo}", passportExp = STR_TO_DATE("$data->passportExp", "%Y-%m-%d"),
	    email = "{$data->email}", phone = "{$data->phone}"  
	WHERE customerId = $customerId;
EOF;

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto quit;
}
/*
if (($resultArray = $result->fetch_assoc()) == NULL) {
	$response["error"] = "login";
	goto quit;
}
*/
$response['name'] = $data->name;
$response['birth'] = $data->birth;
$response['nationality'] = $data->nationality;
$response['passportNo'] = $data->passportNo;
$response['passportExp'] = $data->passportExp;
$response['email'] = $data->email;
$response['phone'] = $data->phone;

/* hash the password */
$salt = base64_decode($salt);
$passwordInput = crypt($data->password, $salt);
$passwordInput = base64_encode($passwordInput);

/* check the password */
if ($passwordInput != $password){
	$response["error"] = "login";
	goto quit;
}

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
