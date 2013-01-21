<?php
class Loan
{
	private $mysqli;
	private $utils;
	private $table;
	private $id='loan_id';
	public function __construct()
	{
		$this->mysqli=new mysqli(Config::$db_server,Config::$db_username,Config::$db_password,Config::$db_database);
		$this->utils=new Utils();
		$this->table=Config::$loan_main;
	}
	public function getDetails($page,$limit,$sidx,$sord,$wh="")
	{
		$page=$this->mysqli->real_escape_string($page);
		$limit=$this->mysqli->real_escape_string($limit);
		$sidx=$this->mysqli->real_escape_string($sidx);
		$sord=$this->mysqli->real_escape_string($sord);
		if ($result = $this->mysqli->query("SELECT COUNT(*) AS count FROM ".$this->table." WHERE delete_flag=0 ".$wh)) 
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
                $query="SELECT loan_id, loan_start_date, loan_end_date , loan_amount , loan_paid_amount , monthly_installment , note 
                    FROM 
                    ".$this->table." 
                    WHERE delete_flag=0  
                    ORDER BY ".$sidx." ". $sord." LIMIT ".$start." , ".$limit;
				if ($result1 = $this->mysqli->query($query)) {
					$responce->page = $page;
					$responce->total = $total_pages;
					$responce->records = $count;
					$i=0;
                    $loan_amount=0;
					while ($row1 = $result1->fetch_object()) {
						$responce->rows[$i]['loan_id']=$row1->expense_id;
                        $loan_amount=$loan_amount+$row1->loan_amount;
                        $loan_paid_amount=$loan_paid_amount+$row1->loan_paid_amount;
                        $monthly_installment=$monthly_installment+$row1->monthly_installment;
                        $loan_remaining_amount= $row1->loan_amount - $row1->loan_paid_amount;
                        $loan_remaining_amount_total = $loan_remaining_amount + $loan_remaining_amount_total;
						$responce->rows[$i]['cell']=array(
							$row1->loan_id,
							$row1->loan_start_date,
                            $row1->loan_end_date,
							$row1->loan_amount,
							$row1->loan_paid_amount,
                            $row1->monthly_installment,
                            $loan_remaining_amount,
                            $row1->note
						);
						$i++;
					}
                    $responce->userdata['loan_amount'] = $loan_amount;
                    $responce->userdata['loan_paid_amount'] = $loan_paid_amount;
                    $responce->userdata['monthly_installment'] = $monthly_installment;
                    $responce->userdata['loan_remaining_amount'] = $loan_remaining_amount_total;
					return $responce;
				}
			}
		}
	}
	public function addDetails($loan_start_date,$loan_end_date,$loan_amount,$loan_paid_amount,$monthly_installment,$note,$by_userid)
	{
    	$loan_start_date=$this->mysqli->real_escape_string($loan_start_date);
		$loan_end_date=$this->mysqli->real_escape_string($loan_end_date);
		$loan_amount=$this->mysqli->real_escape_string($loan_amount);
		$loan_paid_amount=$this->mysqli->real_escape_string($loan_paid_amount);
        $monthly_installment=$this->mysqli->real_escape_string($monthly_installment);
        $note=$this->mysqli->real_escape_string($note);
		if ($result = $this->mysqli->query("INSERT INTO ".$this->table."(loan_start_date,loan_end_date,loan_amount,loan_paid_amount,monthly_installment,note,by_userid) VALUES('$loan_start_date','$loan_end_date','$loan_amount','$loan_paid_amount','$monthly_installment','$note','$by_userid')")) {
			if($this->mysqli->affected_rows>0) {
				return TRUE;
			}
		}
		return FALSE;
	}
	public function editDetails($loan_start_date,$loan_end_date,$loan_amount,$loan_paid_amount,$monthly_installment,$note,$id,$by_userid)
	{
    	$loan_start_date=$this->mysqli->real_escape_string($loan_start_date);
		$loan_end_date=$this->mysqli->real_escape_string($loan_end_date);
		$loan_amount=$this->mysqli->real_escape_string($loan_amount);
		$loan_paid_amount=$this->mysqli->real_escape_string($loan_paid_amount);
        $monthly_installment=$this->mysqli->real_escape_string($monthly_installment);
        $note=$this->mysqli->real_escape_string($note);
		$id=$this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("UPDATE ".$this->table." SET loan_start_date='$loan_start_date',loan_end_date='$loan_end_date',loan_amount='$loan_amount',loan_paid_amount='$loan_paid_amount',monthly_installment='$monthly_installment',note='$note' WHERE loan_id='$id' AND by_userid='$by_userid'")) {
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