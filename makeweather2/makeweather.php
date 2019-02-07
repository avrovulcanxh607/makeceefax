<?php
/*
	makeweather.php 
	Creates Ceefax Magazine 4 from https://www.metoffice.gov.uk/mobile/forecast/
	makeweather.php is part of makeceefax.php
	Nathan Dane, 2018
*/
define ("PAGEDIR","/home/pi/ceefax");	// Where do you want your teletext files?
define ("PREFIX","AUTO");	// What do you want the filename prefix to be?
define ("INTHEAD",true);	// Do you want to use the internal page header?
require "api.php";
require "../common.php";

echo "Loaded MAKEWEATHER.PHP V2.0 (c) Nathan Dane, 2019\r\n";

makeweather();
function makeweather()
{
	libxml_use_internal_errors(true);
	$regions=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/sitelist?key=".met_office_api);
	foreach($regions->Location as $region)	// Gets all the regional forecasts. Don't run this too often or you'll hit the limit!
	{
		$id=$region['id'];
		$area=$region['name'];
		$$area=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/$id?key=".met_office_api);
		break;
	}
	weatherRegional($os);
}

function getTemp($in)
{
	$in=substr($in,(strrpos($in,".",-5)+10));
	$in=substr($in,(strrpos($in," ")+1));
	$in=str_replace('.', '',$in);
	return $in;
}

function weatherRegional($xml)
{
	function getRegional($xml,$day=1,$ht,$lt)
	{
		$a=1;
		$summary=$xml->FcstPeriods->Period->Paragraph[$day];
		$para=substr($summary,0,(strrpos($summary,".",-5)+1));
		
		$para=explode('\r\n',wordwrap($para,19,'\r\n'));
		$para=array_pad($para,13,' ');
		foreach($para as $text)
		{
			$$a=substr(str_pad($text,19),0,19);
			$a++;
		}
		
		$title=str_replace(':', '',(strtoupper($xml->FcstPeriods->Period->Paragraph[$day]['title'])));	// title
		
		$dir= 'dir';	// wind dir
		$spd = 'sp';	// wind spd
		$spd.='mph';	// Add MPH
		
		$A=1;
		$output=array(
		"OL,6,C$title\r\n",
		"OL,8,B${$A++}S5CSTATISTICS       \r\n",
		"OL,9,B${$A++}S5C \r\n",
		"OL,10,B${$A++}S5G   Maximum       \r\n",
		"OL,11,B${$A++}S5GTemperatureC$ht"." \r\n",
		"OL,12,B${$A++}S5G                 \r\n",
		"OL,13,B${$A++}S5G   Minumum       \r\n",
		"OL,14,B${$A++}S5GTemperatureC$lt"." \r\n",
		"OL,15,B${$A++}S5C                 \r\n",
		"OL,16,B${$A++}S5G\r\n",
		"OL,17,B${$A++}S5G\r\n",
		"OL,18,B${$A++}S5G                 \r\n",	// Unfortunatley, we can't get wind speed/direction from this feed
		"OL,19,B${$A++}S5G\r\n",
		"OL,20,B${$A++}S5G\r\n");
		return $output;
	}
	if(preg_match('/\bMaximum\b/',$xml->FcstPeriods->Period->Paragraph[1]))
	{
		$ht=getTemp($xml->FcstPeriods->Period->Paragraph[1]);
		$lt=getTemp($xml->FcstPeriods->Period->Paragraph[2]);
	}
	elseif(preg_match('/\bMinimum\b/',$xml->FcstPeriods->Period->Paragraph[1]))
	{
		$ht=getTemp($xml->FcstPeriods->Period->Paragraph[2]);
		$lt=getTemp($xml->FcstPeriods->Period->Paragraph[1]);
	}
	$header=array("OL,1,Wh,,lh,,lh,,lT||,<<l,,|,,|,,<l<l,,<,,l||\r\n",
	"OL,2,Wj 1nj 1nj =nT]Sjj5shw{4k7juz5sjw{%  \r\n",
	"OL,3,W*,,.*,,.*,,.T]Sozz%pj5j5j5j5j5pj5j5  \r\n",
	"OL,4,  N IRELAND  T//-,,/,,-.-.-.-.-.,,-.-.//\r\n");
	$footer=array('OL,22,T]GN IRELANDCHeadlinesG160CSport   G390 '."\r\n",
	"OL,23,D]GNATIONALC Main menuG100CWeatherG 400 "."\r\n",
	"OL,24,AOutlookB NIrelTravC Trav HeadFMain Menu"."\r\n",
	"FL,403,437,430,100,F,199\r\n");
	file_put_contents(PAGEDIR.'/'.PREFIX."402.tti",array_merge(pageInserter("Regional Weather"),pageHeader(402,0001),intHeader(),$header
	,getRegional($xml,$day=1,$ht,$lt),array("OL,21,                                    1/2 \r\n"),$footer,pageHeader(402,0002),intHeader()
	,$header,getRegional($xml,$day=2,$ht,$lt),array("OL,21,                                    2/2 \r\n"),$footer));
}
