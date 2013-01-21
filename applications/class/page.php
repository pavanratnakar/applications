<?php
include_once('../global/class/page.php');
class Page extends PageBase
{
	public function __construct()
	{
		$this->title='Pavan Ratnakar Applications | Main Module';
		$this->description='Pavan Ratnakar Applications Main Module';
		$this->keywords='Pavan Ratnakar, Login, Applications, Pavan Ratnakar Applications';
        $this->breadcrum=array(
            'Home'=>array(
                'link'=>FALSE,
                'href'=>'http://'.$_SERVER['SERVER_NAME'].'/applications/applications',
             )              
         );
	}
	public function printPagination($length)
	{
		if($size<6)
		{
			$prev='class="disabled"';
			$next='class="disabled"';
			$size=1;
		}
		$return='
		<div class="center-pagination">
			<ul class="pagination">
				<li class="prev"><span '.$prev.'>«</span></li>
				<li>
					<span>
					<em>page</em>
					1
					<em>of</em>
					'.$size.'
					</span>
				</li>
				<li class="next"><span '.$next.'>»</span></li>					
			</ul>
		</div>';
		return $return;
	}
}
?>