<?php
/*
	makesport.php 
	Creates Ceefax Magazine 3 from the BBC Sport website
	makesport.php is part of makeceefax.php
	Nathan Dane, 2018
*/

require "sportconfig.php";
require "simplesport.php";

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
		$url=$chan->link;
		echo $url."\r\n";
		$name="news".$count;
		$$name=getSport($url,4);
		file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));
		$rstories[]=$$name;
		$count++;
		if ($count>315) break;	// Stop after we get the pages that we want
	}
	}
}