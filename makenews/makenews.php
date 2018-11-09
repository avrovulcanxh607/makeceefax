<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	Nathan Dane, 2018
*/
include "simplenews.php";

function newsPage($page,$mpp)
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("News Page $mpp $page[4]");
	$pheader=pageHeader($mpp);
	$nheader=newsHeader($page[4]);	// Need to add other stuff for this
	$title=outputLine($line,"C",$page[0],21);
	$line+=$title[0];
	$intro=outputLine($line," ",$page[5],21);
	$ln=$line;
	$ln+=$intro[0];
	foreach($page[6] as $element)
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
	$footer=array("footer goes here");	// Nothing yet!
	return array_merge($inserter,$pheader,$nheader,$title[1],$intro[1],$para,$footer);
}

/*
	array newsHeader(str $title)
	Returns an array of lines for the given title
*/
function newsHeader($title="default")
{
	switch ($title)
	{
	case "Health" : ;
		return array(
		"OL,1,Wj#3kj#3kj#3kT]S | |h<$|,|h4h||4| | \r\n",
		"OL,2,Wj \$kj \$kj 'kT]S #jw1#ju0j5 #  \r\n",
		"OL,3,W\"###\"###\"###T///,/,-,.,/,-,.-./,/,/////\r\n");
		break;
	case "technology" : ;
		break;
	case "UK" : ;
	case "Cambridgeshire" : ;
		return array(
		"OL,1,Wj#3kj#3kj#3kT]S    h4h4|,|h<<|h<$\r\n",
		"OL,2,Wj \$kj \$kj 'kT]S    j7k5pj55jw1\r\n",
		"OL,3,W\"###\"###\"###T//////-.-.,,,-..,-,.//////\r\n");
		break;
	case "scotland" : ;
		break;
	case "northern ireland" : ;
		break;
	case "wales" : ;
		break;
	case "london" : ;
		break;
	case "World" : ;
		break;
	case "Politics" : ;
		
		break;
	default;
		break;
		}
		return array("OL,2,header goes here\r\n");
}

$stories=array();
$rssfeed="http://feeds.bbci.co.uk/news/uk/rss.xml?edition=uk";	// BBC UK stories
$rawFeed = file_get_contents($rssfeed);
$xml = new SimpleXmlElement($rawFeed);
$count=104;
foreach($xml->channel->item as $chan) {
	// Don't want video/sport stories. They don't render too well on teletext
	if (strncmp($chan->title,"VIDEO:",6)) 
	if (strncmp($chan->link,"http://www.bbc.co.uk/sport/",26))
	{
		$url=$chan->link; 
		$str = file_get_html($url);
		$title=$str->find("link[rel=canonical]");
		$title=substr ($title[0],35);
		$title=substr($title, 0, strpos( $title, '"'));
		echo $title."\n";
		if (!strncmp($title,"www.bbc.co.uk/news/av/",21))
		{
			continue 1;
		}
		echo $chan->title."\n";
		$name="news".$count;
		$$name=getNews($url,4);	// REEEALLY inefficiant. We're effectively downloading the page twice
		file_put_contents(PREFIX."$count.tti",(newsPage($$name,$count)));	// Make the ordinary pages while downloading
		$stories[]=$$name;
		$count++;
		if ($count>115) break;	// Stop after we get the pages that we want
	}
} 
$rssfeed="http://feeds.bbci.co.uk/news/world/rss.xml?edition=uk";	// BBC world stories
$rawFeed = file_get_contents($rssfeed);
$xml = new SimpleXmlElement($rawFeed);
foreach($xml->channel->item as $chan) {
	// Don't want video/sport stories. They don't render too well on teletext
	if (strncmp($chan->title,"VIDEO:",6)) 
    if (strncmp($chan->link,"http://www.bbc.co.uk/sport/",25))
	{
		$url=$chan->link; 
		$str = file_get_html($url);
		$title=$str->find("link[rel=canonical]");
		$title=substr ($title[0],35);
		$title=substr($title, 0, strpos( $title, '"'));
		echo $title."\n";
		if (!strncmp($title,"www.bbc.co.uk/news/av/",21))
		{
			continue 1;
		}
		echo $chan->title."\n";
		$name="news".$count;
		$$name=getNews($url,4);
		file_put_contents(PREFIX."$count.tti",(newsPage($$name,$count)));
		$stories[]=$$name;
		$count++;
		if ($count>124) break;	// Stop after we get the pages that we want
	}
} 
