<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	Nathan Dane, 2018
*/

/*
	array newsInserter(str $Description, mixed $Time, str $CycleorTimed)
	Returns an array containing the opening lines of a TTI teletext Page.
*/
function newsInserter($de="News Page", $t=10, $ct="t")
{
	return array("DS,inserter\r\n","SP,/home/pi/Pages\r\n","DE,$de\r\n","CT,$t,$ct\r\n");
}
/*
	array newsSubpage(