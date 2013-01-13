<?php
class TcpSocket
{
	private $socket;
	public $received = 0, $sent = 0;
	public $IP;

	function __construct($socket = false)
	{
		if ($socket !== false)
		{
			$this->socket =& $socket;
		}
		else
		{
			$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		}

		socket_set_nonblock($this->socket);
	}

	/* Server */

	private $AcceptCallbackObj, $AcceptCallbackMethod, $AcceptedSocket;

	function Bind($port)
	{
		socket_bind($this->socket, '0.0.0.0', intval($port));
	}

	function Listen($backlog)
	{
		socket_listen($this->socket, $backlog);
	}

	function BeginAccept(&$callObj, $callMethod)
	{
		$this->AcceptCallbackObj =& $callObj;
		$this->AcceptCallbackMethod =& $callMethod;

		$this->TestAccept();
	}

	function TestAccept()
	{
		if ($this->socket === false)
		{
			return;
		}

		$this->AcceptedSocket = @socket_accept($this->socket);

		if ($this->AcceptedSocket !== false)
		{
			new Thread($this->AcceptCallbackObj, $this->AcceptCallbackMethod);
			return;
		}

		new Thread($this, 'TestAccept');
	}

	function EndAccept()
	{
		$return = new self($this->AcceptedSocket);
		socket_getsockname($this->AcceptedSocket, $return->IP);

		unset($this->AcceptedSocket);

		return $return;
	}

	/* End Server */

	/* Client */

	private $ConnectIP, $ConnectPort, $ConnectCallbackObj, $ConnectCallbackMethod;

	function BeginConnect($ip, $port, &$callObj, $callMethod)
	{
		$this->ConnectIP = $ip;
		$this->ConnectPort = intval($port);

		$this->ConnectCallbackObj =& $callObj;
		$this->ConnectCallbackMethod =& $callMethod;

		$this->TestConnect();
	}

	function TestConnect()
	{
		if ($this->socket === false)
		{
			return;
		}

		if (@socket_connect($this->socket, $this->ConnectIP, $this->ConnectPort) || socket_last_error($this->socket) == SOCKET_EALREADY)
		{
			new Thread($this->ConnectCallbackObj, $this->ConnectCallbackMethod);
			return;
		}

		new Thread($this, 'TestConnect');
	}

	/* End Client */

	/* Functions */

	private $ReceiveBuffer, $ReceiveLength, $ReceiveCallbackObj, $ReceiveCallbackMethod;

	function BeginReceive(&$buffer, $len, &$callObj, $callMethod)
	{
		$this->ReceiveBuffer =& $buffer;
		$this->ReceiveLength = $len;

		$this->ReceiveCallbackObj =& $callObj;
		$this->ReceiveCallbackMethod =& $callMethod;

		$this->TestReceive();
	}

	function TestReceive()
	{
		if ($this->socket === false)
		{
			return;
		}

		if (($int = @socket_recv($this->socket, $this->ReceiveBuffer, $this->ReceiveLength, null)) !== false)
		{
			$this->received += $int;
			new Thread($this->ReceiveCallbackObj, $this->ReceiveCallbackMethod);
			return;
		}

		new Thread($this, 'TestReceive');
	}

	private $SendBuffer, $ToSendLength, $SendCallbackObj, $SendCallbackMethod;

	function BeginSend(&$buffer, &$callObj, $callMethod)
	{
		$this->SendBuffer =& $buffer;
		$this->ToSendLength = strlen($buffer);

		$this->SendCallbackObj =& $callObj;
		$this->SendCallbackMethod =& $callMethod;

		$this->TestSend();
	}

	function TestSend()
	{
		if ($this->socket === false)
		{
			return;
		}

		if (($int = @socket_write($this->socket, $this->SendBuffer)) !== false)
		{
			$this->sent += $int;
			$this->ToSendLength -= $int;

			if ($this->ToSendLength < 1)
			{
				new Thread($this->SendCallbackObj, $this->SendCallbackMethod);
				return;
			}

			$this->SendBuffer = substr($this->SendBuffer, $int);
		}

		new Thread($this, 'TestSend');
	}

	function Disconnect($reuse = false)
	{
		if ($this->socket !== false)
		{
			@socket_close($this->socket);

			if ($reuse)
			{
				$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			}
			else
			{
				$this->socket = false;
			}
		}
	}

	/* End Functions */
}
?>