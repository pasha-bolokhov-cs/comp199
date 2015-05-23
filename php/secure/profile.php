 <?php
/**
 * This script refer the customer information from the database
 *
 */
require_once '../validate.php';
/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);
/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}
/* form the query */
$query = <<<"EOF"
  SELECT name, birth, nationality, passportNo, passportExp, email, phone, salt, password
  FROM customers
  WHERE LCASE(email) = LCASE("{$data->email}");
EOF;
/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto quit;
}
if (($resultArray = $result->fetch_assoc()) == NULL) {
	$response["error"] = "login";
	goto quit;
}
$response['name'] = $resultArray['name'];
$response['birth'] = $resultArray['birth'];
$response['nationality'] = $resultArray['nationality'];
$response['passportNo'] = $resultArray['passportNo'];
$response['passportExp'] = $resultArray['passportExp'];
$response['email'] = $resultArray['email'];
$response['phone'] = $resultArray['phone'];
$name = $resultArray['name'];
$email = $resultArray['email'];
$password = $resultArray['password'];
$salt = $resultArray['salt'];
    
/* hash the password *//*
$salt = base64_decode($salt);
$passwordInput = crypt($data->password, $salt);
$passwordInput = base64_encode($passwordInput);
/* check the password */
/*if ($passwordInput != $password){
	$response["error"] = "login";
	goto quit;
}
/* generate a token *//*
$key = "super secret";		//GG change it and move it away
$token = array(
	"iss"	=>	"Albatross Travel",
	"iat"	=>	time(),
	"name"	=>	$name,
	"email"	=>	$email
);
$jwt = JWT::encode($token, $key, 'HS256');
$response["jwt"] = $jwt;*/
quit:
/* return the response */
echo json_encode($response);
?> 