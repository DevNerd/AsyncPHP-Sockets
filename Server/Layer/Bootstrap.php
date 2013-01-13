<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

$s = DIRECTORY_SEPARATOR;
$root = dirname(__DIR__).$s;

foreach (glob($root.'Layer'.$s.'Classes'.$s.'*.php') as $filename)
{
	require($filename);
}

foreach (glob($root.'Application'.$s.'*.php') as $filename)
{
	require($filename);
}

Thread::Start('Program::Main');
?>