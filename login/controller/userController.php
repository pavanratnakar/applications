<?php
class UserController
{
	private $utils;
	private $user;
	public function __construct($ref=null)
	{
		$this->checkIfAjax();
		include_once($this->path.'../global/controller/sessionController.php');
		include_once($this->path.'../global/class/config.php');
		include_once($this->path.'../global/class/utils.php');
		include_once($this->path.'../login/class/user.php');
		$sessionController=new SessionController();
		$this->utils=new Utils();
		$this->user=new User();
		if($ref)
		{
			$ref=$this->utils->checkValues($ref);
			$this->$ref();
		}
	}
	public function userLogin()
	{
		$login_user=$this->utils->checkValues($_REQUEST['login_user']);
		$login_password=$this->utils->checkValues($_REQUEST['login_password']);
		$login_rememberMe=$this->utils->checkValues($_REQUEST['login_rememberMe']);
		if($this->user->loginUser($login_user,$login_password,$login_rememberMe))
		{
			$status=TRUE;
			$message='Logged in successfully';
		}
		else
		{
			$status=FALSE;
			$message='The password you entered is incorrect. Please try again (make sure your caps lock is off).';
		}
		$returnArray= array(
			"status" => $status,
			"message" => $message
		);
		$response = $_POST["jsoncallback"] . "(" .json_encode($returnArray). ")";
		echo $response;
		unset($response);
	}
	public function checkUserStatus()
	{
		return $this->user->checkUserStatus();
	}
    public function getFullName()
    {
        return $this->user->getFullName();
    }
	public function userLogout()
	{
		if($this->user->userLogout())
		{
			$status=TRUE;
			$message='Logged out user successfully';
		}
		else
		{
			$status=FALSE;
			$message='Could not logout user';
		}
		$returnArray= array(
			"status" => $status,
			"message" => $message
		);
		$response = $_GET["jsoncallback"] . "(" .json_encode($returnArray). ")";
		echo $response;
		unset($response);
	}
    public function userList()
    {
        echo $this->user->userList();
    }
    public function userJsonList()
    {
        $response = $_GET["jsoncallback"] . "(" .json_encode($this->user->userJsonList()). ")";
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
if(isset($_REQUEST['ref']))
{
    if(preg_match('/^user/', $_REQUEST['ref'])) {
        $userController=new UserController($_REQUEST['ref']);
    }
}
?>