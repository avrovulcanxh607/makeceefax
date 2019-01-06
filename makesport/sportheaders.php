<?php
/*
	sportheaders.php 
	Headers for makesport.php
	sportheaders.php is part of makeceefax.php
	Nathan Dane, 2019
*/

function sportHeader($header)
{
	return array(
	"OL,1,Wj#3kj#3kj#3kT]R h<h<|h<|(|$|l4|l4| |\r\n",
	"OL,2,Wj \$kj \$kj 'kT]R j7juju  {4k500\r\n",
	"OL,3,W\"###\"###\"###T///-.-,,-,,/,/,,.,-.,.,.//\r\n");
}

function sportFooter($header,$mpp)
{
	$mpp++;
	return array(
	"OL,22,D]CCEEFAX FOOTBALL SECTION PAGE 302\r\n",
	"OL,23,D]CBBC WEBSITE: bbc.co.uk/football\r\n",
	"OL,24,ANext page  BFootball CHeadlines FSport\r\n",
	"FL,$mpp,302,301,300,F,199");
}