<?php
/*
	makeweather.php 
	Creates Ceefax Magazine 4 from https://www.metoffice.gov.uk/mobile/forecast/
	makeweather.php is part of makeceefax.php
	Nathan Dane, 2018
*/

require "simpleweather.php";

echo "Loaded MAKEWEATHER.PHP V1 alpha (c) Nathan Dane, 2018\r\n";

function makeweather()
{
	$inhtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gfhyzzs9j");
	$abhtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gfnt07u1s");
	$edhtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcvwr3zrw");
	$behtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcey94cuf");
	$nehtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcybg0rne");
	$mahtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcw2hzs1u");	// Get the latest weather, keep the html in the memory for later
	$sthtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcqkrv0ge");
	$cahtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/u1214b469");
	$crhtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcjszmp44");
	$lohtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcpvj0v07");
	$exhtml=file_get_html("https://www.metoffice.gov.uk/mobile/forecast/gcj2x8gt4");
	weatherMap($inhtml,$abhtml,$edhtml,$behtml,$nehtml,$mahtml,$sthtml,$cahtml,$crhtml,$lohtml,$exhtml);
}

function weatherRegional($html,$tab=0)
{
	$return=array();
	$html=$html->find("div[data-content-id=0]",$tab);
	$titles=$html->find("h4");
	$paragraphs=$html->find("p");
	foreach ($titles as $key => $title)
	{
		$temp=false;
		$outtitle=$title->plaintext;
		$outpara=$paragraphs[$key]->plaintext;
		if (strpos($outpara, 'temperature') !== false)
		{
			$length=strrpos($outpara,".",-5);
			$length++;
			$finalpara=substr($outpara,0,$length);	// Remove "Maximum temperature", but leave the fullstop
			$temp=substr($outpara,($length+1));
			$temp=preg_replace('/[^0-9]/', '',$temp);
			$temp=str_pad($temp,2,'0',STR_PAD_LEFT);
		}
		else
			$finalpara=$outpara;
		$return[]=array($outtitle,$finalpara,$temp);
	}
	return $return;
}

// The following is an example of the worst code in the history of the world. It should be handled with caution.
function weatherMap($inhtml,$abhtml,$edhtml,$behtml,$nehtml,$mahtml,$sthtml,$cahtml,$crhtml,$lohtml,$exhtml)
{
function findWeather($weather)
{
	$output=array();	// All the weather comparison stuff that was here was useless and kept breaking so it's gone.
	$weather=ucwords($weather);
	$weather = str_replace(' Night', '', $weather);
	if (strpos($weather, 'Rain') !== false) 
		$weather = str_replace(' Shower', '', $weather);
	$weather = str_replace(' Day', '', $weather);
	$output=array($weather);
	return $output;
}

function writePage($AB,$BE,$CA,$CR,$ED,$EX,$IN,$LO,$MA,$NE,$ST,$s)
{
	$red="Q";
	$green="R";
	$yellow="S";
	$magenta="U";	// Colours defined here
	$cyan="V";
	$blue="T";
	$white="W";
	$colours=array($cyan,$green,$magenta,$yellow,$blue,$red,$white);
	$cities=array('IN','AB','ED','NE','MA','CR','CA','ST','LO','EX','BE');	// Cities should be in THIS order or things won't work
	$missedcities=$cities;
	$a1=$red;
	$a2=$red;
	$a3=$red;
	$a4=$red;
	$a6=$red;	// All areas are red to begin with
	$a5=$red;
	$a8=$red;
	$a7=$red;
	$a9=$red;
	$a10=$red;
	$a11=$red;

	$units=0;
	$tens=0;
	$places=array();
	while ($tens<10)	// Compare everywhere with everywhere
	{
		if($units==$tens)	// Don't compare the same weather (duh)
			$units++;
		$return=array_intersect(findWeather(${$cities[$tens]}[2]),findWeather(${$cities[$units]}[2]));
		if (!empty($return))
			array_push($places,(array($tens,$units,$return)));	// Add all similar areas to the array
		if($units==10)
		{
			$units=$tens+1;	// No need to compare things twice
			$tens++;
		}
		else
			$units++;
	}
	$grouptitle=array();
	foreach($places as $area)
	{
		$arrayname=$area[2][0];
		if (!isset($$arrayname))
		{
			$$arrayname=array();
			array_push($grouptitle,$arrayname);	// If it isn't there already, add it to the list
		}
		if(!in_array($area[0],$$arrayname))
		{
			array_push($$arrayname,$area[0]);
		}
		if(!in_array($area[1],$$arrayname))
		{
			array_push($$arrayname,$area[1]);
		}
	}
	$weather=array();
	foreach($grouptitle as $arrayname)
	{
		array_push($weather,(array((${$arrayname}[0]),$arrayname,$colours[0])));	// Possibly make the colours more random
		foreach($$arrayname as $area)
		{
			$missedcities[($area)]='';
			$area='a'.($area+1);
			$$area=$colours[0];
		}
		array_shift($colours);
	}
	$missedcities=array_filter($missedcities);	// These are all the places that weren't in groups
	foreach($missedcities as $test)
	{
		$lcity=strtolower($test);
		switch($lcity)
		{
			case 'be' : ;	// Convert cities to numbers.
				$no=11;
				break;
			case 'ca' : ;
				$no=7;
				break;
			case 'cr' : ;
				$no=6;
				break;
			case 'ex' : ;
				$no=10;
				break;
			case 'st' : ;
				$no=8;
				break;
			case 'ed' : ;
				$no=3;
				break;
			case 'in' : ;
				$no=1;
				break;
			case 'ab' : ;
				$no=2;
				break;
			case 'lo' : ;
				$no=9;
				break;
			case 'ma' : ;
				$no=5;
				break;
			case 'ne' : ;
				$no=4;
				break;
		}
		$extra=findweather(${$test}[2]);
		array_push($weather,(array(($no-1),$extra[0],$colours[0])));
		$area='a'.($no);
		$$area=$colours[0];
		if (count($colours)>1)
			array_shift($colours);
	}
	foreach($cities as $city)
	{
		$city=strtolower($city);
		$city1=$city.'1';
		$city2=$city.'2';
		$pad=STR_PAD_LEFT;
		switch($city)
		{
			case 'be' : ;
				$pad=STR_PAD_RIGHT;
				$len=11;
				break;
			case 'ca' : ;
				$len=8;		// Get how long each text area is
				break;
			case 'cr' : ;
			case 'ex' : ;
			case 'st' : ;
				$pad=STR_PAD_RIGHT;
				$len=13;
				break;
			case 'ed' : ;
				$len=15;
				break;
			case 'in' : ;
			case 'ab' : ;
				$len=16;
				break;
			case 'lo' : ;
				$len=10;
				break;
			case 'ma' : ;
				$len=11;
				break;
			case 'ne' : ;
				$len=14;
				break;
		}
		$$city1=str_pad(" ",$len,' ',$pad);		// Make sure they all exist even if they're empty
		$$city2=str_pad(" ",$len,' ',$pad);
	}
	foreach($weather as $text)
	{
		$pad=STR_PAD_LEFT;
		switch(($text[0]+1))	// If space was a problem, these could become a separate function, but it's not so it won't.
		{
			case '01': ;
				$city='in';
				$len=15;
				break;
			case '02': ;
				$city='ab';
				$len=15;
				break;
			case '03': ;
				$city='ed';
				$len=14;
				break;
			case '04': ;
				$city='ne';
				$len=13;
				break;
			case '05': ;
				$city='ma';
				$len=10;
				break;
			case '06': ;
				$city='cr';
				$pad=STR_PAD_RIGHT;
				$len=12;
				break;
			case '07': ;
				$city='ca';
				$len=7;
					break;
			case '08': ;
				$city='st';
				$pad=STR_PAD_RIGHT;
				$len=12;
				break;
			case '09': ;
				$city='lo';
				$len=9;
				break;
			case '10': ;
				$city='ex';
				$pad=STR_PAD_RIGHT;
				$len=12;
				break;
			case '11': ;
				$city='be';
				$pad=STR_PAD_RIGHT;
				$len=10;
				break;
			}
			switch($text[2])
			{
				case "Q": ;
					$textcol="A";	// Make sure the text is the same colour as the area it describes
					break;
				case "R": ;
					$textcol="B";
					break;
				case "S": ;
					$textcol="C";
					break;
				case "U": ;
					$textcol="E";
					break;
				case "V": ;
					$textcol="F";
					break;
				case "T": ;
					$textcol="D";
					break;
				case "W": ;
					$textcol="G";
					break;
			}
			$city1=$city.'1';
			$city2=$city.'2';
			$A=1;
			$utext=explode('\r\n',wordwrap($text[1],$len,'\r\n',true));
			foreach ($utext as $text)
			{
				$city1=$city.$A;
				$$city1=str_pad("$textcol$text",$len+2,' ',$pad); // +2 to take the control code into account.
				$A++;
			}
		}
	$temp=0;				// If its nighttime, get the minimum temp, otherwise get max
	if ($s=='2') $temp=1;
	$title=$AB[4].' '.$AB[6];
	$title=str_replace(':', '', $title);
	$title=str_pad($title,40,' ',STR_PAD_BOTH);	// Centered Title
	$title=substr($title,3);
	return array(
	"OL,1,T]G$title\r\n",
	"OL,2,           ^Z$a1 4`~|}            G $s/2 \r\n",
	"OL,3,           Z$a1`?0~G".$IN[$temp]."$a1"."%  $in1\r\n",
	"OL,4,           $a1Zj**gppp $in2\r\n",
	"OL,5,           ^Z$a2({5                \r\n",
	"OL,6,            Z$a2!'~G".$AB[$temp].""."$a2"."!$ab1\r\n",		// This is a real mess, but it works
	"OL,7,           ^Z$a2 ~ow1 $ab2\r\n",
	"OL,8,$be1^Z$a3*y?sp  $ed1\r\n",
	"OL,9,           ^Z$a3"."j)\"G ".$ED[$temp]."$a3} $ed2\r\n",
	"OL,10,       ^Z$a11"."p|t $a3"."x$a4"."g0 $ne1\r\n",
	"OL,11,       Z$a11"."nG".$BE[$temp]."$a11"."t$a3+\""."$a4".'G'.$NE[$temp]."$a4"." $ne2\r\n",
	"OL,12,       Z$a11+$a4`4$a4+|0             \r\n",
	"OL,13,        Z$a11+%*o!$a4\" $a5 *u  $ma1\r\n",
	"OL,14,$be2  ^Z$a6`  $a5"."*G ".$MA[$temp]."$a5"."} $ma2\r\n",
	"OL,15,$st1^Z$a6"."k|"."$a7"."            \r\n",
	"OL,16,$st2^Z$a6'oG ".$ST[$temp]."$a8"."}zt $ca1\r\n",
	"OL,17,$cr1  Z$a6"."j^"."$a7"."G ".$CA[$temp]."$a8"." $ca2\r\n",
	"OL,18,$cr2Z$a6"."x|^G ".$CR[$temp]."$a8"."'         \r\n",
	"OL,19,             ^Z$a6"."! +/'i"."$a9"."qp0        \r\n",
	"OL,20,$ex1^Z$a10  tG ".$LO[$temp]."$a9"."/!        \r\n",
	"OL,21,$ex2Z$a10 zG".$EX[$temp]."$a10"."^/s/$a9/? $lo1\r\n",
	"OL,22,            Z$a10"."8?' \"'    \"    $lo2\r\n",
	"OL,23,D]G        From the Met Office          \r\n",
	"OL,24,AN.Ire WeathBSportCTrav Head FMain Menu \r\n",
	"FL,402,300,430,100,0,199\r\n");
}

$page=array();
$inserter=pageInserter("Weathermap P401");	// Get all the headers 
$pheader=pageHeader(401,0001);
$iheader=intHeader();

$IN=getWeather($inhtml,0);
$AB=getWeather($abhtml,0);
$ED=getWeather($edhtml,0);
$BE=getWeather($behtml,0);
$NE=getWeather($nehtml,0);
$MA=getWeather($mahtml,0);	// Get the next time
$ST=getWeather($sthtml,0);
$CA=getWeather($cahtml,0);
$CR=getWeather($crhtml,0);
$LO=getWeather($lohtml,0);
$EX=getWeather($exhtml,0);
$page1=writePage($AB,$BE,$CA,$CR,$ED,$EX,$IN,$LO,$MA,$NE,$ST,1);

$IN=getWeather($inhtml,1);
$AB=getWeather($abhtml,1);
$ED=getWeather($edhtml,1);
$BE=getWeather($behtml,1);
$NE=getWeather($nehtml,1);
$MA=getWeather($mahtml,1);	// Get the next time
$ST=getWeather($sthtml,1);
$CA=getWeather($cahtml,1);
$CR=getWeather($crhtml,1);
$LO=getWeather($lohtml,1);
$EX=getWeather($exhtml,1);
$page2=writePage($AB,$BE,$CA,$CR,$ED,$EX,$IN,$LO,$MA,$NE,$ST,2);

$page=array_merge($inserter,$pheader,$iheader,$page1,pageHeader(401,0002),$iheader,$page2);

file_put_contents(PAGEDIR.'/'.PREFIX."401.tti",$page);

//writePage($AB,$BE,$CA,$CR,$ED,$EX,$IN,$LO,$MA,$NE,$ST,2);

}