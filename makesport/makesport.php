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

echo "Loaded MAKESPORT.PHP V0.2 (c) Nathan Dane, 2019\r\n";

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
		echo "Generating Football Stories...\r\n";
		file_put_contents("makesport/football.rss",$xml->channel->lastBuildDate);
		foreach($xml->channel->item as $chan) {
			if (strncmp($chan->link,"http://www.bbc.co.uk/sport/av/",30) && 
			!strncmp($chan->link,"http://www.bbc.co.uk/sport/football/",36))
			{
				$url=$chan->link;
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
		echo "Generating Football Stories...Done\r\n";
	}
	if(leaguetable)
	{
		echo "Generating Football League Tables...";
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_1[0].".tti",(footballLeague(league_table_1[0],league_table_1[1])));
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_2[0].".tti",(footballLeague(league_table_2[0],league_table_2[1])));
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_3[0].".tti",(footballLeague(league_table_3[0],league_table_3[1])));
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_4[0].".tti",(footballLeague(league_table_4[0],league_table_4[1])));
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_5[0].".tti",(footballLeague(league_table_5[0],league_table_5[1])));
		file_put_contents(PAGEDIR.'/'.PREFIX.league_table_6[0].".tti",(footballLeague(league_table_6[0],league_table_6[1])));
		echo "Done\r\n";
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

function footballLeague($mpp,$url="https://www.bbc.co.uk/sport/football/premier-league/table")
{
	$OL=8;
	$inserter=pageInserter("Football League Table");	// Get all the headers 
	$pheader=pageHeader($mpp,"0001");	// Hard coded for now
	$iheader=intHeader();
	$nheader=sportHeader("Football");
	$footer=array("OL,22,D]CFootballG302CFront pageG100CTV  G600 \r\n",
	"OL,23,D]CRugby   G370CMotorsportG360CGolfG330 \r\n",
	"OL,24,ANext page  BFootball CHeadlines FSport \r\n",
	"FL,".($mpp+1).",302,301,300,f,320\r\n");
	$data=leagueTable($url);
	$league=$data[0];
	$date=$data[1];
	$table=$data[2];
	$league=strtoupper($league);
	$league=str_replace("TABLE","",$league);
	
	$date=substr($date,strpos($date,"updated"));
	$date=str_replace(" at","",$date);
	$date=str_replace("updated ","",$date);
	$date=date("M d H:i", strtotime($date));
	
	$page=array_merge($inserter,$pheader,$iheader,$nheader,array("OL,4, B$league\r\n","OL,6, G$date    P  W  D  L   F   A Pts\r\n"));
	
	foreach($table as $key=>$row)
	{
		if($key%2==0)
			$colour="G";
		else
			$colour="F";
		$key=str_pad($colour.($key+1),4," ",STR_PAD_LEFT);
		
		$name=$row[0];
		$name=str_replace("Crystal Palace","C Palace",$name);
		$name=str_replace("Huddersfield","H'field",$name);
		$name=str_pad($name,12," ",STR_PAD_RIGHT);
		$name=substr($name,0,12);
		
		$p=str_pad($row[1],2," ",STR_PAD_LEFT);
		$w=str_pad($row[2],2," ",STR_PAD_LEFT);
		$d=str_pad($row[3],2," ",STR_PAD_LEFT);
		$l=str_pad($row[4],2," ",STR_PAD_LEFT);
		$f=str_pad($row[5],2," ",STR_PAD_LEFT);
		$a=str_pad($row[6],2," ",STR_PAD_LEFT);
		$pts=str_pad($row[7],3," ",STR_PAD_LEFT);
		
		$page=array_merge($page,array("OL,$OL,$key $name $p $w $d $l  $f  $a $pts\r\n"));
		$OL++;
		if($OL>20)
		{
			$page=array_merge($page,$footer,$inserter,pageHeader($mpp,"0002"),$iheader,$nheader,array("OL,4, B$league\r\n","OL,6, G$date    P  W  D  L   F   A Pts\r\n"));
			$OL=8;
		}
	}
	
	return array_merge($page,$footer);
}

function leagueTable($url="https://www.bbc.co.uk/sport/football/premier-league/table")
{
	$html=file_get_html($url);
	$leaguetable=array();
	$table=$html->find("table");
	$rows=$table[0]->find("tr");
	array_shift($rows);
	array_pop($rows);
	foreach($rows as $row)
	{
		$data=$row->find("td");
		array_push($leaguetable,array($data[2]->plaintext,$data[3]->plaintext,$data[4]->plaintext,
		$data[5]->plaintext,$data[6]->plaintext,$data[7]->plaintext,$data[8]->plaintext,$data[10]->plaintext));
	}
	$time=$html->find("time",0)->plaintext;
	$league=$html->find("h1",0)->plaintext;
	return array($league,$time,$leaguetable);
}