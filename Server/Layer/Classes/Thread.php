<?php
class Thread
{
	private static $todo = Array();

	static function Start($method)
	{
		new Thread($method, Array());

		while (count(self::$todo) > 0)
		{
			foreach (self::$todo as $key => $func)
			{
				unset(self::$todo[$key]);
				call_user_func_array($func[0], $func[1]);
			}
		}
	}

	function __construct()
	{
		$args = func_get_args();

		if (is_string($args[0]))
		{
			$call = explode('::', array_shift($args));
		}
		else if (is_object($args[0]))
		{
			if ($args[0] instanceof Closure)
			{
				$call = array_shift($args);
			}
			else
			{
				$call = Array(array_shift($args), array_shift($args));
			}
		}

		self::$todo[] = Array($call, $args);
	}
}
?>