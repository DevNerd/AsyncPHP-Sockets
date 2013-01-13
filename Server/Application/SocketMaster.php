<?php
class SocketMaster
{
	private $master, $port, $clients = Array();

	function __construct($port)
	{
		Console::WriteLine('Starting Socket'); 
		$this->port = $port;

		$this->master = new TcpSocket;
		$this->master->Bind($port);
	}

	function Start()
	{
		$this->master->Listen(100);
		Console::WriteLine('Socket started on port '.$this->port);

		$this->Accept();
	}

	function NewConnection()
	{
		$clients[] = new SocketWrapper($this->master->EndAccept());

		$this->Accept();
	}

	function Accept()
	{
		$this->master->BeginAccept($this, 'NewConnection');
	}
}
?>