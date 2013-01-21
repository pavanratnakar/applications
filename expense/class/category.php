<?php
class Category
{
	private $mysqli;
	private $utils;
	private $table;
	private $id='category_id';
	public function __construct($type)
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
        if ($type == 'expense') {
            $this->table=Config::$expense_category;
        }
        else if($type == 'income') {
            $this->table=Config::$income_category;
        }
        else if($type == 'payment') {
            $this->table=Config::$payment_category;
        }
	}
	public function getSelect()
	{
		$i=1;
		if ($result = $this->mysqli->query("SELECT *FROM ".$this->table." WHERE delete_flag=FALSE ORDER BY category_name")) 
		{
			while ($row = $result->fetch_object())
			{
				$return_array .= $row->category_id.':'.$row->category_name;
				if($result->num_rows!=$i)
				{
					$return_array .=';';
				}
				$i++;
			}
		}
		return $return_array;
	}
	public function getDetails($page,$limit,$sidx,$sord,$wh="",$accountable=null)
	{
		$page=$this->mysqli->real_escape_string($page);
		$limit=$this->mysqli->real_escape_string($limit);
		$sidx=$this->mysqli->real_escape_string($sidx);
		$sord=$this->mysqli->real_escape_string($sord);
		if ($result = $this->mysqli->query("SELECT COUNT(*) AS count FROM ".$this->table." WHERE delete_flag=FALSE ".$wh)) 
		{
			while ($row = $result->fetch_object())
			{
				$count = $row->count;
				if( $count >0 ) 
				{
					$total_pages = ceil($count/$limit);
				} 
				else 
				{
					$total_pages = 0;
				}
				if ($page > $total_pages)
				{
					$page=$total_pages;
				}
				$start = $limit*$page - $limit; // do not put $limit*($page - 1)
				if ($start<0)
				{
					$start = 0;
				}
                if($accountable!=null) {
                    $result1 = $this->mysqli->query("SELECT category_id, category_name, accountable FROM ".$this->table." WHERE delete_flag=FALSE ".$wh." ORDER BY ".$sidx." ". $sord." LIMIT ".$start." , ".$limit);
                } else {
                    $result1 = $this->mysqli->query("SELECT category_id, category_name FROM ".$this->table." WHERE delete_flag=FALSE ".$wh." ORDER BY ".$sidx." ". $sord." LIMIT ".$start." , ".$limit);
                }
                if ($result1) 
                {
                    $responce->page = $page;
                    $responce->total = $total_pages;
                    $responce->records = $count;
                    $i=0;
                    while ($row1 = $result1->fetch_object())
                    {
                        $responce->rows[$i]['category_id']=$row1->category_id;
                        if( isset($row1->accountable) ) {
                            $responce->rows[$i]['cell'] =array($row1->category_id,$row1->category_name,$row1->accountable);
                        } else {
                            $responce->rows[$i]['cell'] =array($row1->category_id,$row1->category_name);
                        }
                        $i++;
                    }
                    return $responce;
                }
			}
		}
	}
	public function addDetails($category_name,$accountable=null)
	{
		$category_name=$this->mysqli->real_escape_string($category_name);
        if($accountable!=null) {
            $accountable=$this->mysqli->real_escape_string($accountable);
            $result = $this->mysqli->query("INSERT INTO ".$this->table."(category_name,accountable) VALUES('$category_name','$accountable')");
        }
        else {
            $result = $this->mysqli->query("INSERT INTO ".$this->table."(category_name) VALUES('$category_name')");
        }
        if ($result) 
        {
            if($this->mysqli->affected_rows>0)
            {
                return TRUE;
            }
        }
		return FALSE;
	}
	public function editDetails($category_name,$id,$accountable=null)
	{
		$category_name=$this->mysqli->real_escape_string($category_name);
		$id=$this->mysqli->real_escape_string($id);
        if($accountable!=null) {
            $accountable=$this->mysqli->real_escape_string($accountable);
            $result = $this->mysqli->query("UPDATE ".$this->table." SET category_name='$category_name',accountable='$accountable' WHERE category_id='$id'");
        }
        else {
            $result = $this->mysqli->query("UPDATE ".$this->table." SET category_name='$category_name' WHERE category_id='$id'");
        }
        if ($result) 
        {
            if($this->mysqli->affected_rows>0)
            {
                return TRUE;
            }
        }
		return FALSE;
	}
	public function deleteDetails($id)
	{
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET delete_flag=TRUE WHERE ".$this->id."='".$id."'")) 
		{
			if($this->mysqli->affected_rows>0)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	public function __destruct()
	{
		$this->mysqli->close();
	}
}
?>