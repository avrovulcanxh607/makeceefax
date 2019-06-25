<?php
/*
	makeweather.php 
	Creates Ceefax Magazine 4 from https://www.metoffice.gov.uk/mobile/forecast/
	makeweather.php is part of makeceefax.php
	Nathan Dane, 2019
*/
require "api.php";
require "weatherconfig.php";

echo "Loaded MAKEWEATHER.PHP V2.1 (c) Nathan Dane, 2019\r\n";

function makeweather()
{
	
	libxml_use_internal_errors(true);
	$regions=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/sitelist?key=".met_office_api);
	foreach($regions->Location as $region)	// Gets all the regional forecasts. Don't run this too often or you'll hit the limit!
	{
		$id=$region['id'];
		$area=$region['name'];
		$$area=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/txt/wxobs/regionalforecast/xml/$id?key=".met_office_api);
	}
	
	weatherFront($uk,$ni);
	weatherMap();
	weatherRegional($ni);
	weatherUKoutlook($uk);
	weatherUKfiveday();
	weatherCurrent();
	file_put_contents("makeweather/headlines.txt","Weather	".$uk->FcstPeriods->Period->Paragraph[0]."	401");
}

function weatherFront($uk,$reg)
{
	$headline=$uk->FcstPeriods->Period->Paragraph[0];
	$region=$reg->FcstPeriods->Period->Paragraph[0];
	
	$region=myTruncate2($region,35,".");
	$region=myTruncate2($region,35," ");
	$region=str_pad($region,35);
	$region=strtoupper($region);
	
	$headline="WEATHER NEWS:B".$headline;
	$headline=explode('\r\n',wordwrap($headline,39,'\r\n'));
	$headline2=str_pad($headline[1],35);
	$headline2.="C401";
	
	$body=array(
	"OL,1,Wj#3kj#3kj#3kT]S |hh4|$|l4l<h4|h<h<4    \r\n",
	"OL,2,Wj \$kj \$kj 'kT]S ozz%1k5j5j7jwj7}    \r\n",
	"OL,3,W\"###\"###\"###T///-,,/,.,-.-.-.,-,-.,////\r\n",
	"OL,4,M$regionC402\r\n",
	"OL,6,CUKD````````````````````````````````````\r\n",
	"OL,7,FForecast Maps  C401FWeather WarningC405\r\n",
	"OL,8,FRegions        C402FUK Cities 5 DayC406\r\n",
	"OL,9,FNational       C403FUK Review      C407\r\n",
	"OL,10,FCurrent        C404FEvents         C408\r\n",
	"OL,11,                    FInshore Waters C409\r\n",
	"OL,12,CFIVE DAY FORECASTSD````````````````````\r\n",
	"OL,13,FEurope         C410FS America      C413\r\n",
	"OL,14,FAfrica         C411FAsia           C414\r\n",
	"OL,15,FN America      C412FAustralasia    C415\r\n",
	"OL,16,CEXTRAD`````````````````````````````````\r\n",
	"OL,17,FWeather News   C416FFlood Warnings C419\r\n",
	"OL,18,FPollution IndexC417FSurfing        C429\r\n",
	"OL,19,FSun Index      C419FSkiing         C420\r\n",
	"OL,20,                    FPollen         C426\r\n",
	"OL,21,C$headline[0]\r\n",
	"OL,22,B$headline2\r\n",
	"OL,23,D]G     From the BBC Weather Centre     \r\n",
	"OL,24,AMaps  BWarnings  COutlook  FMain Menu  \r\n",
	"FL,401,405,403,100,100,199\r\n");
	
	file_put_contents(PAGEDIR.'/'.PREFIX."400.tti",array_merge(pageInserter("Weather Front Page"),pageHeader(400,'0000'),intHeader(),$body));
}

function weatherCurrent()
{
	$header=array("OL,1,Wj#3kj#3kj#3kT]S |hh4|$|l4l<h4|h<h<4\r\n",
	"OL,2,Wj \$kj \$kj 'kT]S ozz%1k5j5j7jwj7}\r\n",
	"OL,3,W\"###\"###\"###T///-,,/,.,-.-.-.,-,-.,////\r\n",
	"OL,7,C            temp   wind  pres\r\n",
	"OL,8,                C    mph    mB\r\n");
	
	$footer=array("OL,20,C   pressureFRCrisingGSCsteadyBFCfalling\r\n",
	"OL,24,AWarningsB NIreTV CTrav Head FMain Menu\r\n",
	"FL,405,600,430,100,100,100\r\n");

	$mintemp=100;
	$maxtemp=-100;
	$c="G";
	foreach(current_uk_obs_id as $key => $location)
	{
		$current=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/val/wxobs/all/xml/$location?key=".met_office_api."&res=hourly");
		$period=count($current->DV->Location);
		$rep=count($current->DV->Location->Period[$period]);	// Always get the latest one
		$rep--;
		
		$temp=round($current->DV->Location->Period[$period]->Rep[$rep]['T']);
		$dir=$current->DV->Location->Period[$period]->Rep[$rep]['D'];
		$spd=$current->DV->Location->Period[$period]->Rep[$rep]['S'];
		$press=$current->DV->Location->Period[$period]->Rep[$rep]['P'];
		$tend=$current->DV->Location->Period[$period]->Rep[$rep]['Pt'];
		$weather=weathertostr($current->DV->Location->Period[$period]->Rep[$rep]['W']);
		$time=date("Hi",strtotime($current->DV['dataDate']));
		
		if($mintemp>$temp)
			$mintemp=$temp;
		if($temp>$maxtemp)
			$maxtemp=$temp;
		
		if($c=="F")
			$c="G";
		else
			$c="F";
		$rf="  ";
		switch ($tend)
		{
		case "R" : ;
			$rf="FR";
			break;
		case "S" : ;
			$rf="GS";
			break;
		case "F" : ;
			$rf="BF";
			break;
		}
		
		$press=str_pad($press,4,' ',STR_PAD_LEFT);
		$spd=str_pad($spd,2,' ',STR_PAD_LEFT);
		$dir=str_pad($dir,3,' ',STR_PAD_LEFT);
		$temp=str_pad($temp,2,' ',STR_PAD_LEFT);
		$weather=str_pad($weather,7,' ');
		$name=str_pad(current_uk_obs_nm[$key],13,' ');
		
		$lines[]="$name $temp $dir $spd  $press$rf$c$weather\r\n";
	}
	$count=count($lines);
	$subpages=(int) ($count / 10);
	if($count % 10 != 0)
		$subpages++;
	$OL=9;
	$ss=1;
	$out=array_merge(pageInserter("UK Current Weather",30),pageHeader(404,"0001","8000"),intHeader(),$header);
	foreach($lines as $key=>$line)
	{
		if($OL>18)
		{
			$out=array_merge($out,array("OL,4,                                    $ss/$subpages \r\n",
			"OL,5,CCURRENT UK WEATHER: Report at $time\r\n"),converterBar($mintemp,$maxtemp),$footer,
			pageHeader(404,"000".($ss+1),"8000"),intHeader(),$header);
			$OL=9;
			$ss++;
		}
		if($key % 2 == 0)
			$c="F";
		else
			$c="G";
		
		$out=array_merge($out,array("OL,$OL,$c$line"));
		$OL++;
	}
	$out=array_merge($out,array("OL,4,                                    $ss/$subpages \r\n",
	"OL,5,CCURRENT UK WEATHER: Report at $time\r\n"),converterBar($mintemp,$maxtemp),$footer);
	file_put_contents(PAGEDIR.'/'.PREFIX."404.tti",$out);
}

function weathertostr($in)
{
	switch($in)
	{
		case 0 : ;
			return "clear";
		case 1 : ;
			return "sunny";
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
		$return1=outputLine($OL,"G",$title,22);
		$OL+=$return1[0];
		$return2=outputLine($OL,"F",$paragraph,22);
		$OL+=$return2[0];
		if(is_array($return1[1]) && is_array($return2[1]))
		{
			$OL++;
			$out=array_merge($out,$return1[1],$return2[1],array("OL,4,                                    1/2 \r\n"));
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
			$out=array_merge($out,$return1[1],$return2[1],array("OL,4,                                    2/2 \r\n"));
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
	/*
	if(false !== ($breakpoint = strpos($forecast, "."))) {
		$forecast = substr($forecast, 0, $breakpoint);
	}
	
	if(false !== ($breakpoint = strpos($forecast, ","))) {
		$forecast = substr($forecast, 0, $breakpoint);
	}
	*/
	$temp=str_replace('C', '',$temp);
	$temp=str_pad($temp,2,"0",STR_PAD_LEFT);
	
	$title=str_replace(':', '',($xml->FcstPeriods->Period->Paragraph[$type]['title']));
	
	//echo "$temp $forecast \r\n";		//debug
	return array($temp,$temp,$forecast,"",$title,"");	// Emulate simpleweather.php for now
}

function weatherMap()
{
	$evening=0;
	if(date("H")>17 || date("H")<6)
		$evening=1;
	$first=drawMap(0,$evening,1);
	$second=drawMap(1,0,2);
	file_put_contents(PAGEDIR.'/'.PREFIX."401.tti",array_merge(pageInserter("Weather Map"),pageHeader(401,"0001"),intHeader(),
	$first,pageHeader(401,"0002"),intHeader(),$second));
}

function drawMap($period=0,$rep=0,$s=1)
{
	libxml_use_internal_errors(true);
	//	          ST      AB     ED     NE     HU     NO     LO     PL     CA     BI     BE
	$cities=array(353720,310170,351351,352790,310093,352876,352409,310016,350758,310002,350347);
	$tempu="Nm";
	foreach($cities as $id)
	{
		$area=simplexml_load_file("http://datapoint.metoffice.gov.uk/public/data/val/wxfcs/all/xml/$id?res=daily&key=".met_office_api);
		if($rep==0)$tempu="Dm";
		$date=$area->DV->Location->Period[$period]["value"];
		$areatemp=round($area->DV->Location->Period[$period]->Rep[$rep][$tempu]);
		$areaweather=weatherstrmap($area->DV->Location->Period[$period]->Rep[$rep]['W']);
		$weather[$id]=$areaweather;
		$temp[$id]="$areatemp";
	}
	//echo $date;//debug
	//print_r($weather);//debug
	//print_r($temp);//debug
	
	// Main comparison 'algorithm'. Produces an array containing all the areas with the same weather,
	// but not grouped. Array contains both statements so that we can use the shortest of either later on,
	// as well as the result of running it through findWeather for de-bugging or future use.
	foreach($weather as $key1=>$place1)
	{
		foreach($weather as $key2=>$place2)
		{
			if($key1==$key2) continue;	// Don't compare the same place
			if($key1>$key2) continue;	// Don't do stuff twice
			
			$cmp=strcmp($place1,$place2);
			if($cmp==0)
			{
				$areas[]=array($key1,$key2,$place1);
			}
			if(!isset($otherareas[$key1]))
			{
				$otherareas[$key1]=array($place1);
			}
			if(!isset($otherareas[$key2]))
			{
				$otherareas[$key2]=array($place2);
			}
		}
	}
	//print_r($areas);	//debug
	//print_r($otherareas);//debug
	// Now to make all the groups and work out colours. Produce an array in which is each group, it's members and it's title text.
	// $grouplist = all the areas we have defined
	// $$arrayname ends up with all the areas numbered in it
	foreach($areas as $area)
	{
		$arrayname=$area[2];
		if (!isset($$arrayname))
		{
			$$arrayname=array($area[2]);
			$grouplist[]=$arrayname;
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
	//print_r($grouplist);//debug
	//print_r($$arrayname);//debug
	// OK, we've got all we need, now just package it up into another array
	foreach($grouplist as $group)
	{
		$finalareas[$group]=array(array(array_shift($$group)),$$group);
	}
	//print_r($finalareas);//debug
	// Non-groups
	foreach($otherareas as $key=>$area)
	{
		$arrayname=$area[0];
		if (!isset($$arrayname))
		{
			foreach($finalareas as $key2=>$finalarea)
			{
				if(in_array($key,$finalarea[1]))
				{
					array_push($finalareas[$key2][0],"$area[0]");
					continue 2;
				}
			}
			$finalareas["$area[0]"]=array(array($area[0]),array($key));
		}
	}
	//print_r($finalareas);//debug	// Final areas output array
	// What's next? Now we must work out where everything needs to go. So what colour each group is, where it can put text
	// and, of course, all the temperatures.
	$red="Q";
	$green="R";
	$yellow="S";
	$magenta="U";	// Colours
	$cyan="V";
	$blue="T";
	$white="W";
	$colours=array($cyan,$green,$magenta,$yellow,$blue,$red,$white);
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
	foreach($cities as $city)	// Make sure all text areas are the right size
	{
		$return=mapText(" ",$city,"R");
		$stringname=$return[2]."1";
		$$stringname=$return[0];
		$stringname=$return[2]."2";
		$$stringname=$return[0];
	}
	
	// Generate groups, colours and text!
	foreach($finalareas as $finalarea)
	{
		$return=mapText($finalarea[0][0],$finalarea[1][0],$colours[0]);
		$stringname=$return[2]."1";
		$$stringname=$return[0];
		$stringname=$return[2]."2";
		$$stringname=$return[1];
		foreach($finalarea[1] as $areaname)
		{
			switch($areaname)
			{
			case 350347 : ;	// Convert cities to numbers.
				$no=11;
				break;
			case '352876' : ;
				$no=7;
				break;
			case '350758' : ;
				$no=6;
				break;
			case '310016' : ;
				$no=10;
				break;
			case '310002' : ;
				$no=8;
				break;
			case '351351' : ;
				$no=3;
				break;
			case '353720' : ;
				$no=1;
				break;
			case '310170' : ;
				$no=2;
				break;
			case '352409' : ;
				$no=9;
				break;
			case '310093' : ;
				$no=5;
				break;
			case '352790' : ;
				$no=4;
				break;
			}
			$area='a'.($no);
			$$area=$colours[0];
		}
		if (count($colours)>1)
			array_shift($colours);
	}
	// Now we need to make a title for the top of the page
	$todaysdate=date("d");
	$pagedate=date("d",strtotime($date));	// Is this page for today?
	$difference=$pagedate-$todaysdate;
	//echo "$todaysdate $pagedate $difference\r\n";//debug
	if($difference==0 && date("H")>6)
	{
		if($rep>0)
			$title="This Evening";
		else
			$title="Today";
	}
	elseif($difference==1 && date("H")>6)
	{
		$title="Tomorrow";
		if($rep>0)
			$title.=" Evening";
	}
	else
	{
		$title=date("l",strtotime($date));
		if($rep>0)
			$title.=" Evening";
	}
	$title=str_pad($title,40," ",STR_PAD_BOTH);
	$title=substr($title,3);
	
	// I REALLY couldn't be bothered to go through all this and change the string names, so here's a block of conversion
	// code instead. 
	// Temps
	$IN=str_pad($temp[353720],2,"0",STR_PAD_LEFT); $AB=str_pad($temp[310170],2,"0",STR_PAD_LEFT); $ED=str_pad($temp[351351],2,"0",STR_PAD_LEFT);
	$BE=str_pad($temp[350347],2,"0",STR_PAD_LEFT); $NE=str_pad($temp[352790],2,"0",STR_PAD_LEFT); $MA=str_pad($temp[310093],2,"0",STR_PAD_LEFT);
	$ST=str_pad($temp[310002],2,"0",STR_PAD_LEFT); $CA=str_pad($temp[352876],2,"0",STR_PAD_LEFT); $CR=str_pad($temp[350758],2,"0",STR_PAD_LEFT); 
	$LO=str_pad($temp[352409],2,"0",STR_PAD_LEFT); $EX=str_pad($temp[310016],2,"0",STR_PAD_LEFT);
	
	return array(
	"OL,1,T]G$title\r\n",
	"OL,2,           ^Z$a1 4`~|}            G $s/2 \r\n",
	"OL,3,           Z$a1`?0~G".$IN."$a1"."%  $in1\r\n",
	"OL,4,           $a1Zj**gppp $in2\r\n",
	"OL,5,           ^Z$a2({5                \r\n",			// Remember this?
	"OL,6,            Z$a2!'~G".$AB.""."$a2"."!$ab1\r\n",		// This is a real mess, but it works
	"OL,7,           ^Z$a2 ~ow1 $ab2\r\n",
	"OL,8,$be1^Z$a3*y?sp  $ed1\r\n",
	"OL,9,           ^Z$a3"."j)\"G ".$ED."$a3} $ed2\r\n",
	"OL,10,       ^Z$a11"."p|t $a3"."x$a4"."g0 $ne1\r\n",
	"OL,11,       Z$a11"."nG".$BE."$a11"."t$a3+\""."$a4".'G'.$NE."$a4"." $ne2\r\n",
	"OL,12,       Z$a11+$a4`4$a4+|0             \r\n",
	"OL,13,        Z$a11+%*o!$a4\" $a5 *u  $ma1\r\n",
	"OL,14,$be2  ^Z$a6`  $a5"."*G ".$MA."$a5"."} $ma2\r\n",
	"OL,15,$st1^Z$a6"."k|"."$a7"."            \r\n",
	"OL,16,$st2^Z$a6'oG ".$ST."$a8"."}zt $ca1\r\n",
	"OL,17,$cr1  Z$a6"."j^"."$a7"."G ".$CA."$a8"." $ca2\r\n",
	"OL,18,$cr2Z$a6"."x|^G ".$CR."$a8"."'         \r\n",
	"OL,19,             ^Z$a6"."! +/'i"."$a9"."qp0        \r\n",
	"OL,20,$ex1^Z$a10  tG ".$LO."$a9"."/!        \r\n",
	"OL,21,$ex2Z$a10 zG".$EX."$a10"."^/s/$a9/? $lo1\r\n",
	"OL,22,            Z$a10"."8?' \"'    \"    $lo2\r\n",
	"OL,23,D]G        From the Met Office          \r\n",
	"OL,24,AN.Ire WeathBSportCTrav Head FMain Menu \r\n",
	"FL,402,300,430,100,0,199\r\n");
}

function mapText($text,$target,$colour)
{
	$pad=STR_PAD_LEFT;
	switch($target)
	{
		case '353720': ;
			$city='in';
			$len=15;
			break;
		case '310170': ;
			$city='ab';
			$len=15;
			break;
		case '351351': ;
			$city='ed';
			$len=14;
			break;
		case '352790': ;
			$city='ne';
			$len=13;
			break;
		case '310093': ;
			$city='ma';
			$len=10;
			break;
		case '350758': ;
			$city='cr';
			$pad=STR_PAD_RIGHT;
			$len=12;
			break;
		case '352876': ;
			$city='ca';
			$len=7;
				break;
		case '310002': ;
			$city='st';
			$pad=STR_PAD_RIGHT;
			$len=12;
		break;
		case '352409': ;
			$city='lo';
			$len=9;
			break;
		case '310016': ;
			$city='ex';
			$pad=STR_PAD_RIGHT;
			$len=12;
			break;
		case '350347': ;
			$city='be';
			$pad=STR_PAD_RIGHT;
			$len=10;
			break;
		}
		switch($colour)
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
	$A=1;
	$utext=explode('\r\n',wordwrap(str_pad($text,($len*2)),$len,'\r\n',true));
	foreach ($utext as $text)
	{
		$city1="string".$A;
		$$city1=str_pad("$textcol$text",$len+2,' ',$pad); // +2 to take the control code into account.
		$A++;
	}
	return array($string1,$string2,$city);
}

function weatherstrmap($in)
{
	switch($in)
	{
		case 0 : ;
			return "Clear";
		case 1 : ;
			return "Sunny";
		case 2 : ;
		case 3 : ;
			return "Some Cloud";
		case 5 : ;
			return "Misty";
		case 6 : ;
			return "Foggy";
		case 7 : ;
			return "Cloudy";
		case 8 : ;
			return "Overcast";
		case 9 : ;
		case 10 : ;
		case 12 : ;
			return "Light Rain";
		case 11 : ;
			return "Drizzle";
		case 13 : ;
		case 14 : ;
		case 15 : ;
			return "Heavy Rain";
		case 16 : ;
		case 17 : ;
		case 18 : ;
			return "Sleet";
		case 19 : ;
		case 20 : ;
		case 21 : ;
			return "Hail";
		case 22 : ;
		case 23 : ;
		case 24 : ;
			return "Light Snow";
		case 25 : ;
		case 26 : ;
		case 27 : ;
			return "Heavy Snow";
		case 28 : ;
		case 29 : ;
		case 30 : ;
			return "Thunder";
		default;
			return "n/a";
	}
}