<?php
/*
	makefinance.php
	Generates the Finance Magazine 2 for Ceefax
	Nathan Dane, 2018
*/

require "financeconfig.php";

echo "Loaded MAKEFINANCE.PHP V(indev) (c) Nathan Dane, 2018\r\n";

function makeFinance()
{
	$p1=file_get_html('https://uk.webfg.com/indices/index.html');
	$p2=file_get_html('https://uk.webfg.com/index/FTSE_100');
	$page=array_merge(pageInserter("Finance BBC1 P200"),pageHeader(finindxp),intHeader(),array(
	"OL,1,Wj#3kj#3kj#3kA]W  |,h4xl0xl0xl0|,h<\r\n",
	"OL,2,Wj \$kj \$kj 'kQ]W  #j5j5k5j5pjw\r\n",
	"OL,3,W\"###\"###\"###Q////,/-.,-.,-.,-.,,-,/////\r\n",
	"OL,4,C   BBC1 SHARES PAGES:   FO-P    G229\r\n",
	"OL,5,F   A  G221  FF-G  G225  FQ-R    G230\r\n",
	"OL,6,F   B  G222  FH-I  G226  FS      G231\r\n",
	"OL,7,F   C  G223  FJ-K-LG227  FT-U-V  G232\r\n",
	"OL,8,F   D-EG224  FM-N  G228  FW-X-Y-ZG233\r\n",
	"OL,9,F Share updates 3 times a day: Opening,\r\n",
	"OL,10,F lunchtime,close. Take 20 mins to load\r\n",
	"OL,11,A `````````````````````````````````````\r\n",
	"OL,13,A `````````````````````````````````````\r\n",
	"OL,20,A `````````````````````````````````````\r\n",
	"OL,21,F   Exchange RatesG240F   IndexG199\r\n",
	"OL,22,M] A FULL CITY SERVICE ON BBC2 200\r\n",
	"OL,24,AShares BExchanges CTourRates FMain Menu\r\n",
	"FL,220,240,244,100,100,199\r\n"));
	//$p2=str_get_html($p2);					// Convert the string back to DOM
	$winners=$p2->find("span[class=greenarrow]");	// Find all the winners/losers
	$losers=$p2->find("span[class=redarrow]");
	$nw=(count($winners));		// Count how many there are, divide by 2 because there's 2 coloums
	$nl=(count($losers));		// $Number of Winners $Number of Losers
	
	$ftse100 = getfinanceData($p1,0,1);
	$techmark = getfinanceData($p1,0,7);
	$dow = getfinanceData($p1,1,2);
	$nasdaq = getfinanceData($p1,1,3);
	$nikkei = getfinanceData($p1,3,2);
	$djeurostoxx = getfinanceData($p1,2,8);
	
	$page[]="OL,12,  FTSE100Fat $ftse100[4]GWinnersF$nwGLosersA$nl\r\n";
	$page[]= indxlineFinance(14,"FTSE100",$ftse100[1],$ftse100[2],$ftse100[4]);
	$page[]= indxlineFinance(15,"techMARK100",$techmark[1],$techmark[2],$techmark[4]);
	$page[]= indxlineFinance(16,"Dow Jones",$dow[1],$dow[2],$dow[4]);
	$page[]= indxlineFinance(17,"Nasdaq",$nasdaq[1],$nasdaq[2],$nasdaq[4]);
	$page[]= indxlineFinance(18,"Nikkei",$nikkei[1],$nikkei[2],$nikkei[4]);
	$page[]= indxlineFinance(19,"DJEurStoxx",$djeurostoxx[1],$djeurostoxx[2],$djeurostoxx[4]);
	
	file_put_contents(PAGEDIR.'/'.PREFIX.finindxp.".tti",$page);	// Make the index page
}

function indxlineFinance($OL,$market,$value,$change,$time)
{
	if ($change[0]=='-')
		$change='A'.$change;
	else
	{
		$change='+'.$change;
		$change='F'.$change;
	}
	$market=str_pad($market,11,' ');
	$market=substr($market,0,11);
	
	$value=str_pad($value,8,' ');
	$value=substr($value,0,8);
	
	$change=str_pad($change,9,' ');
	$change=substr($change,0,9);
	
	return ("OL,$OL,C $marketG$value$changeFat $time\r\n");
}

function getfinanceData($html,$area,$share)
{
	$tables=$html->find("table[class=table table-striped]");
	$table=$tables[$area];
	$shares=$table->find("tr");
	$data=$shares[$share];
	
	$name=$data->find("a");
	$name=$name[0]->plaintext;		// Share Name
	$name=str_replace(' ', '', $name);
	
	$data=$data->find("span[class=ls]");
	
	$last=$data[0]->plaintext;		// Last
	$last=str_replace(' ', '', $last);
	$last=str_replace(',', '', $last);
	
	$change=$data[1]->plaintext;		// Change
	$change=str_replace(' ', '', $change);
	
	$percent=$data[2]->plaintext;		// Change %
	$percent=str_replace(' ', '', $percent);
	
	$time=$data[3]->plaintext;		// Time
	$time=substr($time,0,5);
	switch ($area.$share)
	{
		case"01" : ;
		case"07" : ;
			$market="16:00";
			$marketh="17:00";
			break;
		case"12" : ;
		case"13" : ;
		case"28" : ;
			$market="20:30";
			$marketh="23:59";
			break;
		case"32" : ;
			$market="05:30";
			$marketh="07:00";
			break;
	}
	if ($time>$market && $time<$marketh)	// Change anything after 16:00 to 'Close'
		$time="CLOSE";
	
	return (array($name,$last,$change,$percent,$time));
}