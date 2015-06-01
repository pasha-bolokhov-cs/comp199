<?php
/* 
 * Create Payment using PayPal as payment method
 * and store receipt id in the database
 *
 */

require __DIR__ . '/../bootstrap.php';
use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;

require_once 'auth.php';
if (!($token = authenticate()))
	goto auth_error;
	
/* get the query from JSON data */
$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData);

/* validate data */
if (!property_exists($token, "email")) {
	$response["error"] = "email-required";
	goto quit;
}
if (!validate($token->email)) {
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
$payer->setPayment_method("paypal");


/* Payment Amount */
$amount = new Amount();
$amount->setCurrency("CAD");
$amount->setTotal("1.00");     //tobeupdated


/* Transaction */
/* A transaction defines the contract of a payment
   - what is the payment for and who is fulfilling it.
   Transaction is created with a `Payee` and `Amount` types.
*/
$transaction = new Transaction();
$transaction->setAmount($amount);
$transaction->setDescription("This is the payment description.");


/* Redirect urls after payment approval/ cancellation */
$baseUrl = getBaseUrl();
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturn_url("$baseUrl/ExecutePayment.php?success=true");      //tobeupdated
$redirectUrls->setCancel_url("$baseUrl/ExecutePayment.php?success=false");     //tobeupdated


/* Payment */
/* create one using the above types and intent as 'sale' */
$payment = new Payment();
$payment->setIntent("sale");
$payment->setPayer($payer);
$payment->setRedirect_urls($redirectUrls);
$payment->setTransactions(array($transaction));


/* Api Context */
/* Pass in a `ApiContext` object to authenticate the call 
   and to send a unique request id (that ensures idempotency).
   The SDK generates a request id if you do not pass one explicitly. */
$apiContext = new ApiContext($cred, 'Request' . time());

/* Create Payment */
/* by posting to the APIService using a valid apiContext.
   The return object contains the status 
   and the url to which the buyer must be redirected to for payment approval
*/
try {
	$payment->create($apiContext);
} catch (\PPConnectionException $ex) {
	echo "Exception: " . $ex->getMessage() . PHP_EOL;
	var_dump($ex->getData());	
	exit(1);
}

/* Redirect buyer to paypal */
/* Retrieve buyer approval url from the `payment` object. */
foreach($payment->getLinks() as $link) {
	if($link->getRel() == 'approval_url') {
		$redirectUrl = $link->getHref();
	}
}

/* save the payment id */
$receiptId = $payment->getId();


/* * * * * * * * * * * */
/*  Store recepit id   */
/*  into the database  */
/* * * * * * * * * * * */

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
