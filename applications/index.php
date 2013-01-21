<?php
	include_once('controller/pageController.php');
	include_once('controller/applicationsController.php');
	$pageController=new PageController();
	$applicationsController=new ApplicationsController();
	echo $pageController->printHeader('applications_global_css');
?>
	<body>
		<?php 
			echo $pageController->printNavigationHeader();
			echo $pageController->breadCrumb();
		?>
		<div id="content">
			<div class="container" id="blog">
				<div class="page-header clearfix">
					<h1 class="page-title">Your Applications</h1>
					<div class="search" id="search-form">
						<input type="text" placeholder="Search for applications" value="" class="placeholder">
						<input type="submit" value="Go">
					</div>
				</div>
				<?php echo $pageController->printBlogPosts($applicationsController->listApplications($pageController->checkUserStatus())); ?>
				<hr/>
				<?php
					echo $pageController->printPagination(sizeof($applicationsController->listApplications($pageController->checkUserStatus())));
				?>
			</div><!-- end #Applications -->
		</div>		
		<?php
			echo $pageController->printFooter();
			echo $pageController->printJS('applications_applications_js');
			echo $pageController->printGA();
		?>
	</body>
</html>