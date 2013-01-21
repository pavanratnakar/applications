<?php
class Subcategory
{
	private $mysqli;
	private $utils;
	private $table;
	private $id='subcategory_id';
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
		$this->table=Config::$expense_sub_category;
	}
    public function getSelect($category_id)
	{
		$i=1;
        $category_id=$this->mysqli->real_escape_string($category_id);
        $return_array = 0;
		if ($result = $this->mysqli->query("SELECT * FROM ".$this->table." WHERE category_id='".$category_id."' AND delete_flag=FALSE ORDER BY subcategory_name")) 
		{
			while ($row = $result->fetch_object())
			{
				$return_array .= $row->subcategory_id.':'.$row->subcategory_name;
				if($result->num_rows!=$i)
				{
					$return_array .=';';
				}
				$i++;
			}
		}
		return $return_array;
	}
	public function getDetails($page,$limit,$sidx,$sord,$wh="")
	{
		$page=$this->mysqli->real_escape_string($page);
		$limit=$this->mysqli->real_escape_string($limit);
		$sidx=$this->mysqli->real_escape_string($sidx);
		$sord=$this->mysqli->real_escape_string($sord);
		if ($result = $this->mysqli->query("SELECT COUNT(*) AS count FROM ".$this->table." a,".Config::$expense_category." b WHERE b.delete_flag=FALSE AND a.delete_flag=FALSE AND a.category_id=b.category_id ".$wh)) 
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
				if ($result1 = $this->mysqli->query("SELECT a.subcategory_id, a.subcategory_name, b.category_name FROM ".$this->table." a,".Config::$expense_category." b WHERE a.category_id=b.category_id AND b.delete_flag=FALSE AND a.delete_flag=FALSE ".$wh." ORDER BY ".$sidx." ". $sord." LIMIT ".$start." , ".$limit)) 
				{
					$responce->page = $page;
					$responce->total = $total_pages;
					$responce->records = $count;
					$i=0;
					while ($row1 = $result1->fetch_object())
					{
						$responce->rows[$i]['subcategory_id']=$row1->subcategory_id;
						$responce->rows[$i]['cell']=array($row1->subcategory_id,$row1->subcategory_name,$row1->category_name);
						$i++;
					}
					return $responce;
				}
			}
		}
	}
	public function addDetails($subcategory_name,$category_id)
	{
		$subcategory_name=$this->mysqli->real_escape_string($subcategory_name);
		$category_id=$this->mysqli->real_escape_string($category_id);
		if ($result = $this->mysqli->query("INSERT INTO ".$this->table."(subcategory_name,category_id) VALUES('$subcategory_name','$category_id')")) 
		{
			if($this->mysqli->affected_rows>0)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	public function editDetails($subcategory_name,$category_id,$id)
	{
		$subcategory_name=$this->mysqli->real_escape_string($subcategory_name);
		$category_id=$this->mysqli->real_escape_string($category_id);
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET subcategory_name='$subcategory_name', category_id='$category_id' WHERE subcategory_id='$id'")) 
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