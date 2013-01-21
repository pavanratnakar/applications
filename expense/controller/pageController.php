<?php
include_once('../global/controller/pageController.php');
class PageController extends PageControllerBase
{
	public function __construct()
	{
		$this->init();
        if(!$this->checkUserStatus())
		{
			header( 'Location: http://'.$_SERVER['SERVER_NAME'].'/applications/login' ) ;
		}
		include_once('class/page.php');
		$this->page=new Page();
	}
}
?>