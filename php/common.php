<?php
/* 
 * Common functions and definitions
 *
 */

/* Path to the authentication information directory - stored in constant AUTH_CONFIG_PATH */
require_once __DIR__ . "/auth-path.php";


/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);


/**
 * ### getBaseUrl function
 *     utility function that returns base url for
 *     determining return/cancel urls
 *
 * @return string
 */
function getBaseUrl()
{
  $protocol = 'http';
  if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
    $protocol .= 's';
  }
  $host = $_SERVER['HTTP_HOST'];
  $request = $_SERVER['PHP_SELF'];
  return dirname($protocol . '://' . $host . $request);
}


/**
 * The function that does the database connection.
 * Assumes that all necessary DB_ constants are defined
 *
 * @return PDO object
 */
function db_connect()
{
	return new PDO(DB_DSN, DB_USER, DB_PASS);
}


/*
 * This function validates a string for having only alphabetic characters, numbers or 
 * some characters which are allowed in e-mail address
 *
 * This function should be used for validation of most of the strings - names, emails
 *
 * Passwords should not be validated
 *
 */
function validate($string)
{
	return preg_match('/^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i', $string);
}
?>
