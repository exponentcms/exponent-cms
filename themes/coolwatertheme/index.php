<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    	
    <?php 
    expTheme::head(array(
    	"xhtml"=>true,
    	"css_primer"=>false,
    	"css_core"=>array("common"), 
    	"css_links"=>true,
    	"css_theme"=>true
        )
    );
    ?>
</head>
<body>
<!-- wrap starts here -->
<div id="wrap">
	<!--header -->
	<div id="header">			
		<h1 id="logo-text"><a href="<?php echo URL_FULL; ?>index.php">ex<span class="green">ponent</span> <sup>CMS</sup></a></h1>		
		<p id="slogan">The "coolwater" theme from Styleshout.com</p>		
		<div id="header-links">
			<a href="<?php echo exponent_core_makeLink(array('section'=>SITE_DEFAULT_SECTION)); ?>">Home</a> | 
			<a href="<?php echo exponent_core_makeLink(array('section'=>16)); ?>">Contact Us</a> | 
			<a href="<?php echo exponent_core_makeLink(array('section'=>10)); ?>">Site-map</a>
		</div>
		<div id="header-login">
			<?php exponent_theme_showModule("loginmodule","Expanded"); ?>
		</div>
	</div>
	<!-- navigation -->
	<div  id="menu">
		<?php exponent_theme_showModule("navigationmodule","YUI Top Nav"); ?>
	</div>
	<!-- content-wrap starts here -->
	<div id="content-wrap">
		<div id="main">
			<?php exponent_theme_main(); ?>
		</div>
		<div id="sidebar">
			<?php exponent_theme_showModule("containermodule","Default","","@left"); ?>			
		</div>
	<!-- content-wrap ends here -->	
	</div>
	<!--footer starts here-->
	<div id="footer">
	    <?php expTheme::showController(array("controller"=>"text","action"=>"showall","view"=>"showall","source"=>"textmodulesrc1")) ?>
	</div>	
</div>
	<?php echo exponent_theme_footerInfo(); ?>
</body>
</html>
