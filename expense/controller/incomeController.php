<?php
class expenseController
{
	private $utils;
	private $page;
	private $limit;
	private $sidx;
	private $sord;
	private $searchOn;
	private $income;
    private $toDate;
    private $fromDate;
    private $userController;
	public function __construct($ref=null)
	{
		include_once ('../../global/class/config.php');
		include_once ('../../global/class/utils.php');
        include_once ('../../login/controller/userController.php');
		include_once ('../class/income.php');
		$this->utils=new Utils();
		$this->income=new Income();
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
			if( $fld=='income_id' || $fld=='income_date' || $fld=='category_name' || $fld=='amount' || $fld=='note' || $fld=='userid' || $fld=='userip' || $fld=='income_enterDate') 
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
        if(isset($_REQUEST['toDate']) && sizeof($_REQUEST['toDate'])>0) {
            $this->toDate = $this->utils->checkValues($_REQUEST['toDate']);
        }
        else {
            $this->toDate = null;
        }
        if(isset($_REQUEST['fromDate']) && sizeof($_REQUEST['fromDate'])>0) {
            $this->fromDate = $this->utils->checkValues($_REQUEST['fromDate']);
        }
        else {
            $this->fromDate = null;
        }
		$response=$this->income->getDetails($this->page,$this->limit,$this->sidx,$this->sord,$wh,$this->toDate,$this->fromDate);
		echo json_encode($response);
		unset($response);
	}
	public function operation()
	{
		$oper=$this->utils->checkValues($_REQUEST['oper']);
		/* ADD */
		if($oper=='add')
		{
			$response=$this->income->addDetails(
				$this->utils->checkValues($_POST['income_date']),
				$this->utils->checkValues($_POST['category_name']),
				$this->utils->checkValues($_POST['amount']),
				$this->utils->checkValues($_POST['note']),
                $this->utils->checkValues($_POST['for_username']),
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
			$response=$this->income->editDetails(
				$this->utils->checkValues($_POST['income_date']),
				$this->utils->checkValues($_POST['category_name']),
				$this->utils->checkValues($_POST['amount']),
				$this->utils->checkValues($_POST['note']),
				$this->utils->checkValues($_POST['id']),
                $this->utils->checkValues($_POST['for_username']),
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
			$response=$this->income->deleteDetails(
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
	$expenseController=new ExpenseController($_REQUEST['ref']);
}
?>