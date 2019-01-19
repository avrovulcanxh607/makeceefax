<?php
/*
	makesport.php 
	Creates Ceefax Magazine 3 from the BBC Sport website
	makesport.php is part of makeceefax.php
	Nathan Dane, 2019
*/

require "sportconfig.php";
require "simplesport.php";
require "sportheaders.php";

echo "Loaded MAKESPORT.PHP V0.1 (c) Nathan Dane, 2019\r\n";

function makesport()
{
	sportFootball();
}

function sportFootball()
{
	$count=303;
	$rssfeed="http://feeds.bbci.co.uk/sport/football/rss.xml";	// BBC Football stories
	$time = file_get_contents("makesport/football.rss");
	$rawFeed = file_get_contents($rssfeed);
	$xml = new SimpleXmlElement($rawFeed);
	if ($time == $xml->channel->lastBuildDate) echo "Football News Up-to-date\r\n";
	else
	{
	file_put_contents("makesport/football.rss",$xml->channel->lastBuildDate);
	foreach($xml->channel->item as $chan) {
		if (strncmp($chan->link,"http://www.bbc.co.uk/sport/av/",30) && strncmp($chan->link,"http://www.bbc.co.uk/news",25))
		{
			$url=$chan->link;
			//$url="http://www.bbc.co.uk/sport/46816207";
			echo $url."\r\n";
			$name="sport".$count;
			$$name=getSport($url,4);
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(sportPage($$name,$count)));
			$sportdata[]=$$name;
			$count++;
			if ($count>315) break;	// Stop after we get the pages that we want
		}
	}
	file_put_contents(PAGEDIR.'/'.PREFIX."302.tti",(footballIndex($sportdata)));
	}
}

function sportPage($page,$mpp,$header="test")
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("Sport Page $mpp $page[4]");	// Get all the headers 
	$pheader=pageHeader($mpp);
	$iheader=intHeader();
	$nheader=sportHeader($page[4]);
	$title=outputLine($line,"C",$page[0],21);	// Page title
	$line+=$title[0];
	$intro=outputLine($line," ",$page[5],21);	// Intro
	$ln=$line;
	$ln+=$intro[0];
	foreach($page[6] as $element)	// Paragraphs
	{
		if ($ln>21)
			break;
		$ln++;
		$out=outputLine($ln,"F",$element,22);
		if ($out[1] !== false)
		{
			foreach($out[1] as $line)
			{
				array_push($para,$line);
			}
		}
		$ln+=$out[0];
	}
	$footer=sportFooter($page[4],$mpp);	// Generate footer
	return array_merge($inserter,$pheader,$iheader,$nheader,$title[1],$intro[1],$para,$footer);	// Merge them all in an array to export as page
}

function footballIndex($data)
{
	$inserter=pageInserter("Football Index");	// Get all the headers 
	$pheader=pageHeader(302);	// Hard coded for now
	$iheader=intHeader();
	$header=array(
	"OL,1,Wj#3kj#3kj#3kT]R h<h<|h<|(|$|l4|l4| |\r\n",
	"OL,2,Wj \$kj \$kj 'kT]R j7juju  {4k500\r\n",
	"OL,3,W\"###\"###\"###T///-.-,,-,,/,/,,.,-.,.,.//\r\n");
	$footer=array(
	"OL,22,D]CRESULTS AND FIXTURES SECTIONG339 \r\n",
	"OL,23,D]CBBC WEBSITE: bbc.co.uk/football\r\n",
	"OL,24,ATop story  BRegional CHeadlines FSport\r\n",
	"FL,303,300,301,300,F,199\r\n");
	$i=0;
	$OL=4;
	foreach($data as $page)
	{
		if($i==1 || $i==6 || $i==10)
			$OL++;
		$mpp=(303+$i);	// Hard code for now
		if ($i<1) 	// Only the first headline is double height, then they're cyan
			$textcol='M';	// Double Height
		else
			$textcol='F';	// Cyan
		$headline=myTruncate2($page[0], 35, " ");	// Cut the headline to 35 chars, but at word breaks
		$headline=substr(str_pad($headline,35),0,35);
		$headline.='G';	// White
		$titles[]="OL,$OL,$textcol$headline$mpp\r\n";	// On all subpages
		$i++;
		$OL++;
	}
	return array_merge($inserter,$pheader,$iheader,$header,$titles,$footer);
}