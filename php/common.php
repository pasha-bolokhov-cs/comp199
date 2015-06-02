<?php
/* 
 * Common functions and definitions
 *
 */

/**
 * ### getBaseUrl function
 * // utility function that returns base url for
 * // determining return/cancel urls
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
?>
