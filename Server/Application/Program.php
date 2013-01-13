<?php
class Program
{
	static $Master;

	static function Main($args)
	{
		self::$Master = new SocketMaster(1337);
		self::$Master->Start();
	}
}
?>