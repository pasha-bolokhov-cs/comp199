<?php
  
require_once '../validate.php';
require_once 'auth.php';

if (!($token = authenticate())) {
	
  /* Cancel very long responses */
  define("MAX_RESPONSE_LINES", 1000);

  /* get the query from JSON data */
  $jsonData = file_get_contents("php://input");
  $data = json_decode($jsonData);

  $msg = "You have succeeded to purchase the trip. /n Below is your purchase receipt.".$data->merchantId.".";
  if(email("$data->email","Your Purchase Record",wordwrap($msg,70));
  }
}
