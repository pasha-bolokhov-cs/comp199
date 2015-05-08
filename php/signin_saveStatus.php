<?php
/**
 * This script save a customer's "sign in" status
 *
 */
require_once 'validate.php';

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
error_log(" data = " . print_r($data, true));  //GG
//GGGG validate and test existence !!!
if (!property_exists($data, "email")) {
	$response["error"] = "email-required";
	goto quit;
}
if (!property_exists($data, "password")) {
	$response["error"] = "password-required";
	goto quit;
}

/* connect to the database */
require_once '../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* form the query */
$query = <<<"EOF"
	SELECT email, password, salt
	FROM customer
	WHERE email = "{$data->email}"
	;
EOF;
error_log(" query = $query ");  //GG

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error (' . $mysqli->error . ')';
  	$mysqli->close();
  	goto quit;
  } else {
  	$resultArray = mysqli_fetch_assoc($result);
    $name = $resultArray['email']
    $password = $resultArray['$password']
    $salt = $resultArray['salt']
    
    /* hash the password */
    $passwordInput = crypt($data->password, $salt);
    $passwordInput = base64_encode($passwordInput);
     
    /* check the password */
    if ($passwordInput != $password){
        //send warning to user
        $mysqli->close();
  	    goto quit;
    } else {
      /* create a session to save this user's login status   */
      $cookieLife=1200;
      session_start();
      setcookie(session_name(),session_id(),time()+$cookieLife); 
      
      /* .... */
      $status = $email;
      
    
    }
      
  }




/* close the database */
database_quit:
$mysqli->close();
quit:

/* return the response */
echo json_encode($response);
?>
