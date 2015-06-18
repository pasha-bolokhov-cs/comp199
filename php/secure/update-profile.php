 <?php
/**
 * This script updates personal information in the database
 *
 */
require_once '../validate.php';
require_once 'auth.php';

if (!($token = authenticate()))
	goto auth_error;

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

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

/* get customerId */
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

/* form the query */
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

// GG do a query to the database
$response["customer"] = array();
$response["customer"]['name'] = $data->name;
$response["customer"]['birth'] = $data->birth;
$response["customer"]['nationality'] = $data->nationality;
$response["customer"]['passportNo'] = $data->passportNo;
$response["customer"]['passportExp'] = $data->passportExp;
$response["customer"]['email'] = $data->email;
$response["customer"]['phone'] = $data->phone;

/* generate a token */
/* GGGG generate a token if email or name have changed */
// $response["jwt"] = generate_jwt($data->name, $data->email);

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
