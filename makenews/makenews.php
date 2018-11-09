<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	makenews.php is part of makeceefax.php
	Nathan Dane, 2018
*/
require "simplenews.php";	// You should have got simplenews.php with this module
require "newsheader.php";

function newsPage($page,$mpp)
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("News Page $mpp $page[4]");
	$pheader=pageHeader($mpp);
	$iheader=intHeader();
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
	$footer=newsFooter($nheader[1],$mpp);
	return array_merge($inserter,$pheader,$iheader,$nheader[0],$title[1],$intro[1],$para,$footer);
}

function makenews()
{
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
			if (!strncmp($title,"/www.bbc.co.uk/news/av/",21))
			{
				continue 1;
			}
			echo $chan->title."\n";
			$name="news".$count;
			$$name=getNews($url,4);	// REEEALLY inefficiant. We're effectively downloading the page twice
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));	// Make the ordinary pages while downloading
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
			if (!strncmp($title,"/www.bbc.co.uk/news/av/",21))
			{
				continue 1;
			}
			echo $chan->title."\n";
			$name="news".$count;
			$$name=getNews($url,4);
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));
			$stories[]=$$name;
			$count++;
			if ($count>124) break;	// Stop after we get the pages that we want
		}
	}
	$count=161;
	$region=strtolower(REGION);
	$region=str_replace(' ','_',$region);
	$rssfeed="http://feeds.bbci.co.uk/news/$region/rss.xml";	// BBC regional stories
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
			if (!strncmp($title,"/www.bbc.co.uk/news/av/",21))
			{
				continue 1;
			}
			echo $chan->title."\n";
			$name="news".$count;
			$$name=getNews($url,4);
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));
			$stories[]=$$name;
			$count++;
			if ($count>169) break;	// Stop after we get the pages that we want
		}
	} 
}
// OK, that's 30 pages of news made. Now for the indexes!