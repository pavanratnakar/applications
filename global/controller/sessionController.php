<?php
class SessionController
{
	private $path;
	private $ajax;
	public function __construct()
	{
		$this->checkIfAJAX();
		include_once($this->path.'../global/class/session.php');
		$session=new Session(Config::$session_name,Config::$session_expiry);
	}
	public function checkIfAJAX()
	{
		if(!file_exists('../global/class/session.php')) 
		{	
			$this->ajax=TRUE;
			$this->path='../';
		}
		else
		{	
			$this->ajax=FALSE;
			$this->path='';
		}
	}
}	
?>