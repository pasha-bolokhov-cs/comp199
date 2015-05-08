#! /usr/bin/php
<?php
/*
 * This program validate string data for only alphabets and numbers or e-mail address
 * , and string which include some special characters. Spechial characters are "()[]{}#@%+-."
 * when match the condition return true, if not, will return false
 * Parameter: 1: alphabets, numbers and '_' only 2: e-mail address: default: alphabets and numbers including
 * some special characters
 * last modified 5/7/2015 Toshi
 */
 
 function validate($data, $para)
 {
	switch($para)
	{
		case 1:
			return preg_match('/^\w+$/', $data);	
		case 2:
			return preg_match('/^[\w\-\.]+\@[\w\-\.]+\.([a-z]+)$/' , $data);
		default:
			return preg_match('/^[\w\Q()[]{}#@%+-.\E]+$/', $data);
	}		
 }
 ?>