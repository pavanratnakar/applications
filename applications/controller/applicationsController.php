<?php
class applicationsController
{
	private $utils;
	private $applicationController;
	public function __construct($ref=null)
	{
		$this->checkIfAJAX();
		include_once($this->path.'../global/class/config.php');
		include_once($this->path.'../global/class/utils.php');
		include_once($this->path.'../applications/class/applications.php');
		$this->utils=new Utils();
		$this->applications=new Applications();
		if($ref)
		{
			$ref=$this->utils->checkValues($ref);
			$this->$ref();
		}
	}
	public function listApplications($userid=null)
	{
		return $this->applications->listApplications($userid);
	}
	public function listApplicationsAjax()
	{
		$returnArray= array(
			"status" => TRUE,
			"message" => $this->listApplications()
		);
		$response = $_POST["jsoncallback"] . "(" .json_encode($returnArray). ")";
		echo $response;
		unset($response);
	}
	public function checkIfAJAX()
	{
		if(!file_exists('../global/class/utils.php')) 
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