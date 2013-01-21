<?php
	include_once('controller/pageController.php');
	$pageController=new PageController();
	echo $pageController->printHeader('expense_css');
?>
	<body>
    		<?php 
			echo $pageController->printNavigationHeader();
			echo $pageController->breadCrumb();
		?>
        <div id="content" class="expense_application_container">
                <!-- #LeftPane -->				
                <div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content left">						
                    <table id="west-grid"></table>
                </div> 		
                <!-- #LeftPane -->		
                <!-- #RightPane -->		
                <div id="RightPane" class="ui-layout-center ui-helper-reset ui-widget-content right" >
                    <!-- Tabs pane -->			
                    <div class="left" id="switcher"></div>			
                    <div class="clear"></div>			
                    <div id="tabs" class="jqgtabs">				
                        <ul>	
                            <li><a href="#tabs-1">Welcome</a></li>				
                        </ul>				
                        <div id="tabs-1">					
                            <h1>Pavan Ratnakar Expense Module</h1>					
                        </div>			
                    </div>
                </div>		
                <!-- #RightPane -->
        </div>
        <?php
			echo $pageController->printFooter();
            echo $pageController->printJS('expense_ie');
			echo $pageController->printJS('expense_js');
			echo $pageController->printGA();
		?>
	</body>
</html>