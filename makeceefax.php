<?php
/*
	makeceefax.php
	Generates a Ceefax service from various 'modules' which make specific magazines
	Nathan Dane, 2018
*/
// Settings. See the Wiki for details @todo
define ("VERSION","V1 alpha");
define ("PAGEDIR",".");	// Where do you want your teletext files?
define ("PREFIX","MENU");	// What do you want the filename prefix to be?
define ("REGION","Northern Ireland");	// What UK TV Region are you in? 

include "common.php";
echo "MAKECEEFAX.PHP ".VERSION." (c) Nathan Dane, 2018\r\n";
echo "Saving to ".PAGEDIR."/\r\n\r\n";

// Load Modules
$moduledir=file_get_contents("modules.txt");
$moduledir=explode("\r\n",$moduledir);

foreach ($moduledir as $key=>$module)
{
	if(file_exists("make$module/make$module.php"))
	{
		include "make$module/make$module.php";
	}
	else
	{
		echo ucfirst($module)." module not found\r\n";
		unset($moduledir[$key]);
	}
}
foreach ($moduledir as $function)
{
	$function="make".$function;
	echo "\r\nRunning $function...\r\n";
	$function();
	echo "$function finished\r\n";
}