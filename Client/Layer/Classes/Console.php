<?php
class Console
{
	static function WriteLine($txt = '')
	{
		self::Write($txt."\r\n");
	}

	static function Write($txt)
	{
		echo $txt;
	}
}
?>