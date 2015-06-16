<?php
/* 
 *
 * Create Payment using PayPal
 * Pass EC-Token to the client
 *
 */

require __DIR__ . '/../common.php';
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../../../comp199-www/paypal-credentials.php';
use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

require_once 'auth.php';
require_once '../validate.php';
if (!($token = authenticate()))
	goto auth_error;
	
/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);
$response = array();

/* validate data */
if (!array_key_exists("email", $token)) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($token["email"])) {
	$response["error"] = "email-wrong";
	goto quit;
}
if (!property_exists($data, "package")) {
	$response["error"] = "package-required";
	goto quit;
}
if (!validate($data->package)) {
	$response["error"] = "package-wrong";
	goto quit;
}

/* connect to the database */
require_once '../../../../comp199-www/mysqli_auth.php';
$mysqli = @new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
if ($mysqli->connect_error) {
	$response["error"] = 'Connect Error (' . $mysqli->connect_errno . ') '
			     . $mysqli->connect_error;
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId($mysqli, $token))) {
	goto auth_error_database;
}


/* * * * * * * * * * * */
/* * * * * * * * * * * */
/*   Create  Payment   */
/* * * * * * * * * * * */
/* * * * * * * * * * * */

/** Get Package Details **/
/* form the query */
$query = <<<"EOF_PACKAGE_QUERY"
	SELECT name, price, available
	       FROM packages
	       WHERE UCASE(name) = UCASE("{$data->package}");
EOF_PACKAGE_QUERY;

/* do the query */
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}
if (!($row = $result->fetch_assoc())) {
	$response["error"] = "package-non-existent";
	goto database_quit;
}
if (array_key_exists("available", $row) && $row["available"] == 0) {
	$response["error"] = "package-sold-out";
	goto database_quit;
}
if (!array_key_exists("name", $row) || !array_key_exists("price", $row)) {
	$response["error"] = "could not get package details";
	goto database_quit;
}
$package = $row["name"];
$price = $row["price"];


/** Payer **/
$payer = new Payer();
$payer->setPaymentMethod("paypal");


/** Payment Amount **/
$amount = new Amount();
$amount->setCurrency("CAD")
	->setTotal($price);


/** Transaction **/
/* set package name as description */
$transaction = new Transaction();
$transaction->setAmount($amount)
		->setDescription($package);


/** Redirect urls after payment approval / cancellation **/
$baseUrl = getBaseUrl();
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("$baseUrl/execute-payment.php?success=true")
		->setCancelUrl("$baseUrl/execute-payment.php?success=false");


/** Add payment details and set intent as 'sale' **/
$payment = new Payment();
$payment->setIntent("sale")
	->setPayer($payer)
	->setRedirectUrls($redirectUrls)
	->setTransactions(array($transaction));


/** Api Context **/
$apiContext = new ApiContext(new OAuthTokenCredential(PayPal_App_ClientID, PayPal_App_Secret));

/** Create Payment **/
try {
	$payment->create($apiContext);
} catch (Exception $ex) {
	$response["error"] = "exception: " . $ex->getMessage();
	goto database_quit;
}

/** Redirect buyer to paypal - in our case, extract the EC token **/
$approvalUrl = $payment->getApprovalLink();

$url_components = parse_url($approvalUrl);
if (!array_key_exists("query", $url_components)) {
	$response["error"] = "approval URL has no parameters";
	goto database_quit;
}
parse_str($url_components["query"], $url_params);
if (!array_key_exists("token", $url_params)) {
	$response["error"] = "approval URL does not have a EC token";
	goto database_quit;
}

/** Return the token to the client **/
$response["ec_token"] = $url_params["token"];


database_quit:
/* close the database */
$mysqli->close();

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
