<?php
/*
	makeceefax.php
	Generates a Ceefax service from various 'modules' which make specific magazines
	Nathan Dane, 2018
*/
// Settings. See the Wiki for details @todo
define ("VERSION","V1 alpha");
define ("PAGEDIR","/home/pi/ceefax");	// Where do you want your teletext files?
define ("PREFIX","AUTO");	// What do you want the filename prefix to be?
define ("REGION","Northern Ireland");	// What UK TV Region are you in? 

require "common.php";
require "simple_html_dom.php";

echo "MAKECEEFAX.PHP ".VERSION." (c) Nathan Dane, 2018\r\n";
echo "Saving to ".PAGEDIR."/\r\n\r\n";

// Load Modules
$moduledir=file_get_contents("modules.txt");	// If there's no modules.txt, show's over. Need redundancy
$moduledir=explode("\r\n",$moduledir);

foreach ($moduledir as $key=>$module)
{
	if(file_exists("make$module/make$module.php"))	// Make sure the module exists before trying to load it
	{
		include "make$module/make$module.php";	// Load it. Might remove the 'make'
	}
	else
	{
		echo ucfirst($module)." module not found\r\n";	// If it doesn't exist, don't try to include or run it 
		unset($moduledir[$key]);	// And delete it from the list of available modules
	}
}
foreach ($moduledir as $function)	// Run each available module's initial function
{
	$function="make".$function;
	echo "\r\nRunning $function...\r\n";
	$function();
	echo "$function finished\r\n";
}

$time=date("H:i:s d-m-Y");
echo "MAKECEEFAX.PHP finished at $time\r\n";	// Closing statement. Useful for logging (but not much else!)
?>