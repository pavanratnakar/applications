<?php
class loanController
{
	private $utils;
	private $page;
	private $limit;
	private $sidx;
	private $sord;
	private $searchOn;
	private $loan;
    private $userController;
	public function __construct($ref=null)
	{
		include_once ('../../global/class/config.php');
		include_once ('../../global/class/utils.php');
        include_once ('../../login/controller/userController.php');
		include_once ('../class/loan.php');
		$this->utils=new Utils();
		$this->loan=new Loan();
        $this->userController=new UserController();
		$this->page = $this->utils->checkValues($_REQUEST['page']); // get the requested page
		$this->limit = $this->utils->checkValues($_REQUEST['rows']); // get how many rows we want to have into the grid
		$this->sidx = $this->utils->checkValues($_REQUEST['sidx']); // get index row - i.e. user click to sort
		$this->sord = $this->utils->checkValues( $_REQUEST['sord']); // get the direction
		$this->searchOn = $this->utils->checkValues($_REQUEST['_search']); // search
		$this->$ref();
	}
	public function details()
	{
		if(isset($_REQUEST['_search']) && $this->searchOn!='false')
		{
			$fld =  $this->utils->checkValues($_REQUEST['searchField']);
			if( $fld=='load_id' || $fld=='loan_start_date' || $fld=='loan_end_date' || $fld=='loan_amount' || $fld=='loan_paid_amount' || $fld=='monthly_installment' || $fld=='note') 
			{
				$fldata =  $this->utils->checkValues($_REQUEST['searchString']);
				$foper =  $this->utils->checkValues($_REQUEST['searchOper']);
				// costruct where
				$wh .= " AND ".$fld;
				switch ($foper) 
				{
					case "bw":
						$fldata .= "%";
						$wh .= " LIKE '".$fldata."'";
						break;
					case "eq":
						if(is_numeric($fldata)) 
						{
							$wh .= " = ".$fldata;
						}
						else 
						{
							$wh .= " = '".$fldata."'";
						}
						break;
					case "ne":
						if(is_numeric($fldata))
						{
							$wh .= " <> ".$fldata;
						}
						else
						{
							$wh .= " <> '".$fldata."'";
						}
						break;
					case "lt":
						if(is_numeric($fldata)) 
						{
							$wh .= " < ".$fldata;
						}
						else
						{
							$wh .= " < '".$fldata."'";
						}
						break;
					case "le":
						if(is_numeric($fldata)) 
						{
							$wh .= " <= ".$fldata;
						} 
						else 
						{
							$wh .= " <= '".$fldata."'";
						}
						break;
					case "gt":
						if(is_numeric($fldata)) 
						{
							$wh .= " > ".$fldata;
						} else 
						{
							$wh .= " > '".$fldata."'";
						}
						break;
					case "ge":
						if(is_numeric($fldata)) 
						{
							$wh .= " >= ".$fldata;
						}
						else 
						{
							$wh .= " >= '".$fldata."'";
						}
						break;
					case "ew":
						$wh .= " LIKE '%".$fldata."'";
						break;
					case "cn":
						$wh .= " LIKE '%".$fldata."%'";
						break;
					default :
						$wh = "";
				}
			}
		}
		else
		{
			if(!$this->sidx) 
			{
				$this->sidx =1;
			}
			$totalrows = isset($_REQUEST['totalrows']) ? $this->utils->checkValues($_REQUEST['totalrows']): false;
			if($totalrows) 
			{	
				$this->limit = $totalrows;
			}
			$wh="";
		}
		$response=$this->loan->getDetails($this->page,$this->limit,$this->sidx,$this->sord,$wh);
		echo json_encode($response);
		unset($response);
	}
	public function operation()
	{
		$oper=$this->utils->checkValues($_REQUEST['oper']);
		/* ADD */
		if($oper=='add')
		{
			$response=$this->loan->addDetails(
				$this->utils->checkValues($_POST['loan_start_date']),
				$this->utils->checkValues($_POST['loan_end_date']),
				$this->utils->checkValues($_POST['loan_amount']),
				$this->utils->checkValues($_POST['loan_paid_amount']),
                $this->utils->checkValues($_POST['monthly_installment']),
                $this->utils->checkValues($_POST['note']),
                $this->userController->checkUserStatus()
				);
			if($response)
			{
				$status=TRUE;
				$message="Details Added";
			}
			else
			{
				$status=FALSE;
				$message="Details could not be added";
			}
		}
		/* ADD */
		/* EDIT */
		else if($oper=='edit')
		{
			$response=$this->loan->editDetails(
				$this->utils->checkValues($_POST['loan_start_date']),
				$this->utils->checkValues($_POST['loan_end_date']),
				$this->utils->checkValues($_POST['loan_amount']),
				$this->utils->checkValues($_POST['loan_paid_amount']),
				$this->utils->checkValues($_POST['monthly_installment']),
                $this->utils->checkValues($_POST['note']),
                $this->utils->checkValues($_POST['id']),
                $this->userController->checkUserStatus()
                );
			if($response)
			{
				$status=TRUE;
				$message="Details Edited";
			}
			else
			{
				$status=FALSE;
                $message="Sorry ! Details could not be edited. You dont have the right permissions to edit the following record.";
			}
		}
		/* EDIT */
		/* DELETE */
		else if($oper=='del')
		{
			$response=$this->loan->deleteDetails(
                $this->utils->checkValues($_POST['id']),
                $this->userController->checkUserStatus()
            );
			if($response)
			{
				$status=TRUE;
				$message="Details Deleted";
			}
			else
			{
				$status=FALSE;
				$message="Sorry ! Details could not be deleted. You dont have the right permissions to delete.";
			}
		}
		/* DELETE */
		$returnArray= array(
			"status" => $status,
			"message" => $message
		);
		$response = $_POST["jsoncallback"] . "(" . json_encode($returnArray) . ")";
		echo $response;
		unset($response);
	}
}
if(isset($_REQUEST['ref']))
{
	$loanController=new LoanController($_REQUEST['ref']);
}
?>