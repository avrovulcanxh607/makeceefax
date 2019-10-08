<?php
/*
	makefrontpage.php
	Ceefax P100
	Nathan Dane, 2018
*/

echo "Loaded MAKEFRONTPAGE.PHP V1.1 (c) Nathan Dane, 2019\r\n";

function makefrontpage()
{
$modules=file("makefrontpage/fronthead.txt");
$out=pageInserter("Service Front Page",8);
$restof=array(
"OL,1,W`ppp`ppp`pppT||,,,<,,<,,<,,|,,,|,l<,|||\r\n",
"OL,2,Wj \$zj \$zj tzT]S7#jsjsjshs4ouz?T \r\n",
"OL,3,Wj %jj %jj 'kT]Supjpjpj j 55jT \r\n",
"OL,4,W\"###\"###\"###T##########################\r\n",
"OL,8,D```````````````````````````````````````\r\n",
"OL,9,A]GFormula One now on Ceefax: See P360\r\n",
"OL,10,CBBC INFO      G695CNEWS FOR REGION G160\r\n",
"OL,11,CCHESS         G568CNEWSROUND       G570\r\n",
"OL,12,CCOMMUNITYGBBC2G650CRADIO      GBBC1G640\r\n",
"OL,13,CENTERTAINMENT G500CREAD HEAR  GBBC2G640\r\n",
"OL,14,C                                       \r\n",
"OL,15,CFILM REVIEWS  G526CSPORT           G300\r\n",
"OL,16,CFINANCEG  BBC2G200CSUBTITLING      G888\r\n",
"OL,17,CFLIGHTS       G440CTOP 40          G528\r\n",
"OL,18,CGAMES REVIEWS G527CTRAVEL          G430\r\n",
"OL,19,CHORSERACING   G660CTV LINKS        G615\r\n",
"OL,20,CLOTTERY       G555CTV LISTINGS     G600\r\n",
"OL,21,CSCI-TECH      G154CWEATHER         G400\r\n",
"OL,22,C\r\n",
"OL,23,D]CCeefax: The world at your fingertips \r\n",
"OL,24,AHeadlines  BSport CN.Ire TV FA-Z Index \r\n");
$fastext=array("FL,101,300,600,199,8FF,199\r\n");
$i=1;
foreach ($modules as $key=>$module)
{
	$module=trim($module);
	if(file_exists("make$module/headlines.txt"))	// Make sure the file exists before trying to load it
	{
		$headlines=file("make$module/headlines.txt");
		foreach ($headlines as $page)
		{
			$pheader=pageHeader(100,str_pad($i,4,'0',STR_PAD_LEFT));
			$iheader=intHeader();
			$title=str_getcsv($page,"	");
			
			$title[1]=strtoupper($title[1]);
			$title[1]=myTruncate2($title[1], 35, " ");
			$title[1]=str_pad($title[1],35);
			
			$headline=array("OL,5,C$title[0]\r\n","OL,6,M$title[1]C$title[2]\r\n");
			$i++;
			//if(ROWADAPT && $i>2)	// If Row adaptive mode is enabled, only send the full page once. 
				//$out=array_merge($out,$pheader,$iheader,$headline,$fastext);	// has the distinct disadvantage of looking like its broken
				//else
			$out=array_merge($out,$pheader,$iheader,$headline,$restof,$fastext);	// Append the subpage to the last one
		}
	}
}
file_put_contents(PAGEDIR.'/'.PREFIX.'100'.".tti",$out);
}