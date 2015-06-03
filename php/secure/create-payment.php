<?php
/* 
 * Create Payment using PayPal as payment method
 * and store receipt id in the database
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

/* Payer */
$payer = new Payer();
$payer->setPaymentMethod("paypal");


/* Payment Amount */
$amount = new Amount();
$amount->setCurrency("CAD")
	->setTotal("77.77");     //GG


/* Transaction */
/* A transaction defines the contract of a payment
   - what is the payment for and who is fulfilling it.
   Transaction is created with a `Payee` and `Amount` types.
*/
$transaction = new Transaction();
$transaction->setAmount($amount)
		->setDescription("This is the payment description.");


/* Redirect urls after payment approval / cancellation */
$baseUrl = getBaseUrl();
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("$baseUrl?success=true")
		->setCancelUrl("$baseUrl?success=false");     //GG


/* Add payment details and set intent as 'sale' */
$payment = new Payment();
$payment->setIntent("sale")
	->setPayer($payer)
	->setRedirectUrls($redirectUrls)
	->setTransactions(array($transaction));


/* Api Context */
$apiContext = new ApiContext(new OAuthTokenCredential(PayPal_App_ClientID, PayPal_App_Secret));

/* Create Payment */
try {
	$payment->create($apiContext);
} catch (Exception $ex) {
	$response["error"] = "exception: " . $ex->getMessage();
	goto database_quit;
}

/* Redirect buyer to paypal - in our case, extract the EC token */
$approvalUrl = $payment->getApprovalLink();
$response["approval_link"] = $approvalUrl; //GG

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
$response["ec_token"] = $url_params["token"];
goto database_quit; //GG

//GGGG payment isn't done yet
/* save the payment id */
$paymentId = $payment->getId();


/* * * * * * * * * * * */
/*  Store receipt id   */
/*  into the database  */
/* * * * * * * * * * * */


/* form the query */
//GGGG $data->package is not packageId!!!
//GGGG $paymentId is not receitId!!!
$query = <<<"EOF"
	UPDATE orders
	SET receiptId = $receiptId
	WHERE customerId = $customerId
	AND UCASE(packageId) = UCASE("{$data->package}");
EOF;
error_log("Albatross(TM) new order query = $query ");  //GG

/* do the query */
$response = array();
if (($result = $mysqli->query($query)) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

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
