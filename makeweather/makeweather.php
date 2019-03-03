<?php
/*
	makeweather.php 
	Creates Ceefax Magazine 4 from https://www.metoffice.gov.uk/mobile/forecast/
	makeweather.php is part of makeceefax.php
	Nathan Dane, 2019
*/
require "api.php";
require "weatherconfig.php";

echo "Loaded MAKEWEATHER.PHP V2.0 (c) Nathan Dane, 2019\r\n";

/*	// Case the script needs to be run on its own
define ("PAGEDIR","/home/pi/ceefax");	// Where do you want your teletext files?
define ("PREFIX","AUTO");	// What do you want the filename prefix to be?
define ("INTHEAD",true);	// Do you want to use the internal page header?
require "../common.php";
require "../fix.php";
makeweather();
*/

function makeweather()
{
	libxml_use_internal_errors(true);
	/*
	$time = file_get_contents("makeweather/last.upd");
	$date=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxfcs/regionalforecast/xml/capabilities?key=".met_office_api);
	$date=$date[0]["issuedAt"];
	if (false)//($date==$time) They don't seem to update this very often.
	{
		echo "Weather Up-to-date\r\n";
		return;
	}
	file_put_contents("makeweather/last.upd",$date);
	*/
	
	$regions=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/sitelist?key=".met_office_api);
	foreach($regions->Location as $region)	// Gets all the regional forecasts. Don't run this too often or you'll hit the limit!
	{
		$id=$region['id'];
		$area=$region['name'];
		$$area=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/$id?key=".met_office_api);
	}
	
	weatherRegional($ni);
	weatherUKoutlook($uk);
	weatherUKfiveday();
	
	file_put_contents(PAGEDIR.'/'.PREFIX."401.tti",array_merge(pageInserter("Weather Map"),pageHeader(401,"0001"),intHeader(),
	drawMap(weatherData($ta),weatherData($ni),weatherData($ee),weatherData($wl),weatherData($dg),weatherData($sw),weatherData($he),
	weatherData($se),weatherData($em),weatherData($ne),weatherData($wm),1),pageHeader(401,"0002"),intHeader(),drawMap(weatherData($ta,2),
	weatherData($ni,2),weatherData($ee,2),weatherData($wl,2),weatherData($dg,2),weatherData($sw,2),weatherData($he,2),weatherData($se,2),
	weatherData($em,2),weatherData($ne,2),weatherData($wm,2),2)));
	
}

function weathertostr($in)
{
	switch($in)
	{
		case 0 : ;
			return "Clear";
		case 1 : ;
			return "Sunny";
		case 2 : ;
		case 3 : ;
			return "pt cldy";
		case 5 : ;
			return "mist";
		case 6 : ;
			return "fog";
		case 7 : ;
			return "cloudy";
		case 8 : ;
			return "ovrcast";
		case 9 : ;
		case 10 : ;
		case 12 : ;
			return "lt rain";
		case 11 : ;
			return "drizzle";
		case 13 : ;
		case 14 : ;
		case 15 : ;
			return "hy rain";
		case 16 : ;
		case 17 : ;
		case 18 : ;
			return "sleet";
		case 19 : ;
		case 20 : ;
		case 21 : ;
			return "hail";
		case 22 : ;
		case 23 : ;
		case 24 : ;
			return "lt snow";
		case 25 : ;
		case 26 : ;
		case 27 : ;
			return "hy snow";
		case 28 : ;
		case 29 : ;
		case 30 : ;
			return "thunder";
		default;
			return "n/a";
	}
}

function c2f($in)
{
	$out=$in*9/5+32;
	$out=substr(trim($out),0,2);
	$out=str_pad($out,2,' ',STR_PAD_LEFT);
	return $out;
}

function converterBar($mintemp,$maxtemp)
{
	$line1= "OL,22,D]GC= ".(str_pad($mintemp,2,' ',STR_PAD_LEFT))." ".(str_pad(($mintemp+2),2,' ',STR_PAD_LEFT))." ".str_pad(($mintemp+4),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+6),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+8),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+10),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+12),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+14),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+16),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+18),2,' ',STR_PAD_LEFT)." ".str_pad(($mintemp+20),2,' ',STR_PAD_LEFT)." \r\n";
	$line2= "OL,23,D]GF= ".c2f($mintemp)." ".c2f($mintemp+2)." ".c2f($mintemp+4)." ".c2f($mintemp+6)." ".c2f($mintemp+8)." ".c2f($mintemp+10)." ".c2f($mintemp+12)." ".c2f($mintemp+14)." ".c2f($mintemp+16)." ".c2f($mintemp+18)." ".c2f($mintemp+20)." \r\n";
	return array($line1,$line2);
}

function weatherUKfiveday()
{
	$header=array("OL,1,Wj#3kj#3kj#3kT]S |hh4|$|l4l<h4|h<h<4    \r\n",
	"OL,2,Wj \$kj \$kj 'kT]S ozz%1k5j5j7jwj7}    \r\n",
	"OL,3,W\"###\"###\"###T///-,,/,.,-.-.-.,-,-.,////\r\n",
	"OL,6,Bmax for 0600-1800   min for 1800-0600  \r\n",
	"OL,7,C    max minGC          Cmax minGC      \r\n");
	$footer=array("OL,24,AReview  B Sport  CTrav Head FMain Menu \r\n",
	"FL,406,600,430,100,100,199\r\n");

	$i=1;
	$ss=1;
	$mintemp=100;
	$maxtemp=-100;
	$page=pageInserter("UK 5 Day Weather",30);
	$subpage=(count(five_day_forecast)/4);
	foreach(five_day_forecast as $id)
	{
		$data=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/datatype/$id?res=daily&key=".met_office_api);
		$oe=0;
		$name=$data->DV->Location['name'];
		$output[$i][]="G".str_pad(ucwords(strtolower($name)),19);
		foreach($data->DV->Location->Period as $day)
		{
			$odate=$day["value"];
			$htemp=$day->Rep["Dm"];
			$ltemp=$day->Rep[1]["Nm"];
			$weather=$day->Rep["W"];
			
			$date=date("D",strtotime($odate));
			$htemp=str_pad($htemp,2," ",STR_PAD_LEFT);
			$ltemp=str_pad($ltemp,2," ",STR_PAD_LEFT);
			if($ltemp<$mintemp)
				$mintemp=$ltemp;
			if($htemp<$maxtemp)
				$maxtemp=$htemp;
			$weather=str_pad(weathertostr($weather),7);
			
			if($oe%2==0)$colour="F";
			else $colour="G";
			$output[$i][]="$colour$date  $htemp  $ltemp $weather";
			if($oe==0) $pdate=date("j M",strtotime($odate));
			$oe++;
		}
		if($i%4==0)
		{
			$temppage=array();
			for($OL=8;$OL<21;$OL++)
			{
				$element=$OL-8;
				$city=1;
				if($element>5 && $OL<15) {
					$element-=6;
					$city=3;
				}
				elseif($element>5 && $OL>14) {
					$element-=7;
					$city=3;
				}
				if($OL==14) $OL++;
				$temppage=array_merge($temppage,array("OL,$OL,".$output[$city][$element].$output[$city+1][$element]."\r\n"));
			}
			$counter="";
			if($subpage>1)
				$counter="$ss/$subpage";
			$dateline=array("OL,4,                                    $counter\r\n",
			"OL,5,CUK FIVE DAY FORECAST FROM $pdate\r\n");
			$page=array_merge($page,pageHeader(406,"000$ss"),intHeader(),$header,$dateline,$temppage,converterBar($mintemp,$maxtemp),$footer);
			$ss++;
			$i=0;
			$output=array();
		}
		$i++;
	}
	file_put_contents(PAGEDIR.'/'.PREFIX."406.tti",$page);
}

function weatherUKoutlook($xml)
{
	$OL=7;
	$header=array("OL,1,Wj#3kj#3kj#3kT]S |hh4|$|l4l<h4|h<h<4    \r\n",
	"OL,2,Wj \$kj \$kj 'kT]S ozz%1k5j5j7jwj7}    \r\n",
	"OL,3,W\"###\"###\"###T///-,,/,.,-.-.-.,-,-.,////\r\n",
	"OL,5, UK WEATHER OUTLOOK                     \r\n");
	$footer=array("OL,23,D]G        From the Met Office          \r\n",
	"OL,24,AUK cities BSport CTrav Head FMain Menu \r\n",
	"FL,404,300,430,100,100,100\r\n");
	$out=array_merge(pageInserter("UK Weather Outlook",30),pageHeader(403,"0001","c000"),intHeader(),$header);
	for($i=1; $i<3; $i++)
	{
		$title=$xml->FcstPeriods->Period[0]->Paragraph[$i]["title"];
		$paragraph=$xml->FcstPeriods->Period[0]->Paragraph[$i];
		if($OL>22)
		{
			$out=array_merge($out,$footer,$header);
			$OL=7;
		}
		$title=str_replace(":","",$title);
		$return1=outputLine($OL,"G",$title,23);
		$OL+=$return1[0];
		$return2=outputLine($OL,"F",$paragraph,23);
		$OL+=$return2[0];
		if(is_array($return1[1]) && is_array($return2[1]))
		{
			$OL++;
			$out=array_merge($out,$return1[1],$return2[1]);
		}
	}
	$out=array_merge($out,$footer,pageHeader(403,"0002","c000"),intHeader(),$header);
	$OL=7;
	for($i=0; $i<2; $i++)
	{
		$title=$xml->FcstPeriods->Period[1]->Paragraph[$i]["title"];
		$paragraph=$xml->FcstPeriods->Period[1]->Paragraph[$i];
		if($OL>22)
		{
			$out=array_merge($out,$footer,pageHeader(403,"0002","c000"),intHeader(),$header);
			$OL=7;
		}
		$title=str_replace(":","",$title);
		$return1=outputLine($OL,"G",$title,23);
		$OL+=$return1[0];
		$return2=outputLine($OL,"F",$paragraph,23);
		$OL+=$return2[0];
		if(is_array($return1[1]) && is_array($return2[1]))
		{
			$OL++;
			$out=array_merge($out,$return1[1],$return2[1]);
		}
	}
	file_put_contents(PAGEDIR.'/'.PREFIX."403.tti",array_merge($out,$footer));
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
	file_put_contents(PAGEDIR.'/'.PREFIX."402.tti",array_merge(pageInserter("Regional Weather",30),pageHeader(402,"0001"),intHeader(),$header
	,getRegional($xml,$day=1,$ht,$lt),array("OL,21,                                    1/2 \r\n"),$footer,pageHeader(402,"0002"),intHeader()
	,$header,getRegional($xml,$day=2,$ht,$lt),array("OL,21,                                    2/2 \r\n"),$footer));
}

function weatherData($xml,$type=1)
{
	$temp=getTemp($xml->FcstPeriods->Period->Paragraph[$type]);
	$forecast=$xml->FcstPeriods->Period->Paragraph[$type];
	
	if(false !== ($breakpoint = strpos($forecast, "."))) {
		$forecast = substr($forecast, 0, $breakpoint);
	}
	
	if(false !== ($breakpoint = strpos($forecast, ","))) {
		$forecast = substr($forecast, 0, $breakpoint);
	}
	
	$temp=str_replace('C', '',$temp);
	$temp=str_pad($temp,2,"0",STR_PAD_LEFT);
	
	$title=str_replace(':', '',($xml->FcstPeriods->Period->Paragraph[$type]['title']));
	
	//echo "$temp $forecast \r\n";		//debug
	return array($temp,$temp,$forecast,"",$title,"");	// Emulate simpleweather.php for now
}

function findWeather($weather)
{
	$output=array();
	$verb=false;
	$previous='';
	
	$adjectives=array("Clear","Sunny","Cloudy","Misty","Foggy","Overcast","Rain","Drizzle","Shower","Sleet","Hail","Snow","Thunder","Dry",
	"Fine","Bright","Damp","Wet","Windy","Murky","Showery","Heavy");	// Words that usually stand alone
	$nouns=array("Cloud");	// Words we expect to be followed by a verb, e.g. "Clearing", "Moving", etc
	$verbs=array("Clearing");	// Words that follow nouns
	
	foreach((preg_split('/\s+/', $weather)) as $word)
	{
		$word=ucfirst($word);
		if(in_array($word,$adjectives))
		{
			$output=array_merge($output,array($word)); 
			continue;
		}
		if(in_array($word,$nouns)) 
		{
			$verb=true;
			$previous=$word;
			//echo "noun! "; //debug
			continue;
		}
		if(in_array($word,$verbs) && $verb)
		{
			$verb=false;
			$output=array_merge($output,array("$previous $word"));
		}
	}
	//print_r($output);		//debug
	return $output;
}

function drawMap($AB,$BE,$CA,$CR,$ED,$EX,$IN,$LO,$MA,$NE,$ST,$s)
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
			array_push($places,(array($tens,$units,array_values($return))));	// Add all similar areas to the array
		if($units==10)
		{
			$units=$tens+1;	// No need to compare things twice
			$tens++;
		}
		else
			$units++;
	}
	$grouptitle=array();
	//print_r($places);				// debug
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
		echo "test line: ".${$test}[2]."\r\n";	//debug
		$extra=findweather(${$test}[2]);
		print_r($extra);	//debug
		array_push($weather,(array(($no-1),$extra[0],$colours[0])));	// 
		//print_r($weather);		//debug
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
	$title=$AB[4];
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
