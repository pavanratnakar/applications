<?php
class Income
{
	private $mysqli;
	private $utils;
	private $table;
	private $id='income_id';
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
		$this->table=Config::$income_main;
	}
	public function getDetails($page,$limit,$sidx,$sord,$wh="",$toDate=null,$fromDate=null)
	{
		$page=$this->mysqli->real_escape_string($page);
		$limit=$this->mysqli->real_escape_string($limit);
		$sidx=$this->mysqli->real_escape_string($sidx);
		$sord=$this->mysqli->real_escape_string($sord);
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
		if ($result = $this->mysqli->query("SELECT COUNT(*) AS count FROM ".$this->table." WHERE delete_flag=0 AND income_date BETWEEN '".$toDate."' AND '".$fromDate."' ".$wh)) 
		{
			while ($row = $result->fetch_object())
			{
				$count = $row->count;
				if( $count >0 ) {
					$total_pages = ceil($count/$limit);
				} 
				else {
					$total_pages = 0;
				}
				if ($page > $total_pages) {
					$page=$total_pages;
				}
				$start = $limit*$page - $limit; // do not put $limit*($page - 1)
				if ($start<0) {
					$start = 0;
				}
                $query="SELECT income_id, income_date, b.category_name AS category_name, amount, note, CONCAT(c.user_firstname, ' ', c.user_lastname) AS for_username,  income_enterDate 
                    FROM 
                    ((".$this->table." a JOIN ".Config::$income_category." b))
                    LEFT JOIN 
                    ".Config::$application_users_attributes." c ON c.user_id=a.for_userid
                    WHERE a.delete_flag=0 AND b.category_id=a.category_id  AND income_date BETWEEN '".$toDate."' AND '".$fromDate."'  
                    ORDER BY ".$sidx." ". $sord." LIMIT ".$start." , ".$limit;
				if ($result1 = $this->mysqli->query($query)) {
					$responce->page = $page;
					$responce->total = $total_pages;
					$responce->records = $count;
					$i=0;
                    $sum=0;
					while ($row1 = $result1->fetch_object()) {
                        if($row1->for_username) {
                            $name=$row1->for_username;
                        }
                        else {
                            $name='General';
                        }
						$responce->rows[$i]['income_id']=$row1->expense_id;
                        $sum=$sum+$row1->amount;
						$responce->rows[$i]['cell']=array(
							$row1->income_id,
							$row1->income_date,
                            $row1->category_name,
							$row1->amount,
                            $name,
                            $row1->note,
							$row1->income_enterDate
						);
						$i++;
					}
                    $responce->userdata['amount'] = $sum;		
					return $responce;
				}
			}
		}
	}
	public function addDetails($income_date,$category_id,$amount,$note,$for_userid,$by_userid)
	{
    	$income_date=$this->mysqli->real_escape_string($income_date);
		$category_id=$this->mysqli->real_escape_string($category_id);
		$amount=$this->mysqli->real_escape_string($amount);
		$note=$this->mysqli->real_escape_string($note);
        $for_userid=$this->mysqli->real_escape_string($for_userid);
		if ($result = $this->mysqli->query("INSERT INTO ".$this->table."(income_date,category_id,amount,note,for_userid,by_userid) VALUES('$income_date','$category_id','$amount','$note','$for_userid','$by_userid')")) {
			if($this->mysqli->affected_rows>0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	public function editDetails($income_date,$category_id,$amount,$note,$id,$for_userid,$by_userid)
	{
		$income_date=$this->mysqli->real_escape_string($income_date);
        $category_id=$this->mysqli->real_escape_string($category_id);
        $amount=$this->mysqli->real_escape_string($amount);
        $note=$this->mysqli->real_escape_string($note);
        $for_userid=$this->mysqli->real_escape_string($for_userid);
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET income_date='$income_date',category_id='$category_id',amount='$amount',note='$note',for_userid='$for_userid' WHERE expense_id='$id' AND by_userid='$by_userid'")) {
			if($this->mysqli->affected_rows>0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	public function deleteDetails($id,$by_userid)
	{
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET delete_flag=TRUE WHERE ".$this->id."='".$id."' AND by_userid='$by_userid'")) {
			if($this->mysqli->affected_rows>0) {
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