<?php
/*
	makenews.php 
	Creates Ceefax Magazine 1 from http://bbc.co.uk/news
	Nathan Dane, 2018
*/

function newsPage($page,$mpp)
{
	$inserter=pageInserter("News Page $mpp");
	$pheader=pageHeader($mpp);
	$nheader=newsHeader($page[0]);	// Need to add other stuff for this
	$title=outputLine(4,"C",$page[1],21);
	$intro=outputLine($title[0],"C",$page[2],21);
	$body=outputLine($intro[0],"C",$page[3],21);
	$footer=array();	// Nothing yet!
	return array_merge($inserter,$pheader,$nheader,$title[1],$body[1],$footer);
}
/*
	array newsPageDecode(DOM $html)
	Decodes title & content of BBC News Pages
*/
function newsPageDecode($html)
{
	$sect =$html->find('meta[property="article:section"]');
	$title=$html->find("meta[property=og:title]");
	$intro=$body->find('p[class=story-body__introduction]');
	$body =$html->find('div[class=story-body]');
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
		echo 'OL,1,—j#3kj#3kj#3k”“ | |h<$|,|h4h||4| | '."\r\n";
		echo 'OL,2,—j $kj $kj'." 'k”“ #jw1#ju0j5 #  "."\r\n";
		echo 'OL,3,—"###"###"###”///,/,-,.,/,-,.-./,/,/////'."\r\n";
		break;
	case "technology" : ;
		echo 'OL,1,Wj#3kj#3kj#3kT]S |,h<$|h<$|0|h<$|,      '."\r\n";
		echo 'OL,2,Wj $kj $kj \'kT]S sju0jw1)ju0s      '."\r\n";
		echo 'OL,3,W"###"###"###T///,,-,.,-,.,/,-,.,,//////'."\r\n";
		break;
	case "home" : ;
		echo 'OL,1,—j#3kj#3kj#3k”“    h4h4|,|h<<|h<$'."\r\n";
		echo 'OL,2,—j $kj $kj \'k”“    j7k5pj55jw1'."\r\n";
		echo 'OL,3,—"###"###"###”//////-.-.,,,-..,-,.//////'."\r\n";
		break;
	case "scotland" : ;
		echo 'OL,1,Wj#3kj#3kj#3kD]S`<$|,h<|(|$| `<l0|th4|l0'."\r\n";
		echo 'OL,2,Wj $kj $kj \'kT]Sb{%pju  pj7k5"o5x%'."\r\n";
		echo 'OL,3,W"###"###"###T//-,/,,-,,/,/,,-.-.,/-.,,/'."\r\n";
		break;
	case "northern ireland" : ;
	    echo 'OL,1,—j#3kj#3kj#3k”“|0| h4|l4|,h4`<thth4|l0'."\r\n";
		echo 'OL,2,—j $kj $kj'." 'k”“+`j5k4sjuj7j7o5z%"."\r\n";
		echo 'OL,3,—"###"###"###”//,/,--.,-.,,-,-.,-.-.,,//'."\r\n";
		break;
	case "wales" : ;
		echo 'OL,1,Wj#3kj#3kj#3kD]S   h44|`<l0| h<$x,'."\r\n";
		echo 'OL,2,Wj $kj $kj \'kT]S   *uu?j7k5pjw1s?'."\r\n";
		echo 'OL,3,W"###"###"###T//////,,.-.-.,,-,.,.//////'."\r\n";
		break;
	case "london" : ;
		echo 'OL,1,Wj#3kj#3kj#3kD]S | h<|h|0|h<th<|h|0|'."\r\n";
		echo 'OL,2,Wj $kj $kj \'kT]S pjuj5+ju>juj5+'."\r\n";
		echo 'OL,3,W"###"###"###T///,,-,,-./,-,.-,,-./,////'."\r\n";
		break;
	case "World" : ;
		echo 'OL,1,—j#3kj#3kj#3k”“   |hh4|,|h<l4| h<l0'."\r\n";
		echo 'OL,2,—j $kj $kj \'k”“   ozz%pj7k4pjuz%'."\r\n";
		echo 'OL,3,—"###"###"###”/////-,,/,,,-.-.,,-,,/////'."\r\n";
		break;
	case "Politics" : ;
		echo 'OL,1,—j#3kj#3kj#3k”“ h<|h<|h4 |(|$|h<$|,$ '."\r\n";
		echo 'OL,2,—j $kj $kj \'k”“ j7#juju0  ju0s{5 '."\r\n";
		echo "OL,3,—\"###\"###\"###”///-./-,,-,.,/,/,-,.,,.///\r\n";
		break;
	default;
		echo "OL,1,Wj#3kj#3kj#3kT]S     xl0|,h44|h,$\r\n";
		echo 'OL,2,Wj $kj $kj \'kT]S     j5s*uu?bs5'."\r\n";
		echo 'OL,3,W"###"###"###T///////,-.,,/,,.-,.///////'."\r\n";
		break;
		}
}
