<?php
class Applications
{
	private $application_id;
	private $application_name;
	private $application_description;
	private $application_link;
	private $application_creation_date;
	private $application_status;
	private $utils;
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
	}
	public function listApplications($userid=null)
	{	
		$userid=$this->mysqli->real_escape_string($userid);
		if ($result = $this->mysqli->query("SELECT application_id,application_name,application_description,application_link,application_creation_date,application_status FROM ".Config::$applications." ORDER BY application_name")) 
		{
			while ($row = $result->fetch_object()) 
			{
				$application_array = array();
				$application_array['id'] = $row->application_id;
				$application_array['title'] =  $row->application_name;
				$application_array['description'] =  $row->application_description;
				if($userid)
				{
					$application_array['link'] =  $row->application_link;
				}
				$application_array['date'] =  $row->application_creation_date;
				$application_array['status'] = $row->application_status;
				$applications[] = $application_array;
			}
			return $applications;
		}
		else
		{
			return FALSE;
		}
	}
	public function __destruct()
	{
		$this->mysqli->close();
	}
}
?>