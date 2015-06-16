<?php
/*
 * This function validates a string for having only alphabetic characters, numbers or 
 * some characters which are allowed in e-mail address
 *
 * This function should be used for validation of most of the strings - names, emails
 *
 * Passwords should not be validated
 *
 */

/* Cancel very long responses */
define("MAX_RESPONSE_LINES", 1000);

function validate($string)
{
	return preg_match('/^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i', $string);
}
?>
