<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <?php 
    expTheme::head(array(
    	"xhtml"=>true,
	    "css_primer"=>array(
    	    YUI2_PATH."reset-fonts-grids/reset-fonts-grids.css"),
    	"css_core"=>array("common"), 
    	"css_links"=>true,
    	"css_theme"=>true
        )
    );
    ?>
</head>
<body>
<!-- wrap starts here -->
<div id="wrap" class="fullbody">
	<!--header -->
	<div id="header">			
		<h1 id="logo-text"><a href="<?php echo URL_FULL; ?>index.php" title="<?php echo SITE_TITLE; ?>"><span class="green"><?php echo ORGANIZATION_NAME; ?></span> <sup></sup></a></h1>
		<p id="slogan"><?php echo SITE_HEADER; ?></p>		
			<div id="header-links">
				<a href="<?php echo exponent_core_makeLink(array('section'=>SITE_DEFAULT_SECTION)); ?>">Home</a> | 
				<a href="<?php echo exponent_core_makeLink(array('section'=>16)); ?>">Contact Us</a> | 
				<a href="<?php echo exponent_core_makeLink(array('section'=>10)); ?>">Site-map</a>
			</div>
		<div id="header-login">
			<?php expTheme::module(array("module"=>"login","view"=>"Expanded")); ?>
		</div>
	</div>
	<!-- navigation -->
	<div  id="menu">
		<?php expTheme::module(array("module"=>"navigation","view"=>"YUI Top Nav")); ?>
	</div>
	<!-- content-wrap starts here -->
	<div id="content-wrap">
		
		<div id="main">
			<?php expTheme::main(); ?>
		</div>
	<!-- content-wrap ends here -->	
	</div>
	<!--footer starts here-->
	<div id="footer">
		<?php expTheme::showController(array("controller"=>"text","action"=>"showall","view"=>"showall","source"=>"@footer")) ?>
	</div>	

	<?php echo expTheme::foot(); ?>
</div>

</body>
</html>
