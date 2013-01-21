<?php
class Expense
{
	private $mysqli;
	private $utils;
	private $table;
	private $id='expense_id';
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
		$this->table=Config::$expense_main;
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
		if ($result = $this->mysqli->query("SELECT COUNT(*) AS count FROM ".$this->table." WHERE delete_flag=0 AND expense_date BETWEEN '".$toDate."' AND '".$fromDate."' ".$wh)) 
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
                $query="SELECT expense_id, expense_date, b.category_name AS category_name, c.subcategory_name AS subcategory_name, amount, note, CONCAT(e.user_firstname, ' ', e.user_lastname) AS by_username,  expense_enterDate, CONCAT(d.user_firstname, ' ', d.user_lastname) AS for_username, f.category_name as payment_name
                    FROM 
                    ((".$this->table." a JOIN ".Config::$expense_category." b) JOIN ".Config::$expense_sub_category." c)
                    LEFT JOIN 
                    ".Config::$application_users_attributes." d ON d.user_id=a.for_userid
                    LEFT JOIN 
                    ".Config::$application_users_attributes." e ON e.user_id=a.by_userid
                    LEFT JOIN 
                    ".Config::$payment_category." f ON f.category_id=a.payment_id 
                    WHERE a.delete_flag=0 AND b.category_id=c.category_id AND a.subcategory_id=c.subcategory_id AND expense_date BETWEEN '".$toDate."' AND '".$fromDate."'  
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
						$responce->rows[$i]['expense_id']=$row1->expense_id;
                        $sum=$sum+$row1->amount;
						$responce->rows[$i]['cell']=array(
							$row1->expense_id,
							$row1->expense_date,
                            $row1->category_name,
							$row1->subcategory_name,
							$row1->amount,
                            $name,
                            $row1->payment_name,
                            $row1->note,
							$row1->by_username,
							$row1->expense_enterDate
						);
						$i++;
					}
                    $responce->userdata['amount'] = $sum;
                    $query1="SELECT sum(a.amount) as expense_amount FROM ".$this->table." a,".Config::$payment_category." b  WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."' AND a.delete_flag=0 AND b.accountable=1 AND a.payment_id=b.category_id";
                    if ($result2 = $this->mysqli->query($query1)) {
                        while ($row2 = $result2->fetch_object()) {
                            $responce->userdata['category_name'] = 'E = '.$row2->expense_amount;
                            $temp = - ($row2->expense_amount);
                        }
                    }
                    $query2="SELECT sum(amount) as income_amount FROM ".Config::$income_main." WHERE income_date BETWEEN '".$toDate."' AND '".$fromDate."' AND delete_flag=0";
                    if ($result3 = $this->mysqli->query($query2)) {
                        while ($row3 = $result3->fetch_object()) {
                            $responce->userdata['expense_date'] = 'I = '.$row3->income_amount;
                            $temp = $temp + $row3->income_amount;
                        }
                    }
                    $query3="SELECT sum(amount) as expense_amount FROM ".$this->table." WHERE expense_date BETWEEN '".$toDate."' AND '".$fromDate."' AND delete_flag=0 AND payment_id=2";
                    if ($result4 = $this->mysqli->query($query3)) {
                        while ($row4 = $result4->fetch_object()) {
                            $responce->userdata['subcategory_name'] = 'C = '.$row4->expense_amount;
                        }
                    }
                    $responce->userdata['by_username'] = 'R = '.( $temp );
					return $responce;
				}
			}
		}
	}
	public function addDetails($expense_date,$subcategory_id,$amount,$note,$for_userid,$payment_id,$by_userid)
	{
    	$expense_date=$this->mysqli->real_escape_string($expense_date);
		$subcategory_id=$this->mysqli->real_escape_string($subcategory_id);
		$amount=$this->mysqli->real_escape_string($amount);
		$note=$this->mysqli->real_escape_string($note);
        $for_userid=$this->mysqli->real_escape_string($for_userid);
        $payment_id=$this->mysqli->real_escape_string($payment_id);
		if ($result = $this->mysqli->query("INSERT INTO ".$this->table."(expense_date,subcategory_id,amount,note,for_userid,payment_id,by_userid) VALUES('$expense_date','$subcategory_id','$amount','$note','$for_userid','$payment_id','$by_userid')")) {
			if($this->mysqli->affected_rows>0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	public function editDetails($expense_date,$subcategory_id,$amount,$note,$id,$for_userid,$payment_id,$by_userid)
	{
		$expense_date=$this->mysqli->real_escape_string($expense_date);
        $subcategory_id=$this->mysqli->real_escape_string($subcategory_id);
        $amount=$this->mysqli->real_escape_string($amount);
        $note=$this->mysqli->real_escape_string($note);
        $for_userid=$this->mysqli->real_escape_string($for_userid);
        $payment_type=$this->mysqli->real_escape_string($payment_id);
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET expense_date='$expense_date',subcategory_id='$subcategory_id',amount='$amount',note='$note',for_userid='$for_userid',payment_id='$payment_id' WHERE expense_id='$id' AND by_userid='$by_userid'")) {
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
    public function visulize_category($toDate=null,$fromDate=null)
    {
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
        $query="SELECT 
        c.category_name AS name, 
        IFNULL(SUM(a.amount),0) AS amount
        FROM 
        ".$this->table." a
        RIGHT JOIN ".Config::$expense_sub_category." b ON a.subcategory_id=b.subcategory_id
        RIGHT JOIN ".Config::$expense_category." c ON c.category_id=b.category_id
        WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."'
        GROUP BY
        c.category_name
        ORDER BY amount DESC LIMIT ".Config::$statistics_max_range."";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_object()) {
                $visulize_array = array();
                $visulize_array['name'] = $row->name;
                $visulize_array['amount'] = $row->amount;
                $visulize[] = $visulize_array;
            }
            return $visulize;
        }
    }
    public function visulize_sub_category($toDate=null,$fromDate=null)
    {
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
        $query="SELECT 
        b.subcategory_name AS name, 
        IFNULL(SUM(a.amount),0) AS amount
        FROM 
        ".$this->table." a
        RIGHT JOIN ".Config::$expense_sub_category." b ON a.subcategory_id=b.subcategory_id
        WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."'
        GROUP BY
        b.subcategory_name
        ORDER BY amount DESC LIMIT ".Config::$statistics_max_range."";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_object()) {
                $visulize_array = array();
                $visulize_array['name'] = $row->name;
                $visulize_array['amount'] = $row->amount;
                $visulize[] = $visulize_array;
            }
            return $visulize;
        }
    }
    public function visulize_user($toDate=null,$fromDate=null)
    {
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
        $query="SELECT 
        IFNULL(CONCAT(b.user_firstname,' ',b.user_lastname),'General') AS name,
        IFNULL(SUM(a.amount),0) AS amount
        FROM 
         ".$this->table." a
        RIGHT JOIN ".Config::$application_users_attributes." b ON a.for_userid=b.user_id
        WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."'
        GROUP BY
        name
        ORDER BY amount DESC LIMIT ".Config::$statistics_max_range."";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_object()) {
                $visulize_array = array();
                $visulize_array['name'] = $row->name;
                $visulize_array['amount'] = $row->amount;
                $visulize[] = $visulize_array;
            }
            return $visulize;
        }
    }
    public function visulize_payment($toDate=null,$fromDate=null)
    {   
        $payment_method = NULL;
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
        $query="SELECT 
        b.category_name AS name, 
        IFNULL(SUM(a.amount),0) AS amount
        FROM 
        ".$this->table." a
        RIGHT JOIN ".Config::$payment_category." b ON b.category_id=a.payment_id
        WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."'
        GROUP BY
        b.category_name
        ORDER BY amount DESC LIMIT ".Config::$statistics_max_range."";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_object()) {
                $visulize_array = array();
                $visulize_array['name'] = $row->name;
                $visulize_array['amount'] = $row->amount;
                $visulize[] = $visulize_array;
            }
            return $visulize;
        }
    }
    public function visulize_logged_user($toDate=null,$fromDate=null)
    {
        if($toDate) {
            $toDate=$this->mysqli->real_escape_string($toDate);
        }
        if($fromDate) {
            $fromDate=$this->mysqli->real_escape_string($fromDate);
        }
        $query="SELECT 
        IFNULL(CONCAT(b.user_firstname,' ',b.user_lastname),'General') AS name,
        IFNULL(SUM(a.amount),0) AS amount
        FROM 
         ".$this->table." a
        RIGHT JOIN ".Config::$application_users_attributes." b ON a.by_userid=b.user_id
        WHERE a.expense_date BETWEEN '".$toDate."' AND '".$fromDate."'
        GROUP BY
        name
        ORDER BY amount DESC LIMIT ".Config::$statistics_max_range."";
        if ($result = $this->mysqli->query($query)) {
            while ($row = $result->fetch_object()) {
                $visulize_array = array();
                $visulize_array['name'] = $row->name;
                $visulize_array['amount'] = $row->amount;
                $visulize[] = $visulize_array;
            }
            return $visulize;
        }
    }
	public function __destruct()
	{
		$this->mysqli->close();
	}
}
?>