<?php
/*
	makeceefax.php
	Generates a Ceefax service from various 'modules' which make specific magazines
	Nathan Dane, 2018
*/
define ("PAGEDIR","/home/pi/Pages");

// Load Modules
$moduledir=file_get_contents("modules.txt");
$moduledir=explode("\r\n",$moduledir);

foreach ($moduledir as $key=>$module)
{
	if(file_exists("make$module/make$module.php"))
	{
		include "make$module/make$module.php";
		${$module}=file("make$module/pages.txt");
		echo "Loaded ".ucfirst($module)." module\r\n";
	}
	else
	{
		echo ucfirst($module)." module not found\r\n";
		unset($moduledir[$key]);
	}
}

echo "Saving to ".PAGEDIR."\r\n";
print_r($moduledir);

foreach($moduledir as $module)
{
	print_r ($$module);
}