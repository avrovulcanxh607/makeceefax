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
$test=newsPageDecode(file_get_html("https://www.bbc.co.uk/news/business-45981436"));
$out=newsPage($test,105);
file_put_contents("test.tti",$out);