<?php
	include_once('controller/pageController.php');
	include_once('../applications/controller/applicationsController.php');
	$pageController=new PageController();
	$applicationsController=new applicationsController();
	echo $pageController->printHeader('applications_global_css');
?>
	<body>
		<?php 
			echo $pageController->printNavigationHeader();
			echo $pageController->breadCrumb();
		?>
		<div id="content">
			<div class="container">
				<h1 class="slogan">Pavan Ratnakar Applications</h1>
				<div class="section-title">
					<h6><span class="icon pen"></span>Welcome</h6>
				</div>
				<div class="three-fourth">
					<h4>How does it work?</h4>
					<p>What you are viewing is common login for all my Applications.</p>
					<div class="process">
						<span class="first">&nbsp;</span>
						<span class="">Register</span>
						<span class="">Authenticate</span>
						<span class="active">Login</span>
						<span>View Applications</span>
						<span class="last">Use Application</span>
						<div class="clear"></div>
					</div>
				</div>
				<div class="one-fourth last">
					<h4>Login</h4>
					<?php 
						echo $pageController->printLoginModule();
					?>
				</div>
			</div>
			<div class="clear"></div>
			<div class="container">
				<div class="section-title">
					<h6><span class="icon pages"></span>Recent Updates</h6>
				</div>
				<div id="recentUpdates" class="jcarousel-container">
					<?php echo $pageController->printBlogPosts($applicationsController->listApplications()); ?>
				</div>
			</div>
		</div>
		<?php
			echo $pageController->printFooter();;
			echo $pageController->printJS('applications_login_js');
			echo $pageController->printGA();
		?>
	</body>
</html>