<?php
/*
	makeentertainment.php
	Generates the Ceefax Entertainment section
	makeentertainment.php is part of makeceefax.php
	Nathan Dane, 2019
*/
//require "simplenews.php";

function makeentertainment()
{
	$count=502;
	$rssfeed="http://feeds.bbci.co.uk/news/entertainment_and_arts/rss.xml";	// BBC Entertainment stories
	$time = file_get_contents("makeentertainment/expire.rss");
	$rawFeed = file_get_contents($rssfeed);
	$xml = new SimpleXmlElement($rawFeed);
	if ($time == $xml->channel->lastBuildDate) echo "Entertainment News Up-to-date\r\n";
	else
	{
	file_put_contents("makeentertainment/expire.rss",$xml->channel->lastBuildDate);
	foreach($xml->channel->item as $chan) 
		{
			// Don't want video/sport stories. They don't render too well on teletext
			if (strcasecmp($chan->title,"in pictures") && strncmp($chan->link,"https://www.bbc.co.uk/sport/",28) 
			&& strncmp($chan->link,"https://www.bbc.co.uk/news/av/",30))
			{
				$url=$chan->link;
				echo $url."\r\n";
				$name="ent".$count;
				$$name=getNews($url,4);
				if ($$name===false) 
				{
					echo "simplenews.php detected a problem with this page\r\n";
					continue 1;	// Don't even try to run a failed page
				}
				file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(entertaimentPage($$name,$count)));
				$entstories[]=$$name;
				$count++;
				if ($count>514) break;	// Stop after we get the pages that we want
			}
		}
		file_put_contents(PAGEDIR.'/'.PREFIX."501.tti",(entertaimentIndex($entstories)));
		file_put_contents("makeentertainment/headlines.txt","Entertainment News	".$entstories[0][0]."	502");
	}
}

function entertaimentPage($page,$mpp)	// Makes all the actual stories, wonder if this would be better as a common function?
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("Entertainment Page $mpp $page[4]");	// Get all the headers 
	$pheader=pageHeader($mpp);
	$iheader=intHeader();
	$nheader=array(
	"OL,1,C]U|\$xl0l<h<h<t(|$|l4|`<t`<<th<`<t(|$\r\n",
	"OL,2,C]U1j5j5jwj7}  k5j5j55jwj5 \r\n",
	"OL,3,S#######################################\r\n");
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
	$footer=array(
	"OL,22,C]DTV    520  Lottery   555  Music 530\r\n",
	"OL,23,C]DFilms 540  Newsround 570  Games 550\r\n",
	"OL,24,ANext NewsB FilmIndexCTV IndexFEntsIndex\r\n",
	"FL,".($mpp+1).",540,520,500,F,199\r\n");
	return array_merge($inserter,$pheader,$iheader,$nheader,$title[1],$intro[1],$para,$footer);	// Merge them all in an array to export as page
}

function entertaimentIndex($pages)
{
	$OL=4;
	$mpp=502;
	$inserter=pageInserter("Entertainment Index");	// Get all the headers 
	$pheader=pageHeader(501);
	$iheader=intHeader();
	$header=array(
	"OL,1,C]U|\$xl0l<h<h<t(|$|l4|`<t`<<th<`<t(|$\r\n",
	"OL,2,C]U1j5j5jwj7}  k5j5j55jwj5 \r\n",
	"OL,3,S#######################################\r\n",
	"OL,6,E```````````````````````````````````````\r\n");
	$footer=array(
	"OL,22,C]DTV    520  Lottery   555  Music 530\r\n",
	"OL,23,C]DFilms 540  Newsround 570  Games 550\r\n",
	"OL,24,ANext NewsBMusicIndeCNewsroundFEntsIndex\r\n",
	"FL,502,530,570,500,8ff,199\r\n");
	$page=array_merge($inserter,$pheader,$iheader,$header);
	foreach($pages as $story)
	{
		if($OL==4)
			$colour="M";
		else
			$colour="G";
		if($OL==11||$OL==16)
			$OL++;
		if($OL==5)
			$OL+=2;
		$title=substr($story[0],0,35);
		$title=str_pad($title,35);
		$page=array_merge($page,array("OL,$OL,$colour$titleC$mpp\r\n"));
		$OL++;
		$mpp++;
	}
	return array_merge($page,$footer);
}