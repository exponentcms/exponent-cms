<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <?php 
    expTheme::head(array(
    	"xhtml"=>true,
	    "css_primer"=>array(
    	    YUI2_PATH."yui2-reset-fonts-grids/yui2-reset-fonts-grids.css"),
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
		<h1 id="logo-text"><a href="<?php echo URL_FULL; ?>index.php" title="<?php echo SITE_TITLE; ?>"><span class="green"><?php echo ORGANIZATION_NAME; ?></span> <sup></sup></a></h1>
		<p id="slogan"><?php echo SITE_HEADER; ?></p>		
		<div id="header-links">
			<a href="<?php echo expCore::makeLink(array('section'=>SITE_DEFAULT_SECTION)); ?>">Home</a> |
			<a href="<?php echo expCore::makeLink(array('section'=>16)); ?>">Contact Us</a> |
			<a href="<?php echo expCore::makeLink(array('section'=>10)); ?>">Site-map</a>
		</div>
		<div id="header-login">
			<?php expTheme::showController(array("controller"=>"login","action"=>"showlogin","view"=>"showlogin_expanded")); ?>
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
		<div id="sidebar">
    	    <?php expTheme::module(array("module"=>"container","view"=>"Default","source"=>"@left")); ?>
        </div>
	<!-- content-wrap ends here -->	
	</div>
	<!--footer starts here-->
	<div id="footer">
	    <?php expTheme::showController(array("controller"=>"text","action"=>"showall","view"=>"showall","source"=>"@footer")) ?>
	</div>	
</div>
<?php echo expTheme::foot(); ?>
</body>
</html>
