<?php
class Program
{
	static function Main($args)
	{
		$p = new Program;
	}

	private $sock, $buff = '';

	function __construct()
	{
		$this->sock = new TcpSocket;
		$this->sock->BeginConnect('127.0.0.1', '1337', $this, 'Connected');
	}

	function Connected()
	{
		Console::WriteLine('Connected');

		$buff = 'ClientSentSomething:)';
		$this->sock->BeginSend($buff, $this, 'Sent');
	}

	function Sent()
	{
		Console::WriteLine('Sent');

		$this->sock->BeginReceive($this->buff, 512, $this, 'Received');
	}

	function Received()
	{
		Console::WriteLine('Received: '.$this->buff);
		$this->sock->Disconnect();
	}
}
?>