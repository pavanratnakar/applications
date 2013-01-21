<?php
include_once('../global/class/page.php');
class Page extends PageBase
{
	public function __construct()
	{
		$this->title='Pavan Ratnakar Applications | Login Module';
		$this->description='Pavan Ratnakar Applications Global Login';
		$this->keywords='Pavan Ratnakar, Login, Applications, Pavan Ratnakar Applications';
        $this->breadcrum=array(
            'Login'=>array(
                'link'=>FALSE,
                'href'=>'http://'.$_SERVER['SERVER_NAME'].'/applications/login',
             )              
         );
	}
	public function printLoginModule()
	{
		$return='
		<form class="form" id="login-form" method="post" action="">	
			<p id="response"></p>
			<p class="input-block">
				<label for="login_user">Email*</label>
				<input type="text" id="login_user" name="login_user" size="25" value="">
			</p>
			<p class="textarea-block">
				<label for="login_password">Password*</label>
				<input type="password" value="" id="login_password" size="25 name="login_password">
			</p>
			<div class="clear"></div>
			<input type="submit" id="login-submit" value="Login" name="submit">
		</form>';
		return $return;
	}
}
?>