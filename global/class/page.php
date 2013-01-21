<?php
class PageBase
{
	protected $title;
	protected $description;
	protected $keywords;
    protected $breadcrum;
	public function printNavigationHeader($fullName)
	{
		$return='
		<div id="header">
			<div class="container">
				<div id="logo">
					<a id="logo" title="Pavan Ratnakar" href="http://www.pavanratnakar.com">Pavan Ratnakar</a>
				</div>';
        $return.=$this->printNav($fullName);
		$return.='<div class="clear"></div>
			</div>
		</div>';
		return $return;
	}
    public function printNav($fullName)
    {
        $return=
        '<ul id="nav">
			<li class="active"><a href="">Applications</a></li>';
			/*<li><a href="#">Drops</a>
				<ul style="width: auto;">
					<li><a href="#">This is an example</a></li>
					<li><a href="#">Of a simple</a></li>
					<li><a href="#">Dropdown menu</a></li>
				</ul>
			</li>*/
		$return.='<li><a href="javascript:void(0);">About</a></li>
			<li><a href="javascript:void(0);">Contact Me</a></li>';
        if($fullName)
        {
            $return.=$this->printUserOptions($fullName);
        }
		$return.='</ul>';
        return $return;
    }
    public function printUserOptions($fullName)
    {
        $return='
            <li>
                <a href="javascript:void(0);" title="'.$fullName.'">'.$fullName.'</a>
                <ul>
                    <li style="border:none" id="user_logout"><a href="javascript:void(0);" title="Logout">Logout</a></li>
                </ul>
            </li>';
        return $return;
    }
	public function printHeader($name)
	{
		$return='
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>';
		$return.=$this->printMeta();
		$return.=$this->printCss($name);
		$return.='</head>';
		return $return;
	}
	public function printMeta()
	{
		$return='
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<!-- No Cache -->
		<meta http-equiv="PRAGMA" content="NO-CACHE"/>
		<meta http-equiv="Expires" content="Mon, 04 Dec 1999 21:29:02 GMT"/>
		<!-- End No Cache -->		
		<title>'.$this->title.'</title>
		<!-- SEO -->
		<meta http-equiv="Content-Language" content="en" />
		<meta name="description" content="'.$this->description.'" />
		<meta name="keywords" content="'.$this->keywords.'" />
		<meta name="author" content="Pavan Ratnakar" />
		<meta name="robots" content="index,follow" />
		<meta name="revisit-after" content="2 days" />
		<meta name="googlebot" content="index, follow, archive" />
		<meta name="msnbot" content="index, follow" />
		<!-- SEO -->';
		return $return;
	}
	public function printCss($name)
	{
		$return='
		<!-- CSS -->
		<link type="text/css" rel="stylesheet" media="screen" href="'.Minify_getUri($name).'"/>
		<!-- CSS -->';
		return $return;
	}
	public function printGA()
	{
		$return=
		'<script type="text/javascript">		
		///GOOGLE ANALYTICS CODE///		
		var _gaq = _gaq || [];		
		_gaq.push(["_setAccount", "UA-22528464-1"]);		
		_gaq.push(["_setDomainName", ".pavanratnakar.com"]);		
		_gaq.push(["_trackPageview"]);		
		(function() 
		{			
			var ga = document.createElement("script"); 
			ga.type = "text/javascript"; 
			ga.async = true;			
			ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";			
			var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);		
		})();		
		///GOOGLE ANALYTICS CODE///		
		</script>';
		return $return;
	}
	public function printJS($name)
	{
		$return='<script type="text/javascript" src="'.Minify_getUri($name).'&debug=1"></script>';
		return $return;
	}
	public function printGoogleAd()
	{
		$return='
		<script type="text/javascript">
			<!--
			google_ad_client = "ca-pub-7513206858623669";
			/* Games */
			google_ad_slot = "0772185248";
			google_ad_width = 728;
			google_ad_height = 90;
			//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>';
		return $return;
	}
	public function breadCrumb()
	{
		$return.='<div id="breadcrumbs">
			<div class="container">
            <ul class="breadcrumbs">';
            $i=0;
            foreach ($this->breadcrum as $key => $value) {
                if($i==0) {
                    $class="class='home'";
                }
                else {
                    $class="";
                }
                if($i>0)
                {
                     $return.='<li>-></li>';
                }
                if($value['link']) {
                    $href=$value['href'];
                }
                else
                {
                    $href='javascript:void(0);';
                }
                $return.='<li '.$class.'><a title="'.$key.'" href="'.$href.'">'.$key.'</a></li>';
                $i++;
			}
			$return.='</ul></div></div>';
		return $return;
	}
	public function printFooter()
	{
		$return='
        <div id="bottonAdContainer" class="container" align="center">
            '.$this->printGoogleAd().'
        </div>
		<div id="footer">
			<div class="container">
				<p class="align-left">&copy; '.date("Y").'. All right reserved. Developed by <a href="http://www.pavanratnakar.com" title="Pavan Ratnakar">Pavan Ratnakar</a>.</p>
				<ul class="social-links align-right">
					<li class="twitter"><a title="Twitter" href="http://twitter.com/#!/pavanratnakar">Twitter</a></li>
					<li class="facebook"><a title="Facebook" href="http://www.facebook.com/pavan.ratnakar">Facebook</a></li>
					<li class="google-buzz"><a title="google-buzz" href="https://plus.google.com/105657677244847005118/">google-buzz</a></li>
					<li class="youtube"><a title="youtube" href="http://www.youtube.com/user/pavanratnakar">You Tube</a></li>
				</ul>
			</div><!-- end .container -->
		</div>';
		return $return;
	}
	public function printBlogPosts($list)
	{
		$return='';
		for($i=0;$i<sizeof($list);$i=$i+3)
		{
			$temp=array_slice($list,$i,$i+3);
			$return.='<ul class="blog-posts">';
			foreach ($temp as $key => $value)
			{
				$return.='<li>';
				$return.='<h4>'.$value['title'].'</h4>';
				$return.='<p class="meta">Created on '.$value['date'].'</p>';
				$return.='<p">'.$value['description'].'</p>';
				if($value['status'] && $value['link'])
				{
					$return.='<p><a title="Click to use the application" href="'.$value['link'].'" class="read-more">Click to use the application</a></p>';
				}
				$return.='</li>';
			}
			$return.='</ul>';
		}
		return $return;
	}
}
?>