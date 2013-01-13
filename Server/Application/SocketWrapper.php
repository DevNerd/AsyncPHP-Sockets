<?php
class SocketWrapper
{
	private $socket, $buffer;
	private $sendbuffer;

	function __construct($socket)
	{
		Console::WriteLine('New Connection from '.$socket->IP);

		$this->socket =& $socket;

		$this->socket->BeginReceive($this->buffer, 512, $this, 'NewData');
	}

	function NewData()
	{
		Console::WriteLine('Received: '.$this->buffer);

		$this->sendbuffer = 'Response';
		$this->socket->BeginSend($this->sendbuffer, $this, 'SentData');
	}

	function SentData()
	{
		Console::WriteLine('Sent: '.$this->sendbuffer);
	}
}
?>