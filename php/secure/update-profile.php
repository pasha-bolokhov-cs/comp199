 <?php
/**
 * This script updates personal information in the database
 *
 */
require_once '../common.php';
require_once 'auth.php';

$response = array();
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
require_once '../../../../comp199-www/db_auth.php';
try {
	$dbh = db_connect();
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId($dbh, $token))) {
	goto auth_error_database;
}

try {

	/* convert 'email' to lower case */
	$data->email = strtolower($data->email);

	/* execute the update statement */
	$sth = $dbh->prepare(
		"UPDATE customers
			SET name = :name, birth = STR_TO_DATE(:birth, '%Y-%m-%d'), nationality = :nationality,
		    	passportNo = :passportNo, passportExp = STR_TO_DATE(:passportExp, '%Y-%m-%d'),
		    	email = :email, phone = :phone
			WHERE customerId = :customerId"
	);
	$sth->execute(array(":name" => $data->name, ":birth" => $birth, ":nationality" => $data->nationality,
			    ":passportNo" => $data->passportNo, ":passportExp" => $passportExp,
			    ":email" => $data->email, ":phone" => $data->phone,
			    ":customerId" => $customerId));

	/* re-fetch data */
	$sth = $dbh->prepare(
		"SELECT name, birth, nationality,
			passportNo, passportExp,
			email, phone
			FROM customers
			WHERE customerId = :customerId"
	);
	if (!($sth->execute(array(":customerId" => $customerId))) ||	// failure in this request
	    !($refetch = $sth->fetch(PDO::FETCH_ASSOC))) {		// is a "login" failure
		$response["error"] = "login";
		goto database_quit;
	}
	$response["customer"] = array(
		'name' => $refetch["name"],
		'birth' => $refetch["birth"],
		'nationality' => $refetch["nationality"],
		'passportNo' => $refetch["passportNo"],
		'passportExp' => $refetch["passportExp"],
		'email' => $refetch["email"],
		'phone' => $refetch["phone"]
	);

	/* generate a token */
	/* GGGG generate a token if email or name have changed */
	// $response["jwt"] = generate_jwt($data->name, $data->email);

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
return;

/*** Normal execution does not go beyond this point ***/


auth_error_database:
/* close the database */
$dbh = null;

auth_error:
$response["error"] = "authentication";
echo json_encode($response);
?>
