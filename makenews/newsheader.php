<?php
/*
	newsheader.php
	Headers for news pages. Part of makenews.php
	Nathan Dane, 2018
	
	array newsHeader(str $title)
	Returns an array of lines for the given title
*/
function newsHeader($title="default")
{
	$region="normal";
	switch ($title)
	{
	case "Health" : ;
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S | |h<$|,|h4h||4| | \r\n",
		"OL,2,Wj \$kj \$kj 'kT]S #jw1#ju0j5 #  \r\n",
		"OL,3,W\"###\"###\"###T///,/,-,.,/,-,.-./,/,/////\r\n");
		break;
	case "Technology" : ;
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S  |,h<$|h<$|0|h<$|,      \r\n",
		"OL,2,Wj \$kj \$kj 'kT]S  sju0jw1+ju0s      \r\n",
		"OL,3,W\"###\"###\"###T////,,-,.,-,.,/,-,.,,/////\r\n");
		break;
	case "UK" : ;
	case "Business" : ;
	case "Entertainment & Arts" : ; // Not sure. Maybe skip this?
	case "London" : ;
	case "Family & Education" : ;
	case "Cambridgeshire" : ;
	case "Shropshire" : ;
	case "Leicester" : ; // These need to be regionalized
	case "Norfolk" : ;
	case "Sheffield & South Yorkshire" : ;
	case "Bristol" : ;
	case "Beds, Herts & Bucks" : ;
	case "Essex" : ;
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S    h4h4|,|h<<|h<$\r\n",
		"OL,2,Wj \$kj \$kj 'kT]S    j7k5pj55jw1\r\n",
		"OL,3,W\"###\"###\"###T//////-.-.,,,-..,-,.//////\r\n");
		break;
	case "Scotland" : ;
	case "Edinburgh, Fife & East Scotland" : ;
	case "Tayside and Central Scotland" : ;
	case "NE Scotland, Orkney & Shetland" : ;
	case "Glasgow & West Scotland" : ;
	case "Hearts" : ;
	case "Scotland politics" : ;
		$region="Scotland";
		$return=array(
		"OL,1,Wj#3kj#3kj#3kD]Sx,h<$|l4l<h4 x,th|0|h<t \r\n",
		"OL,2,Wj \$kj \$kj 'kT]Ss?ju0z5j5ju0#j5+ju> \r\n",
		"OL,3,W\"###\"###\"###T//,.-,.,,.-.-,.,/,-./,-,./\r\n");
		break;
	case "Northern Ireland" : ;
	case "Foyle & West" : ;
	case "N. Ireland Politics" : ;
		$region="Northern Ireland";
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S|0| h4|l4|,h4`<thth4|l0\r\n",
		"OL,2,Wj \$kj \$kj 'kT]S+`j5k4sjuj7j7o5z%\r\n",
		"OL,3,W\"###\"###\"###T//,/,--.,-.,,-,-.,-.-.,,//\r\n");
		break;
	case "Wales" : ;
	case "Wales politics" : ;
	case "North West Wales" : ;
	case "North East Wales" : ;
	case "South East Wales" : ;
	case "South West Wales" : ;
		$region="Wales";
		$return=array(
		"OL,1,Wj#3kj#3kj#3kD]S   h44|`<l0| h<\$x,\r\n",
		"OL,2,Wj \$kj \$kj 'kT]S   *uu?j7k5pjw1s?\r\n",
		"OL,3,W\"###\"###\"###T//////,,.-.-.,,-,.,.//////\r\n");
		break;
	case "World" : ;
	case "Europe" : ;
	case "Latin America & Caribbean" : ;
	case "Middle East" : ;
	case "US & Canada" : ;
	case "Africa" : ;
	case "Australia" : ;
	case "India" : ;
	case "Asia" : ;
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S   |hh4|,|h<l4| h<l0\r\n",
		"OL,2,Wj \$kj \$kj 'kT]S   ozz%pj7k4pjuz%\r\n",
		"OL,3,W\"###\"###\"###T/////-,,/,,,-.-.,,-,,/////\r\n");
		break;
	case "Politics" : ;
	case "UK Politics" : ;
		$region="Politics";
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S h<|h<|h4 |(|$|h<$|,$ \r\n",
		"OL,2,Wj \$kj \$kj 'kT]S j7#juju0  ju0s{5 \r\n",
		"OL,3,W\"###\"###\"###T///-./-,,-,.,/,/,-,.,,.///\r\n");
		break;
	case "headlines" : ;
		return array(array(
		"OL,1,Wj#3kj#3kj#3kT]Sh4|h<h<|h<th4h4xl0|$|,  \r\n",
		"OL,2,Wj \$kj \$kj 'kT]Sj7jwj7ju?juj5j51s  \r\n",
		"OL,3,W\"###\"###\"###T//-.,-,-.,-,.-,-.,-.,.,,//\r\n"));
	case "index" : ;
		return array(
		"OL,1,D]S|h4|h4 `h44|`<th<|h4h<t hth4|$|hh4|,$\r\n",
		"OL,2,D]Soz%k48!*uu?*u?j7}juju? j7o51ozz%s{5\r\n",
		"OL,3,T//-,/,-.///,,./,.-.,-,-,./-.-.,.-,,/,,.\r\n");
	case "summary" : ;
		return array(
		"OL,1,Wj#3kj#3kj#3kT]S|,$|h4|ll4|ll4|l4|l4|h4 \r\n",
		"OL,2,Wj \$kj \$kj 'kT]Ss{5z5jj5jj5k5k4s{5 \r\n",
		"OL,3,W\"###\"###\"###T//,,.,,.,--.,--.,-.,-.,,./\r\n");
	case "scitechhead" : ;
		return array(
		"OL,1,Wj#3kj#3kj#3kT]S x,\$x,h4 (|$|,`<$|h4    \r\n",
		"OL,2,Wj \$kj \$kj 'kT]S s{%opj5#  s*u0k5    \r\n",
		"OL,3,W\"###\"###\"###T//,,/-,-.//,/,,/,.,-./////\r\n");
	default;
		$return=array(
		"OL,1,Wj#3kj#3kj#3kT]S     xl0|,h44|h,$\r\n",
		'OL,2,Wj $kj $kj \'kT]S     j5s*uu?bs5'."\r\n",
		'OL,3,W"###"###"###T///////,-.,,/,,.-,.///////'."\r\n");
		break;
		}
		
		if ($region == REGION)
			$region=true;
		else
			$region=false;
		
		return array($return,$region);
}

function newsFooter($region,$mpp,$type="normal")
{
	$next=$mpp+1;
	if ($type == "normal")
	{
		if ($next==125)
			$FT='OL,24,AIn Depth BNews IndxCHeadlinesFMain Menu'."\r\n";
		else
			$FT='OL,24,ANext NewsBNews IndxCHeadlinesFMain Menu'."\r\n";
		$FL="FL,$next,102,101,100,F,199\r\n";
	}
	if ($type == "normal" && $region)
	{
		$region=str_replace("Northern Ireland", "N IRELAND", REGION);
		$region=strtoupper($region);
		$region=str_pad($region,9,' ');
		return array(
		"OL,22,T]G$regionCHeadlinesG160CSport   G390\r\n",
		"OL,23,D]GNATIONALC Main menuG100CWeatherG 400\r\n",$FT,$FL);
	}
	elseif ($type == "normal" && !$region)
	{
		return array(
		"OL,22,D]CHome news digestG141CWorld digestG142\r\n",
		"OL,23,D]CNews IndexG102CFlashG150CRegionalG160\r\n",$FT,$FL);
	}
}

function newsHeadlinesfooter($region=false)
{
	if ($region)
	{
		$region=str_replace("Northern Ireland", "N IRELAND", REGION);
		$region=strtoupper($region);
		$region=str_pad($region,9,' ');
		return array(
		"OL,22,T]G$regionCHeadlinesG160CSport   G390\r\n",
		"OL,23,D]GNATIONALC Main menuG100CWeatherG 400\r\n",
		"OL,24,ANext PageBTop StoryCReg SportFMain Menu\r\n",
		"FL,161,161,390,100,F,199\r\n");
	}
	else
	{
		return array(
		"OL,22,W]DGet BBC News on your mobile phone 153\r\n",
		"OL,23,D]CCATCH UP WITH N. IRELAND NEWS    G160\r\n",
		"OL,24,ANews IndexBTop StoryCTV/RadioFMain Menu\r\n",
		"FL,102,104,600,100,F,199\r\n");
	}
}

function newsIndexFooter()
{
	return array(
	"OL,22,T]CSummaryG103CExtraG140CFront page G100\r\n",
	"OL,23,D]CLotteryG555CFlashG150CRegional   G160\r\n",
	"OL,24,ASummary B1st story CLocalNewsFMain Menu\r\n",
	"FL,103,104,160,100,F,199\r\n");
}

function newsSummaryFooter()
{
	return array(
	"OL,22,D]CNews indexG102CExtraG140CWeather G400\r\n",
	"OL,23,D]CFront pageG100CTV   G600CChildrenG500\r\n",
	"OL,24,A1st storyBNews IndxCHeadlinesFMain Menu\r\n",
	"FL,104,102,101,100,F,199\r\n");
}
?>