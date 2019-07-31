<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from https://www.bbc.co.uk/news
	makenews.php is part of makeceefax.php
	Nathan Dane, 2019
*/
require "simplenews.php";	// You should have got simplenews.php with this module
require "newsheader.php";
require "newsconfig.php";	// No point in 'including' this stuff, the script won't run without it anyway

echo "Loaded MAKENEWS.PHP V0.1 (c) Nathan Dane, 2019\r\n";

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
		$inserter=pageInserter("Regional Headlines ".regionalp);
		$pheader=pageHeader(regionalp);
		$iheader=intHeader();	// Internal header. Might want to remove this
		$nheader=newsHeader(REGION);
		$footer=newsHeadlinesfooter($region);
		$ref=161;	// First news page
		$i=0;	// Begin at 0 for 9 headlines
		
		$headlinesfile=file("makenews/headlines.txt");
		$keys = array_keys($pages);
		$headlinesfile[1]=REGION." News	".$pages[$keys[0]][0]."	161\r\n";
		$fp = fopen("makenews/headlines.txt", "w+") or die("Couldn't create new file");
		fwrite($fp, implode($headlinesfile,''));
		fclose($fp);
	}
	else
	{
		$inserter=pageInserter("News Headlines ".headlinesp);
		$pheader=pageHeader(headlinesp);
		$iheader=intHeader();
		$nheader=newsHeader('headlines');
		$footer=newsHeadlinesfooter($region);
		$ref=(firstnews-1);	// First page -1
		$i=1;	// Begin at 1 for 8 headlines
		
		$headlinesfile=file("makenews/headlines.txt");
		$keys = array_keys($pages);
		$headlinesfile[0]="News	".$pages[$keys[0]][0]."	$keys[0]\r\n";
		$fp = fopen("makenews/headlines.txt", "w+") or die("Couldn't create new file");
		fwrite($fp, implode($headlinesfile,''));
		fclose($fp);
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
	$page=pageInserter("News Index ".indexp, 15);
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
		$mpp=(firstnews+$i);
		if ($i <= 11)
			$toptitles[]="$textcol$headline$mpp";	// On all subpages
		else
			$sstitles[]="$textcol$headline$mpp";	// 3 per subpage
		$i++;
	}
	for($i=0;$i<3;$i++)
	{
		$OL=4;
		$pheader=pageHeader(indexp,"000".($i+1));
		$iheader=intHeader();	// Again, internal header that you might want to remove
		$nheader=newsHeader('index');
		$lines=array();
		$footer=newsIndexFooter();
		if(!ROWADAPT || $i<1)
		{
			foreach ($toptitles as $title)
			{
				if ($OL == 10) $OL++;	// Add the blank line 10 (Or rather, don't)
				$lines[]="OL,$OL,$title\r\n";	// Actual line output.
				$OL++;
			}
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
		$fastext=array("FL,103,104,160,100,F,199\r\n");
		if(ROWADAPT && $i>0)
			$page=array_merge($page,$pheader,$iheader,$lines,$fastext);	// Append the subpage to the last one
		else
			$page=array_merge($page,$pheader,$iheader,$nheader,$lines,$footer,$fastext);
	}
	return $page;	// Return the full file to be saved
}

function newsSummary($pages)	// Summary P103
{
	$OL=5;
	$i=0;
	$inserter=pageInserter("News Summary ".summaryp, 15);
	$pheader=pageHeader(summaryp,'0001','c000');
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
		$seepage=outputLine($OL,' ',"See ".($i+firstnews),22);
		$OL+=$seepage[0];
		if($outputline[1]) $page=array_merge($page,$outputline[1],$seepage[1]);	// Only output the line if it will fit
		$OL++;			// Extra critical beacuse if it tries to output a line that doesn't fit, we loose the whole array
		$i++;
		if($i > 6) break;
		if($i == 3)	// New subpage
		{
			$OL=5;
			$pheader=pageHeader(summaryp,'0002','c000');
			$top2[]="OL,4,                                   2/2\r\n";	// Top Line
			$page=array_merge($page,$footer,$pheader,$iheader,$nheader,$top2);	// Add the next subpage
		}
	}
	$page=array_merge($page,$footer);
	return $page;
}

function newsLatest($pages)
{
	$i=0;
	$inserter=pageInserter("Latest ".latestp,15);
	$out=array_merge($inserter);
	foreach ($pages as $page)
	{
		$headline='';
		$lines='';
		$pheader=pageHeader(latestp,"000".($i+1),8001);
		$iheader=intHeader();	// Internal Header
		$longtitle=substr($page[1],0,strpos($page[1],'- '));
		$headline=wordwrap($longtitle,36,"\r\n");
		$headline = explode("\r\n",$headline);
		if (count($headline) > 2)
		{
			$headline=wordwrap($page[0],36,"\r\n");
			$headline = explode("\r\n",$headline);
		}
		$lines[]="OL,20,KKD]GLATEST \D``````````````````````````\r\n";
		$lines[]="OL,21,KKF$headline[0]\r\n";
		$lines[]="OL,22,KKF$headline[1]\r\n";
		$lines[]="OL,23,KKD````````````````````````````` ]G".($i+1)."/9\r\n";
		$lines[]="OL,24,KKATickerB Latest CHeadlinesFMain Menu\r\n";
		$lines[]="FL,151,".(104+$i).",101,100,100,100\r\n";
		$out=array_merge($out,$pheader,$iheader,$lines);
		$i++;
		if ($i>8) break;
	}
	return $out;
}

function newsTicker($pages)
{
	$i=0;
	$inserter=pageInserter("Ticker ".tickerp,8);
	$out=array_merge($inserter);
	foreach ($pages as $page)
	{
		$headline='';
		$lines='';
		$pheader=pageHeader(tickerp,"000".($i+1),8001);
		$iheader=intHeader();	// Internal Header
		$longtitle=substr($page[1],0,strpos($page[1],'- '));
		$headline=wordwrap($longtitle,36,"\r\n");
		$headline = explode("\r\n",$headline);
		if (count($headline) > 1)
		{
			$headline=wordwrap($page[0],36,"\r\n");
			$headline = explode("\r\n",$headline);
		}
		$lines[]="OL,21,KKD]GBBC Ceefax ".(104+$i)." \D````````````````\r\n";
		$lines[]="OL,22,KKC$headline[0]\r\n";
		$lines[]="OL,23,KKD`````````````````` ]GHeadlines 101\ \r\n";
		$lines[]="OL,24,KKANewsreel BN.IreTV CExtra FMain Menu\r\n";
		$lines[]="FL,152,600,140,100,100,100\r\n";
		$out=array_merge($out,$pheader,$iheader,$lines);
		$i++;
		if ($i>8) break;
	}
	return $out;
}

function sciTech($pages)
{
	$i=0;
	$inserter=pageInserter("Sci-Tech ".scitechp, 15);
	$outp=array_merge($inserter);
	$length=count($pages);
	foreach ($pages as $page)
	{
		$line=4;
		$para=array();
		$pheader=pageHeader(scitechp,'000'.($i+1),'c000');
		$iheader=intHeader();	// Internal Header
		$nheader=newsHeader("scitechhead");
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
			$out=outputLine($ln,"F",$element,21);
			if ($out[1] !== false)
			{
				foreach($out[1] as $line)
				{
					array_push($para,$line);
				}
			}
			$ln+=$out[0];
		}
		$footer=array("OL,21,                                    ".($i+1)."/$length \r\n",
		"OL,22,D]CHeadlines G101CIndexG102CSport  G300 \r\n",
		"OL,23,D]CFront PageG100CTV   G600CWeatherG400 \r\n",
		"OL,24,ALocalNewsBHeadlinesCNews IndxFMain Menu\r\n",
		"FL,160,101,102,100,8ff,100\r\n");
		$outp=array_merge($outp,$pheader,$iheader,$nheader,$title[1],$intro[1],$para,$footer);	// Merge them all in an array to export as page
		$i++;
		if ($i > 5) break;
	}
	return $outp;
}

function makenews()
{
	$stories=array();
	$rstories=array();
	
	$count=firstnews;
	$ukFeed = file_get_contents("http://feeds.bbci.co.uk/news/uk/rss.xml");	// BBC UK rss
	$worldFeed = file_get_contents("http://feeds.bbci.co.uk/news/world/rss.xml");	// BBC World rss
	$uktime = file_get_contents("makenews/ukrss.txt");
	$worldtime = file_get_contents("makenews/worldrss.txt");
	$ukxml = new SimpleXmlElement($ukFeed);
	$worldxml = new SimpleXmlElement($worldFeed);
	if ($uktime == $ukxml->channel->lastBuildDate && $worldtime == $worldxml->channel->lastBuildDate)	// Check if either rss feed needs updating
		$runnews=false;
	else
		$runnews=true;
	if (!$runnews || !donews) echo "UK/World News Up-to-date\r\n";	// If nothing's changed, don't even bother
	else	// Unfortunately, if somthing has, we have to read in all the pages. Not Efficiant!
	{
	echo "Generating News Stories...\r\n";
	file_put_contents("makenews/ukrss.txt",$ukxml->channel->lastBuildDate);
	file_put_contents("makenews/worldrss.txt",$worldxml->channel->lastBuildDate);
	foreach($ukxml->channel->item as $chan) {
		if (strncmp($chan->link,"https://www.bbc.co.uk/news/in-pictures",38)	// Pictures don't work on teletext!
		&& strncmp($chan->link,"https://www.bbc.co.uk/sport/",28) // Sport belongs elsewhere
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/av/",30)	// We don't want pictures or videos 
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/blogs",32)	// More 'In depth' or 'Entertainment' than news
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/newsbeat",35)	// We're basically removing anything that won't
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/stories",34)	// work or fit on a Ceefax page.
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/newsround",34)
		&& strncmp($chan->title,"In pictures:",12))	// Although there's always a few that slip through the net.
		{
			$url=$chan->link;
			echo $url."\r\n";
			$name="news".$count;
			$$name=getNews($url,4);
			if ($$name===false) continue 1;	// Don't even try to run a failed page
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));	// Make the ordinary pages while downloading
			$stories[$count]=$$name;
			$count+=2;
			if ($count>lastnews) break;	// Stop after we get the pages that we want
		}
	}
	$count=(firstnews+1);
	foreach($worldxml->channel->item as $chan) {
		if (strncmp($chan->link,"https://www.bbc.co.uk/news/in-pictures",38)	// Pictures don't work on teletext!
		&& strncmp($chan->link,"https://www.bbc.co.uk/sport/",28) // Sport belongs elsewhere
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/av/",30)	// We don't want pictures or videos 
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/blogs",32)	// More 'In depth' or 'Entertainment' than news
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/newsbeat",35)	// We're basically removing anything that won't
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/stories",34)	// work or fit on a Ceefax page.
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/newsround",34)
		&& strncmp($chan->title,"In pictures:",12))	// Although there's always a few that slip through the net.
		{
			$url=$chan->link;
			echo $url."\r\n";
			$name="news".$count;
			$$name=getNews($url,4);
			if ($$name===false) 
			{
				echo "simplenews.php detected a problem with this page\r\n";
				continue 1;	// Don't even try to run a failed page
			}
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));	// Make the ordinary pages while downloading
			$stories[$count]=$$name;
			$count+=2;
			if ($count>lastnews) break;	// Stop after we get the pages that we want
		}
	}
	ksort($stories);
	echo "Generating News Stories...Done\r\nGenerating News Indexes and others...";
	file_put_contents(PAGEDIR.'/'.PREFIX.headlinesp.".tti",(newsHeadlines($stories)));	// Make the Headlines page 101
	file_put_contents(PAGEDIR.'/'.PREFIX.indexp.".tti",(newsIndex($stories)));	// Make the UK/World index page
	file_put_contents(PAGEDIR.'/'.PREFIX.summaryp.".tti",(newsSummary($stories)));	// Make the Summary page
	file_put_contents(PAGEDIR.'/'.PREFIX.latestp.".tti",(newsLatest($stories)));	// Make the Latest page
	file_put_contents(PAGEDIR.'/'.PREFIX.tickerp.".tti",(newsTicker($stories)));	// Make the Ticker page
	echo "Done\r\n";
	}
	
	$count=firstreg;
	$region=strtolower(REGION);
	$region=str_replace(' ','_',$region);
	$rssfeed="http://feeds.bbci.co.uk/news/$region/rss.xml";	// BBC regional stories
	$time = file_get_contents("makenews/rrss.txt");
	$rawFeed = file_get_contents($rssfeed);
	$xml = new SimpleXmlElement($rawFeed);
	if ($time == $xml->channel->lastBuildDate || !doregnews) echo REGION." News Up-to-date\r\n";
	else
	{
	echo "Generating Local News Stories...\r\n";
	file_put_contents("makenews/rrss.txt",$xml->channel->lastBuildDate);
	foreach($xml->channel->item as $chan) {
		// Don't want video/sport stories. They don't render too well on teletext
		if (strcasecmp($chan->title,"in pictures") && strncmp($chan->link,"https://www.bbc.co.uk/sport/",28) 
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/av/",30))
		{
			$url=$chan->link;
			echo $url."\r\n";
			$name="reg".$count;
			$$name=getNews($url,4);
			if ($$name===false) 
			{
				echo "simplenews.php detected a problem with this page\r\n";
				continue 1;	// Don't even try to run a failed page
			}
			file_put_contents(PAGEDIR.'/'.PREFIX."$count.tti",(newsPage($$name,$count)));
			$rstories[]=$$name;
			$count++;
			if ($count>lastreg) break;	// Stop after we get the pages that we want
		}
	}
	file_put_contents(PAGEDIR.'/'.PREFIX.regionalp.".tti",(newsHeadlines($rstories,true)));	// Make the regional front page
	echo "Generating Local News Stories...Done\r\n";
	}
	
	$count=0;
	$rssfeed="http://feeds.bbci.co.uk/news/technology/rss.xml";	// BBC Science-Technology stories
	$time = file_get_contents("makenews/scitechrss.txt");
	$rawFeed = file_get_contents($rssfeed);
	$xml = new SimpleXmlElement($rawFeed);
	if ($time == $xml->channel->lastBuildDate) echo "Sci-Tech News Up-to-date\r\n";
	else
	{
	echo "Generating Science/Technology Stories...\r\n";
	$scistories=array();
	file_put_contents("makenews/scitechrss.txt",$xml->channel->lastBuildDate);
	foreach($xml->channel->item as $chan) {
		// Don't want video/sport stories. They don't render too well on teletext
		if (strcasecmp($chan->title,"in pictures") && strncmp($chan->link,"https://www.bbc.co.uk/sport/",28) 
		&& strncmp($chan->link,"https://www.bbc.co.uk/news/av/",30))
		{
			$url=$chan->link;
			echo $url."\r\n";
			$name="tech".$count;
			$$name=getNews($url,4);
			if ($$name===false) 
			{
				echo "simplenews.php detected a problem with this page\r\n";
				continue 1;	// Don't even try to run a failed page
			}
			$scistories[]=$$name;
			$count++;
			if ($count>5) break;	// Stop after we get the pages that we want
		}
	}
	file_put_contents(PAGEDIR.'/'.PREFIX.scitechp.".tti",(sciTech($scistories)));	// Make the Sci-Tech page
	echo "Generating Science/Technology Stories...Done\r\n";
	}
}