<?php
class Session
{
	private $session_name;
	private $session_expiry;
	public function __construct($session_name,$session_expiry)
	{	
		session_name($session_name);
		session_set_cookie_params((int)$session_expiry);
		session_start();
	}
}
?>