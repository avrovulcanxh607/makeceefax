<?php
/*
	makelistings.php 
	Creates Ceefax style TV listings
	makelistings.php is part of makeceefax.php
	Nathan Dane, 2019
*/

echo "Loaded MAKELISTINGS.PHP V0.1 (c) Nathan Dane, 2019\r\n";

function makelistings()
{
	echo "Getting listings data...";
	$bbc1=array_merge(processData("bbc1_n_ireland",-1),processData("bbc1_n_ireland"),processData("bbc1_n_ireland",1));
	$bbc2=array_merge(processData("bbc2_n_ireland",-1),processData("bbc2_n_ireland"),processData("bbc2_n_ireland",1));
	$ch4=array_merge(processData("ch4",-1),processData("ch4"),processData("ch4",1));
	$ch5=array_merge(processData("five",-1),processData("five"),processData("five",1));
	
	echo "Done\r\nGenerating Pages...";
	processPage($bbc1,601,"bbc1");
	processPage($bbc2,602,"bbc2");
	processPage($ch4,604,"ch4");
	processPage($ch5,605,"ch5");
	
	echo "Done\r\n";
}

function processData($channel="bbc1_n_ireland",$day=0)
{
	$add=false;
	$xml=simplexml_load_file("http://bleb.org/tv/data/rss.php?ch=$channel&day=$day");
	foreach($xml->channel->item as $item)	// Read in all the programmes
	{
		$title=fix_text($item->title);
		$description=fix_text($item->description);
		
		$time=substr($title,0,strpos($title,' :'));	// Extract the time from the title
		$name=substr($title,strpos($title,': ')+2);
		if(strpos($description,"["))	// If there's additional attributes, extract them from the description
		{
			$desc=substr($description,0,strpos($description,' ['));
			$attr=substr($description,strpos($description,'['));
			$attr=str_replace("[","",$attr);
			$attr=str_replace("]","",$attr);
		}
		else
		{
			$desc=$description;
			$attr="";
		}
		if($time>0000 && $time<0600 && !$add)
		{
			$day++;
			$add=true;
		}
		$date=strtotime("+$day day, $time");
		//echo date("D H i",$date)."\r\n"; //debug
		$listings[]=array($date,$name,$desc,$attr);
	}
	array_pop($listings);
	return $listings;
}

function processPage($listings,$mpp,$name,$today=true)
{
	$max=4;
	//print_r($listings);	// debug
	// OK, we've got the data. Now, hows this going to fit into the page?
	if($today)	// If this is today's page...
	{
		$max=3;
		$time=time();
		foreach($listings as $key=>$listing)
		{
			if($listing[0]<$time)
				unset($listings[$key]);	// ...remove all listings before now
		}
		$listings=array_values($listings);	// No real reason for this, just looks nicer 
	}
	//print_r($listings);	// debug
	
	$s=1;
	$pages=array_merge(pageInserter("Ceefax TV Listings",25),pageHeader($mpp,"000".$s),intHeader());
	
	$OL=5;
	foreach($listings as $listing)
	{
		if($OL==5)
			$first=$listing[0];
		$return=listingsTitle($OL,date("Hi",$listing[0]),$listing[1],$listing[3]);
		$OL=$return[1];
		if(!$return)
		{
			echo "Oops.\r\n";
		}
		$pages=array_merge($pages,$return[0]);
		if($today)
		{
			$desc=wordwrap($listing[2],34,"\r\n",true);
			$desc=explode("\r\n",$desc);
			$count=count($desc);
			if($count<(20-$OL))
			if($listing[1] != "Breakfast")
			if($listing[1] != "BBC Newsline")
			if($listing[1] != "BBC News at Six")
			if($listing[1] != "BBC News at Ten")
			if($listing[1] != "BBC News")
			if($listing[1] != "Channel 4 News")
			if($listing[1] != "5 News At 5")
			if($listing[1] != "5 News Tonight")
			{
				foreach($desc as $line)
				{
					$OL++;
					$pages[]="OL,$OL,F     $line\r\n";
				}
			}
			$OL++;
		}
		if($OL>20)
		{
			if(($s+1)>$max)
			{
				$pages=array_merge($pages,listingsHeader($name,$today,"$s/$max",$first,$listing[0]),
				listingsFooter($name,$today));
				break;
			}
			else
				$pages=array_merge($pages,listingsHeader($name,$today,"$s/$max",$first,$listing[0]),
				listingsFooter($name,$today),pageHeader($mpp,"000".$s),intHeader());
			$OL=5;
			$s++;
		}
	}
	//$pages=array_merge($pages,listingsHeader($name,$today,"$s/$max",$first,$listing[0]),listingsFooter($name,$today));
	//print_r($pages);	// debug
	file_put_contents(PAGEDIR.'/'.PREFIX.$mpp.".tti",$pages);
}

function listingsHeader($name,$today,$subpage,$first,$last)
{
	$time1=date("Hi",$first);
	$day1=date("l",$first);
	$time2=date("Hi",$last);
	$day2=date("l",$last);
	
	if($day1==$day2)
		$day=$day1;
	else
		$day=date("D",$first)."-".date("D",$last);
	
	$day=str_pad(strtoupper($day),15," ",STR_PAD_LEFT);
	$time=$time1."-".$time2;
	
	switch($name)
	{
		case "bbc1":
			return array(
			"OL,1,V|,,,,,,l|4F````````````````````````````\r\n",
			"OL,2,V\"!j5j=5S{%{%+%(G $day\r\n",
			"OL,3,V  +>!j5Sz5z5z5`0G      $time\r\n",
			"OL,4,V/,,,,,,.-%F``````````````````````G $subpage \r\n");
		case "bbc2":
			return array(
			"OL,1,V|,,,,,,l|4F````````````````````````````\r\n",
			"OL,2,V\"!j5j=5S{%{%+%bsG$day\r\n",
			"OL,3,V  +>!j5Sz5z5z5jupG      $time\r\n",
			"OL,4,V/,,,,,,.-%F``````````````````````G $subpage \r\n");
		case "ch4":
			return array(
			"OL,1,V|,,,,,,l|4F````````````````````````````\r\n",
			"OL,2,V\"!j5j=5S+%p0 h4 G $day\r\n",
			"OL,3,V  +>!j5Sx4j5 #k7G        $time\r\n",
			"OL,4,V/,,,,,,.-%F``````````````````````G $subpage \r\n");
		case "ch5":
			return array(
			"OL,1,V|,,,,,,l|4F````````````````````````````\r\n",
			"OL,2,V\"!j5j=5S#b1|h4<l  G $day\r\n",
			"OL,3,V  +>!j5S#j5oz%wsG         $time\r\n",
			"OL,4,V/,,,,,,.-%F``````````````````````G $subpage \r\n");
	}
}

function listingsFooter($name,$today=true)
{
	switch($name)
	{
		case "bbc1":
			return array(
			"OL,22,F]DS=Subtitles  AD=Audio Description  \r\n",
			"OL,23,D]FBBC1A601FBBC2A602F C4A604FOn nowA606 \r\n",
			"OL,24,ABBC2    BUTV     CCh 4   FNow & Next  \r\n",
			"FL,602,603,604,606,F,600\r\n");
		case "bbc2":
			return array(
			"OL,22,F]DS=Subtitles  AD=Audio Description  \r\n",
			"OL,23,D]FBBC1A601FBBC2A602F C4A604FOn nowA606 \r\n",
			"OL,24,ACh 4    BCh 5  CNow and Next FTV Links\r\n",
			"FL,604,605,606,615,F,600\r\n");
		case "ch4":
			return array(
			"OL,22,F]DS=Subtitles  AD=Audio Description  \r\n",
			"OL,23,D]FBBC1A601FBBC2A602F C4A604FOn nowA606 \r\n",
			"OL,24,ACh 5   BNow & NextC TV LinksFN.Irel. TV\r\n",
			"FL,605,606,615,600,F,600\r\n");
		case "ch5":
			return array(
			"OL,22,F]DS=Subtitles  AD=Audio Description  \r\n",
			"OL,23,D]FBBC1A601FBBC2A602F C4A604FOn nowA606 \r\n",
			"OL,24,ANow & NextB  Prime  CBBC1  FN.Irel. TV \r\n",
			"FL,606,607,601,600,F,600\r\n");
	}
}

function listingsTitle($OL,$time,$title,$attributes)
{
	$first=true;
	$line=wordwrap("$titleC$attributes",33,"\r\n");
	$lines=explode("\r\n",$line);
	//if((count($lines)+$OL)>20)
		//return false;
	foreach($lines as $line)
	{
		if($first)
		{
			$output[]="OL,$OL,C".$time."G".$line."\r\n";
			$first=false;
		}
		else
		{
			$OL++;
			$output[]="OL,$OL,     G".$line."\r\n";
		}
	}
	return array($output,$OL);
}