<?php
include_once('../../global/controller/pageController.php');
class PageController extends PageControllerBase
{
	public function __construct($subModule=null)
	{
		$this->init();
		include_once('../class/page.php');
		$this->page=new Page();
	}
	public function printSubModules($subModule)
	{
		$subModule=$this->utils->checkValues($subModule);
        if(isset($_REQUEST['date'])) {
            echo $this->page->printSubModules($subModule,$this->utils->firstOfMonth(),$this->utils->lastOfMonth());
        }
        else {
            echo $this->page->printSubModules($subModule);
        }
	}
   public function printVisulize($subModule)
	{
		$subModule=$this->utils->checkValues($subModule);
        if(isset($_REQUEST['date'])) {
            echo $this->page->printVisulize($subModule,$this->utils->firstOfMonth(),$this->utils->lastOfMonth());
        }
        else {
            echo $this->page->printVisulize($subModule);
        }
	}
}
if(isset($_REQUEST['submodule']))
{
	$pageController=new PageController();
	$pageController->printSubModules($_REQUEST['submodule']);
}
if(isset($_REQUEST['visualize']))
{
	$pageController=new PageController();
	$pageController->printVisulize($_REQUEST['visualize']);
}
?>