<?php
class User
{
	private $user_id;
	private $user_email;
	private $user_ip;
	private $mysqli;
	private $utils;
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
		$this->user_id=$this->checkUserStatus();
	}
	public function loginUser($user_email,$user_password,$user_rememberMe)
	{	
		$user_email=$this->mysqli->real_escape_string($user_email);
		$user_password=$this->mysqli->real_escape_string(md5($user_password));
		if ($result = $this->mysqli->query("SELECT user_id FROM ".Config::$application_users." WHERE user_email='$user_email' and user_password='$user_password'")) 
		{
			if($result->num_rows==1)
			{
				while ($row = $result->fetch_object()) 
				{
					$this->user_id=$row->user_id;
					$_SESSION['user_id'] = $this->user_id;
					if($rememberMe)
					{
						$_SESSION['user_rememberMe'] =$user_rememberMe;
						setcookie('pavan_applications_rememberMe',$user_rememberMe);
					}
					$this->user_ip = $this->utils->ip_address_to_number($_SERVER['REMOTE_ADDR']);
					if ($result = $this->mysqli->query("INSERT INTO ".Config::$application_active_users."(user_id,user_login_time,user_ip) VALUES ('".$this->user_id."',now(),'".$this->user_ip."')"))
					{
						return TRUE;
					}
					else
					{
						return FALSE;
					}
				}
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
	}
    public function getFullName($user_id=null)
    {
        if($user_id==null) {
            $user_id=$this->checkUserStatus();
        }
        if ($result = $this->mysqli->query("SELECT user_firstname,user_lastname FROM ".Config::$application_users_attributes." WHERE user_id=".$this->checkUserStatus()."")) 
		{
			if($result->num_rows==1)
			{
				while ($row = $result->fetch_object()) 
				{
                     return $row->user_firstname.' '.$row->user_lastname;
				}
			}
			else
			{
				return FALSE;
			}
		}
	}
	public function checkUserStatus()
	{
		if(isset($_SESSION['user_id']))
		{
			return $_SESSION['user_id'];
		}
		else
		{
			return FALSE;
		}
	}
	public function userLogout()
	{
		if ($result = $this->mysqli->query("UPDATE ".Config::$application_active_users." SET user_logoff_time = NOW() WHERE user_id='".$this->user_id."' AND user_logoff_time='0000-00-00 00:00:00'")) 
		{
			$_SESSION = array();
			session_destroy();
			return TRUE;
		}
		return FALSE;
	}
	public function userList()
	{
		$i=1;
		if ($result = $this->mysqli->query("SELECT * FROM ".Config::$application_users_attributes." WHERE 1=1 ORDER BY user_firstname,user_lastname")) 
		{
			while ($row = $result->fetch_object())
			{
                if($i==1) {
                    $return_array .='0:General;';
                }
				$return_array .= $row->user_id.':'.$row->user_firstname.' '.$row->user_lastname;
				if($result->num_rows!=$i)
				{
					$return_array .=';';
				}
				$i++;
			}
		}
		return $return_array;
	}
    public function userJsonList()
	{
        $return_array=array(
            0 => Array ( 
                id => 0, 
                user_name => "General" 
        ));
		if ($result = $this->mysqli->query("SELECT * FROM ".Config::$application_users_attributes." WHERE 1=1 ORDER BY user_firstname,user_lastname")) 
		{
			while ($row = $result->fetch_object()) {
                $user_array = array();
                $user_array['id'] = $row->user_id;
                $user_array['user_name'] = $row->user_firstname.' '.$row->user_lastname;
                $return_array[] = $user_array;
			}
		}
		return $return_array;
	}
	public function __destruct()
	{
		$this->mysqli->close();
	}
}
?>