<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	makenews.php is part of makeceefax.php
	Nathan Dane, 2018
*/
require "simplenews.php";	// You should have got simplenews.php with this module
require "newsheader.php";

echo "Loaded MAKENEWS.PHP V(indev) (c) Nathan Dane, 2018\r\n";

function newsPage($page,$mpp)	// Makes all the actual stories, P104-124 & 161-169
{
	$line=4;
	$found=false;
	$para=array();
	$inserter=pageInserter("News Page $mpp $page[4]");	// Get all the headers 
	$pheader=pageHeader($mpp);
	$iheader=intHeader();
	$nheader=newsHeader($page[4]);
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
	$footer=newsFooter($nheader[1],$mpp);	// Generate footer
	return array_merge($inserter,$pheader,$iheader,$nheader[0],$title[1],$intro[1],$para,$footer);	// Merge them all in an array to export as page
}

function newsHeadlines($pages,$region=false)	// Headlines P101 and Regional P160
{
	if ($region)
	{
		$inserter=pageInserter("Regional Headlines 160");
		$pheader=pageHeader('160');
		$iheader=intHeader();	// Internal header. Might want to remove this
		$nheader=newsHeader(REGION);
		$footer=newsHeadlinesfooter($region);
		$ref=161;	// First news page
		$i=0;	// Begin at 0 for 9 headlines
	}
	else
	{
		$inserter=pageInserter("News Headlines 101");
		$pheader=pageHeader('101');
		$iheader=intHeader();
		$nheader=newsHeader('headlines');
		$footer=newsHeadlinesfooter($region);
		$ref=103;	// First page -1
		$i=1;	// Begin at 1 for 8 headlines
	}
	$lines=array();
	$OL=4;
	foreach ($pages as $page)
	{
		$headline=$page[0];
		if ($OL==4) $textcol="M";
		$headline=myTruncate2($headline, 70, " ");	// Cut headline to 70 chars at nearest word
		$headline=wordwrap($headline,35,"\r\n");	// Wrap it for 2 lines. Not original, but required for longer headlines
		$headline=explode("\r\n",$headline);	// Convert it back to a string
		$headline[0]=substr(str_pad($headline[0],35),0,35);
		$headline[0].='C';	// Yellow
		array_push($lines,"OL,$OL,$textcol$headline[0]".($ref+$i)."\r\n");	// Output first line
		if ($OL<5)
			$OL+=2;
		else
			$OL+=1;
		$textcol='G';	// white
		if(isset($headline[1]))
		{
		if (strlen($headline[1])<36)
		{
			$headline[1]=substr(str_pad($headline[1],39),0,39);
		}
		if ($OL==22)
			break;
		array_push($lines,"OL,$OL,$textcol$headline[1]"."\r\n");	// Output second line
		}
		if ($OL<7 && !$region)	// We don't want extra spaces if this is the regional page!
			$OL+=2;
		else
			$OL+=1;
		$i++;
		if ($i==9)
			break;
	}
	return array_merge($inserter,$pheader,$iheader,$nheader[0],$lines,$footer);
}

function newsIndex($pages)	// UK/World Index P102
{
	$page=pageInserter("News Index 102", 15);
	$toptitles=array();
	$sstitles=array();	// Declaring these for later. Not sure if we need this
	$i=0;
	foreach($pages as $head)
	{
		if ($i<3) 	// Only the first 3 headlines are cyan, then they're white
			$textcol='F';	// Cyan
		else
			$textcol='G';	// White. Could have just done a space
		$headline=myTruncate2($head[0], 35, " ");	// Cut the headline to 35 chars, but at word breaks
		$headline=substr(str_pad($headline,35),0,35);
		$headline.='C';	// Yellow
		$mpp=(104+$i);
		if ($i <= 11)
			$toptitles[]="$textcol$headline$mpp";	// On all subpages
		else
			$sstitles[]="$textcol$headline$mpp";	// 3 per subpage
		$i++;
	}
	for($i=0;$i<3;$i++)
	{
		$OL=4;
		$pheader=pageHeader('102',"000$i");
		$iheader=intHeader();	// Again, internal header that you might want to remove
		$nheader=newsHeader('index');
		$lines=array();
		$footer=newsIndexFooter();
		foreach ($toptitles as $title)
		{
			if ($OL == 10) $OL++;	// Add the blank line 10 (Or rather, don't)
			$lines[]="OL,$OL,$title\r\n";	// Actual line output.
			$OL++;
		}
		$lines[]="OL,17,C Other news ".($i+1)."/3 \r\n";	// Subpages!
		$OL=18;	// Shift down to line 18
		for($a=0;$a<3;$a++)
		{
			$lines[]="OL,$OL,$sstitles[$a]\r\n"; 	// Output the line
			$OL++;
		}
		unset($sstitles[0]);
		unset($sstitles[1]);	// Remove the last three that we did.
		unset($sstitles[2]);	// Probably better way to do this.
		$sstitles = array_values($sstitles);
		$page=array_merge($page,$pheader,$iheader,$nheader,$lines,$footer);	// Append the subpage to the last one
	}
	return $page;	// Return the full file to be saved
}

function newsSummary($pages)	// Summary P103
{
	$OL=5;
	$i=0;
	$inserter=pageInserter("News Summary 103", 15);
	$pheader=pageHeader('103','0001');
	$iheader=intHeader();	// Internal Header
	$nheader=newsHeader('summary');
	$footer=newsSummaryFooter();
	$top[]="OL,4,                                   1/2\r\n";	// Top Line
	$page=array_merge($inserter,$pheader,$iheader,$nheader,$top);	// Merge everything into the page
	foreach($pages as $head)
	{
		$textcol='F';	// Cyan
		$outputline=outputLine($OL,$textcol,$head[5],21);	// Returns False if the text won't fit
		$OL+=$outputline[0];
		$seepage=outputLine($OL,' ',"See ".($i+104),22);
		$OL+=$seepage[0];
		if($outputline[1]) $page=array_merge($page,$outputline[1],$seepage[1]);	// Only output the line if it will fit
		$OL++;			// Extra critical beacuse if it tries to output a line that doesn't fit, we loose the whole array
		$i++;
		if($i > 6) break;
		if($i == 3)	// New subpage
		{
			$OL=5;
			$pheader=pageHeader('103','0002');
			$top2[]="OL,4,                                   2/2\r\n";	// Top Line
			$page=array_merge($page,$footer,$pheader,$iheader,$nheader,$top2);	// Add the next subpage
		}
	}
	$page=array_merge($page,$footer);
	return $page;
}

function makenews()
{
	$stories=array();
	$rstories=array();
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
			$str = file_get_html($url);	// We should really get rid of all this from here ->
			if (!$str) continue 1;
			$title=$str->find("link[rel=canonical]");
			$title=substr ($title[0],35);
			$title=substr($title, 0, strpos( $title, '"'));
			echo $title."\n";
			if (!strncmp($title,"/www.bbc.co.uk/news/av/",21))
			{
				continue 1;
			}
			echo $chan->title."\n";	// <-- To here. It's really inefficiant and kinda pointless.
			$name="news".$count;
			$$name=getNews($url,4);	// REEEALLY inefficiant. We're effectively downloading the page twice
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));	// Make the ordinary pages while downloading
			$stories[]=$$name;
			$count++;
			if ($count>112) break;	// Stop after we get the pages that we want
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
	/*
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
			$rstories[]=$$name;
			$count++;
			if ($count>169) break;	// Stop after we get the pages that we want
		}
	}*/
	// Need to make a config page of some sort so you can remove pages you don't want...
	// And so you can change what page they're on
	
	// This is where new makenews has the advantage. These pages are all generated pretty much instantly
	file_put_contents(PAGEDIR.'/'.PREFIX."101.tti",(newsHeadlines($stories)));	// Make the Headlines page 101
	//file_put_contents(PAGEDIR.'/'.PREFIX."160.tti",(newsHeadlines($rstories,true)));	// Make the regional front page
	file_put_contents(PAGEDIR.'/'.PREFIX."102.tti",(newsIndex($stories)));	// Make the UK/World index page
	file_put_contents(PAGEDIR.'/'.PREFIX."103.tti",(newsSummary($stories)));	// Make the Summary page
}