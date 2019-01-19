<?php
/*
	common.php is part of makeceefax
	Functions which are used by several modules
	Nathan Dane, 2018
*/

/*
	array pageInserter(str $Description, mixed $Time, str $CycleorTimed)
	Returns an array containing the opening lines of a TTI teletext page.
*/
function pageInserter($de="News Page", $t=10, $ct="T")
{
	return array("DS,inserter\r\n","SP,".PAGEDIR."/\r\n","DE,$de\r\n","CT,$t,$ct\r\n");
}

/*
	array pageHeader(mixed $pageno, mixed $subcode, mixed $options)
	Returns an array with all the lines need for subpages.
*/
function pageHeader($mpp=800,$ssss='0000',$ps=8000)
{
	$ss=substr($ssss,2,2);
	return array("PN,$mpp$ss\r\n","SC,$ssss\r\n","PS,$ps\r\n");
}

/*
	str outputline($OL,$Colour,$content,$maxline,$gap,)
	Nicked this from Peter Kwan (@peterkvt80), hope he doesn't mind
*/
function outputLine($lineNumber,$colour,$utext,$maxline)
{
	$out=array();
	$utext=fix_text($utext);
	$utext=explode('\r\n',wordwrap($utext,39,'\r\n'));		// Wrap the text into separate lines
	if (count($utext)+$lineNumber>$maxline)					// This would overflow so forget it
	{	
		return array (0,false);
	}
	$count=0;
	foreach ($utext as $key=>&$value) {
		if (strlen($value) < 2) {
			unset($utext[$key]);
		}
	}
	foreach ($utext as $line)							// Output all the lines
	{
		$ln=$lineNumber+$count;
		$out[] = "OL,".$ln.",$colour$line\r\n";
		$count++;
	}
	return array ($count,$out); 	// return the number lines used
}

/*
	array intHeader(str i)
	If you want an IP address instead of a hostname, set arg1 to 'i'
*/
function intHeader($i='')
{
	$dd = date('d');
	$mm = date('m');
	$hh = date('H');
	$nn = date('i');
	$ss = date('s');
	exec("hostname $i",$host);
	$host=substr($host[0],0,16);
	$host=trim($host);
	$host=str_pad($host,16);
	return array ("OL,0,XXXXXXXXB$hostE$dd/$mmC$hh:$nn:$ss\r\n");
}

/*
	str myTruncate2(str STRING, int LIMIT, str BREAK, str PAD)
	Original PHP code by Chirp Internet: www.chirp.com.au
	Please acknowledge use of this code by including this header.
	Truncates text to the nearest word
*/
function myTruncate2($string, $limit, $break=" ", $pad="")
{
	if(strlen($string) <= $limit) return $string;
	$string = substr($string, 0, $limit);
	if(false !== ($breakpoint = strrpos($string, $break))) {
		$string = substr($string, 0, $breakpoint);
	}
	return $string . $pad;
}