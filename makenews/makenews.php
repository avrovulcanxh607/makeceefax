<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	Nathan Dane, 2018
*/

function newsPage($page,$mpp)
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("News Page $mpp");
	$pheader=pageHeader($mpp);
	$nheader=newsHeader($page[0]);	// Need to add other stuff for this
	$title=outputLine($line,"C",$page[1],21);
	$line+=$title[0];
	$intro=outputLine($line," ",$page[2],21);
	$ln=$line;
	$ln+=$intro[0];
	foreach($page[3] as $element)
	{
		if ($ln>21)
			break;
		if ($found) 
		{
			$ln++;
			$out=outputLine($ln,"F",$element->plaintext,21);
			foreach($out[1] as $line)
			{
				array_push($para,$line);
			}
			$ln+=$out[0];
			
		}
		if (strpos($element,"introduction"))
			$found=true;
	}
	$footer=array("footer goes here");	// Nothing yet!
	return array_merge($inserter,$pheader,$nheader,$title[1],$intro[1],$para,$footer);
}
/*
	array newsPageDecode(DOM $html)
	Decodes title & content of BBC News Pages
*/
function newsPageDecode($html)
{
	$sect =$html->find('meta[property="article:section"]',0)->plaintext;
	$title=$html->find("meta[property=og:title]",0);
	$title=substr ($title,35);
	$title=substr($title, 0, strpos( $title, '"'));
	$body =$html->find('div[class=story-body]');
	$body=str_get_html($body[0]);
	$intro=$body->find('p[class=story-body__introduction]',0)->plaintext;
	$story=$body->find('p');
	return array($sect,$title,$intro,$story);
}

/*
	array newsHeader(str $title)
	Returns an array of lines for the given title
*/
function newsHeader($title="default")
{
	switch ($title)
	{
	case "health" : ;
		break;
	case "technology" : ;
		break;
	case "home" : ;
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
		return array("header goes here");
}

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
		$$name=newsPageDecode($str);
		$count++;
		if ($count>106) break;	// Stop after we get the pages that we want
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
		$$name=newsPageDecode($str);
		$count++;
		if ($count>109) break;	// Stop after we get the pages that we want
	}
} 
$news=file("/home/pi/makeceefax/makenews/pages.txt");

foreach($news as $mpp)
{
	$mpp=rtrim($mpp);
	$name="news".$mpp;
	file_put_contents(PREFIX."$mpp.tti",(newsPage($$name,$mpp)));
}