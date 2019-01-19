<?php
/*
	fix.php is part of makeceefax
	Removes/replaces characters that don't work on teletext.
	Nathan Dane, 2019
*/

function fix_text($text)
{
	$fix_remove=array();
	$fix_replace=array('é'=>'e','á'=>'a','Ó'=>'O');
	
	$text=strtr($text, $fix_replace);
	$text=strip_euro($text);
	
	return $text;
}

function strip_euro($text)
{
	$pos=strpos($text,'€');
	
	if($pos==false)
		return $text;	// If there is no euro symbol return the string unmodified
	
	$cut=strpos($text,' ',$pos);
	$start=substr($text,0,$cut);	// Get the string up to and including the euro
	$end=substr($text,$cut);
	$start=str_replace('€','',$start);
	
	return "$start euros$end";
}