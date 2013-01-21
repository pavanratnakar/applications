<?php
class PageControllerBase
{
	protected $page;
	protected $ajax;
	protected $path;
	protected $utils;
    protected $userController;
	public function init()
	{
		$this->checkIfAjax();
		include_once($this->path.'../min/utils.php');
		include_once($this->path.'global/class/utils.php');
		include_once($this->path.'login/controller/userController.php');
        $this->userController=new UserController();
        $this->utils=new Utils();
	}
	public function printHeader($name)
	{
		return $this->page->printHeader($name);
	}
	public function printNavigationHeader()
	{
		return $this->page->printNavigationHeader($this->userController->getFullName());
	}
	public function breadCrumb()
	{
		return $this->page->breadCrumb();
	}
	public function printGA()
	{
		return $this->page->printGA();
	}
	public function printJS($name)
	{
		return $this->page->printJS($name);
	}
	public function printGoogleAd()
	{
		return $this->page->printGoogleAd();
	}
	public function printFooter()
	{
		return $this->page->printFooter();
	}
	public function printBlogPosts($list)
	{
		return $this->page->printBlogPosts($list);
	}
	public function checkIfAJAX()
	{
		if(!file_exists('../global/class/utils.php')) 
		{	
			$this->ajax=TRUE;
			$this->path='../../';
		}
		else
		{	
			$this->ajax=FALSE;
			$this->path='../';
		}
	}
	public function checkUserStatus()
	{
		return $this->userController->checkUserStatus();
	}
}
?>