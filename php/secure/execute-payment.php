<?php
/* 
 * Execute the payment and store the receipt Id in the database
 *
 */
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../../../comp199-www/paypal-credentials.php';
use PayPal\Api\ExecutePayment;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
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
if (!property_exists($data, "url")) {
	$response["error"] = "return URL missing";
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


/* * * * * * * * * * * * */
/* * * * * * * * * * * * */
/*    Execute  Payment   */
/* * * * * * * * * * * * */
/* * * * * * * * * * * * */

/* extract "paymentId" and "PayerID" */
$url_components = parse_url($data->url);
if (!array_key_exists("query", $url_components)) {
	$response["error"] = "approval URL has no parameters";
	goto database_quit;
}
parse_str($url_components["query"], $url_params);
if (!array_key_exists("paymentId", $url_params)) {
	$response["error"] = "accept-URL does not contain payment ID";
	goto database_quit;
}
$paymentId = $url_params["paymentId"];
if (!array_key_exists("PayerID", $url_params)) {
	$response["error"] = "accept-URL does not contain payer ID";
	goto database_quit;
}
$payerId = $url_params["PayerID"];

/* Api Context */
$apiContext = new ApiContext(new OAuthTokenCredential(PayPal_App_ClientID, PayPal_App_Secret));

/* Get the payment Object by passing paymentId */
$payment = Payment::get($paymentId, $apiContext);
/* Payment Execute */
$execution = new PaymentExecution();
$execution->setPayerId($payerId);

try {
	// Execute the payment
	$result = $payment->execute($execution, $apiContext);

	try {
		$payment = Payment::get($paymentId, $apiContext);
	} catch (Exception $ex) {
		$response["error"] = "exception: " . $ex->getMessage();
		goto database_quit;
	}
} catch (Exception $ex) {
	$response["error"] = "exception: " . $ex->getMessage();
	goto database_quit;
}

use PayPal\Api\Transaction;
use PayPal\Api\Transactions;
use PayPal\Api\RelatedResources;
use PayPal\Api\Sale;
$transactions = $payment->getTransactions();
$transaction = $transactions[0];
$description = $transaction->getDescription();
$relatedResources = $transaction->getRelatedResources();
$relatedResource = $relatedResources[0];
$sale = $relatedResource->getSale();
if (!$sale) {
	$response["error"] = "could not get sale data from transaction";
	goto database_quit;
}
try {
        $sale = Sale::get($sale->getId(), $apiContext);
} catch (Exception $ex) {
	$response["error"] = "exception: " . $ex->getMessage();
	goto database_quit;
}
$saleId = $sale->getId();
error_log("execute-payment.php: package $description is {$payment->getState()}, receipt is {$saleId}"); //GG

/** put sale Id into the order **/
$query = <<<"EOF"
	UPDATE orders
	       SET receiptId = "$saleId", status = "Purchased"
	       WHERE customerId = $customerId
	       AND packageId =
		   (SELECT packageId FROM packages WHERE UCASE(name) = UCASE("$description"));
EOF;
if ($mysqli->query($query) === FALSE) {
	$response["error"] = 'Query Error - ' . $mysqli->error;
	goto database_quit;
}

/** decrement the number of available packages **/
//GG

/** send a confirmation email **/
//GG


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
