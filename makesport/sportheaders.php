<?php
/*
	sportheaders.php 
	Headers for makesport.php
	sportheaders.php is part of makeceefax.php
	Nathan Dane, 2019
*/

function sportHeader($header)
{
	//echo "\r\n$header\r\n";
	switch($header) 
	{
		case "Football" : ;
		case "Bury" : ;
		case "Premier League" : ;
		case "African" : ;
		case "La Liga" : ;
		case "Rangers" : ;
		case "Tottenham" : ;
		case "League Cup" : ;
		case "Wales" : ;
		case "Republic of Ireland" : ;
		case "European Football" : ;
		case "England" : ;
		case "Celtic" : ;
		case "Man Utd" : ;
		case "Sunderland" : ;
		case "Barnsley" : ;
		case "Crystal Palace" : ;
		case "Scotland" : ;
		case "Women's Football" : ;
		case "Norwich" : ;
		case "Champions League" : ;
		case "Liverpool" : ;
		case "Arsenal" : ;
		case "Chelsea" : ;
		case "Southend" : ;
		case "Championship" : ;
			return array(
			"OL,1,Wj#3kj#3kj#3kT]R h<h<|h<|(|$|l4|l4| |\r\n",
			"OL,2,Wj \$kj \$kj 'kT]R j7juju  {4k500\r\n",
			"OL,3,W\"###\"###\"###T///-.-,,-,,/,/,,.,-.,.,.//\r\n");
		case "Formula 1" : ;
			return array(
			"OL,1,Wj#3kj#3kj#3kT]Rh,hlhl <<4444hl hlhlh$  \r\n",
			"OL,2,Wj \$kj \$kj 'kT]Rj#jzj#5555u5ujk jzjjj1  \r\n",
			"OL,3,W\"###\"###\"###T//-/-,-/....,.,--/-,---.//\r\n");
		default :
			echo "sportHeader: $header not recognised\r\n";
			return array(
			"OL,1,Wj#3kj#3kj#3kT]R   h<,h<|h<|h<|(|$      \r\n",
			"OL,2,Wj \$kj \$kj 'kT]R   bsj7#juj7}        \r\n",
			"OL,3,W\"###\"###\"###T/////-,,-./-,,-.,/,///////\r\n");
	}
}

function sportFooter($header,$mpp)
{
	$mpp++;
	if ($mpp == 316)
		$mpp=324;
	switch($header) 
	{
		case "Football" : ;
		case "Bury" : ;
		case "Premier League" : ;
		case "African" : ;
		case "La Liga" : ;
		case "Rangers" : ;
		case "Tottenham" : ;
		case "League Cup" : ;
		case "Wales" : ;
		case "Republic of Ireland" : ;
		case "European Football" : ;
			return array(
			"OL,22,D]CCEEFAX FOOTBALL SECTION PAGE 302\r\n",
			"OL,23,D]CBBC WEBSITE: bbc.co.uk/football\r\n",
			"OL,24,ANext page  BFootball CHeadlines FSport\r\n",
			"FL,$mpp,302,301,300,8FF,199");
		case "Formula 1" : ;
			return array(
			"OL,22,D]CCEEFAX MOTORSPORT SECTION PAGE 360   \r\n",
			"OL,23,D]CBBC WEBSITE: bbc.co.uk/motorsport    \r\n",
			"OL,24,ANext page  BM/sport  CHeadlines FSport \r\n",
			"FL,$mpp,360,301,300,8FF,300\r\n");
		default :
			return array(
			"OL,22,D]CCEEFAX SPORT SECTION PAGE 300\r\n",
			"OL,23,D]CBBC WEBSITE: bbc.co.uk/sport\r\n",
			"OL,24,ANext page  BFootball CHeadlines FSport\r\n",
			"FL,$mpp,302,301,300,8FF,199");
	}
}