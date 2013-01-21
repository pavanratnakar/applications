<?php
if(file_exists('../global/class/page.php'))
{
    include_once('../global/class/page.php');
}
else
{
    include_once('../../global/class/page.php');
}
class Page extends PageBase
{
	public function __construct()
	{
		$this->title='Pavan Ratnakar Applications | Expense Module';
		$this->description='Pavan Ratnakar Applications Expense Module';
		$this->keywords='Pavan Ratnakar, Login, Applications, Pavan Ratnakar Applications, Expense';
        $this->breadcrum=array(
            'Home'=>array(
                'link'=>TRUE,
                'href'=>'http://'.$_SERVER['SERVER_NAME'].'/applications/applications',
             ),                
            'Expense'=>array(
                'link'=>FALSE,
                'href'=>'http://'.$_SERVER['SERVER_NAME'].'/applications/expense'
             )
        );
	}
	public function printSubModules($subModule,$firstDate=null,$lastDate=null)
	{
        $return='';
		$return.='<div class="optionContainer left">';
        if($firstDate)
        {
            $return.='
            <div id="'.$subModule.'_date_container" class="left date_container">
                <form id="'.$subModule.'_date_form" method="post" action="">	
                    To: <input type="text" class="toDate datePicker" value="'.$firstDate.'"/>
                    From: <input type="text" class="fromDate datePicker" value="'.$lastDate.'"/>
                    <input type="submit" value="Submit"/>
                </form>
            </div>';
        }
        $return.='<div id="'.$subModule.'_select_container" class="right select_container"></div>
        </div>
        <div class="clear"></div>
		<table id="'.$subModule.'"></table>
		<div id="p_'.$subModule.'"></div>
		<script type="text/javascript">
			pavan_expense.jqgrid.'.$subModule.'.init();
		</script>';
		return $return;
	}
    public function printVisulize($subModule,$firstDate=null,$lastDate=null)
    {
        $return='';
        $return.='<div id="visualize_'.$subModule.'">
        <div class="optionContainer left">';
        if($firstDate)
        {
            $return.='
            <div id="visualize_'.$subModule.'_date_container" class="left date_container">
                <form id="visualize_'.$subModule.'_date_form" method="post" action="">	
                    To: <input type="text" class="toDate datePicker" value="'.$firstDate.'"/>
                    From: <input type="text" class="fromDate datePicker" value="'.$lastDate.'"/>
                    <input type="submit" value="Submit"/>
                </form>
            </div>';
        }
        $return.='<div id="visualize_'.$subModule.'_select_container" class="right select_container"></div>
        </div><div class="clear"></div>';
		$return.='<table id="visualize_'.$subModule.'_table" class="hide">';
        $return.='<caption></caption>';
        $return.='<thead><tr><td></td></tr></thead>';
        $return.='<tbody></tbody>';
        $return.='</table></div>';
         $return.='<script type="text/javascript">
			pavan_expense.visulize.'.$subModule.'.init();
		</script>';
        return $return;
    }
}
?>