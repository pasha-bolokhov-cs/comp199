<?php
/* 
 * Execute the payment and store the receipt Id in the database
 *
 */
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../../../comp199-www/paypal-credentials.php';
use PayPal\Api\Transaction;
use PayPal\Api\Transactions;
use PayPal\Api\RelatedResources;
use PayPal\Api\Sale;
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
try {
	$dbh = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB, MYSQL_USER, MYSQL_PASS);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	$response["error"] = 'Connect Error: ' . $e->getMessage();
	goto quit;
}

/* get customerId */
if (!($customerId = get_customerId_PDO($dbh, $token))) {
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

/* Get the name of the package (as description) */
$transactions = $payment->getTransactions();
$transaction = $transactions[0];
$description = $transaction->getDescription();

/* Check that the package is still available and GG lock the table */
try {
	$sth = $dbh->prepare(
		"SELECT available FROM packages WHERE UCASE(name) = UCASE(:description) AND available > 0"
	);
	$sth->execute(array(":description" => $description));
	if (!($row = $sth->fetch(PDO::FETCH_ASSOC))) {
		$response["error"] = "package-sold-out";
		goto database_quit;
	}
	if (!array_key_exists("available", $row)) {
		$response["error"] = "could not access package availability";
		goto database_quit;
	}
	$available = $row["available"];
} catch (PDOException $e) {
	$response["error"] = 'Query Error - ' . $e->getMessage();
	goto database_quit;
}

/* Execute Payment */
$execution = new PaymentExecution();
$execution->setPayerId($payerId);

try {
	// Execute the payment
	$result = $payment->execute($execution, $apiContext);
	$payment = Payment::get($paymentId, $apiContext);

} catch (Exception $ex) {
	$response["error"] = "exception: " . $ex->getMessage();
	goto database_quit;
}

$transactions = $payment->getTransactions();
$transaction = $transactions[0];
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

try {
	/** put sale Id into the order **/
	$sth = $dbh->prepare(
		"UPDATE orders
			SET receiptId = :saleId, status = 'Purchased', purchaseDate = STR_TO_DATE(:date, '%Y-%m-%dT%H:%i:%sZ')
			WHERE customerId = :customerId
			AND packageId =
			    (SELECT packageId FROM packages WHERE UCASE(name) = UCASE(:description))"
	);
	$sth->execute(array(
			":saleId" => $saleId,
			":date" => $sale->getUpdateTime(), 
			":customerId" => $customerId,
			":description" => $description
	));

	/** decrement the number of available packages **/
	if ($available > 0)
		$available--;
	$sth = $dbh->prepare(
		"UPDATE packages
			SET available = :available
			WHERE UCASE(name) = UCASE(:description)"
	);
	$sth->execute(array(":available" => $available, ":description" => $description));
} catch (PDOException $e) {
	$response["error"] = 'Query Error - ' . $e->getMessage();
	goto database_quit;
}

/** send a confirmation email **/
$address = $token["email"];
$subject = "Your receipt for your trip";
$message = <<<EOF_MSG
          This email is the receipt of the purchase of your trip "${description}"
          Here is your receipt number: {$saleId}
          Please use this number in further communications regarding this trip
-------------------------------------------------------------------------------------------
          Thank you for using Albatross Travel(R)
          Hope to see you again soon!
EOF_MSG;
$header = 'From: "Albatross Travel" <noreply@albatross-travel.com>';
if (!mail($address, $subject, $message, $header)) {
	// this is not a fatal error as the transaction has succeeded and been recorded
	error_log("could not send email to " . print_r($address, true));
	$response["error"] = "email-failed";
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
