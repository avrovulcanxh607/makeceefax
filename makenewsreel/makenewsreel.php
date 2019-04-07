<?php 
/*
	makenewsreel.php
	Generates the Ceefax Newsreel P152
	Nathan Dane, 2018
*/

require "newsreelconfig.php";

echo "Loaded MAKENEWSREEL.PHP V1.1 (c) Nathan Dane, 2019\r\n";

function makenewsreel()
{
	$pages=file("makenewsreel/pages.txt");
	$out=pageInserter("Newsreel",30);
	$iheader=intHeader();
	$ss=1;
	$smax=count($pages);
	$smax++;
	foreach ($pages as $item)
	{
		$subpage=false;
		$item=trim($item);
		$currentpage=file(PAGEDIR."/$item.tti");
		$sc=0;
		$nld=false;
		foreach($currentpage as $line)
		{
			$subnumber=str_pad(($ss-1)."/$smax",39," ",STR_PAD_LEFT);
			if(substr($line,0,2)=="SC")
			{
				$sc=substr($line,3);
				if(spnumbers==24)
				{
					$out=array_merge($out,pageHeader(newsreelmpp,str_pad($ss,4,'0',STR_PAD_LEFT),"c000"),$iheader,array(
					"OL,24,$subnumber \r\n",
					"FL,154,160,390,100,0,199\r\n"));
				}
				else
				{
					$out=array_merge($out,pageHeader(newsreelmpp,str_pad($ss,4,'0',STR_PAD_LEFT),"c000"),$iheader,array(
					"OL,24,ANext News BLocalNewsCRegionalFMain Menu\r\n",
					"FL,154,160,390,100,0,199\r\n"));
				}
				$ss++;
			}
			elseif(substr($line,0,2)=="OL")
			{
				$OL=substr($line,3);
				$OL=substr($OL, 0, strpos($OL, ","));
				if($OL>0 && $OL<24 && spnumbers===false)
					$out=array_merge($out,array($line));
				elseif($OL>0 && $OL<24 && spnumbers!=$OL)
					$out=array_merge($out,array($line));
				elseif($OL>0 && $OL<24 && spnumbers==$OL)
				{
					$out=array_merge($out,array("OL,$OL,$subnumber \r\n"));
					$nld=true;
				}
			}
		}
		if(spnumbers!=false && !$nld)
		{
			$out=array_merge($out,array("OL,".spnumbers.",$subnumber \r\n"));
			$nld=true;
		}
	}
	file_put_contents(PAGEDIR.'/'.PREFIX.newsreelmpp.".tti",$out);
}