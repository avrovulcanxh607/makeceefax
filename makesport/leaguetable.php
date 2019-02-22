<?php
/*
	leaguetable.php 
	Gets a football league table from the BBC Sport website and outputs it as an array
	leaguetable.php is part of makeceefax.php
	Nathan Dane, 2019
*/

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