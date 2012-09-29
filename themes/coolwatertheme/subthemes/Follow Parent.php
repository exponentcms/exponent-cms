<!DOCTYPE HTML>
<html>
	<head>
	    <?php
	    expTheme::head(array(
	        "xhtml"=>false,
		    "css_primer"=>array(
                YUI2_RELATIVE."yui2-reset-fonts-grids/yui2-reset-fonts-grids.css"),
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
                <h1 id="logo-text"><a href="<?php echo URL_FULL; ?>index.php" title="<?php echo SITE_TITLE; ?>"><span class="green"><?php if(LOGO_TEXT_MAIN=='') echo ORGANIZATION_NAME; else echo LOGO_TEXT_MAIN; ?></span> <sup><?php echo LOGO_TEXT_SUPERSCRIPT; ?></sup></a></h1>
				<p id="slogan"><?php echo SITE_HEADER; ?></p>
				<div id="header-links">
                    <a href="<?php echo expCore::makeLink(array('section'=>(LINK1_SECTION!=''?LINK1_SECTION:SITE_DEFAULT_SECTION))); ?>"><?php if(LINK1_TEXT!='') echo LINK1_TEXT; ?></a>
                    <?php if(LINK1_TEXT!='' && LINK2_TEXT!='') echo ' | '; ?>
                    <a href="<?php echo expCore::makeLink(array('section'=>(LINK2_SECTION!=''?LINK2_SECTION:16))); ?>"><?php if(LINK2_TEXT!='') echo LINK2_TEXT; ?></a>
                    <?php if(LINK2_TEXT!='' && LINK3_TEXT!='') echo ' | '; ?>
                    <?php if(LINK2_TEXT=='' && (LINK1_TEXT!='' && LINK3_TEXT!='')) echo ' | '; ?>
                    <a href="<?php echo expCore::makeLink(array('section'=>(LINK3_SECTION!=''?LINK3_SECTION:10))); ?>"><?php if(LINK3_TEXT!='') echo LINK3_TEXT; ?></a>
				</div>
				<div id="header-login">
					<?php expTheme::module(array("controller"=>"login","action"=>"showlogin","view"=>"showlogin_expanded")); ?>
				</div>
			</div>
			<!-- navigation -->
			<div id="menu">
                <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_YUI Top Nav")); ?>
			</div>
			<!-- content-wrap starts here -->
			<div id="content-wrap">
				<div id="main">
		            <?php expTheme::main(); ?>
				</div>
				<div id="sidebar">
		            <?php expTheme::module(array("module"=>"container","view"=>"Default","source"=>"@left","scope"=>"top-sectional")); ?>
		        </div>
			<!-- content-wrap ends here -->
			</div>
			<!--footer starts here-->
			<div id="footer">
			    <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall","source"=>"@footer")) ?>
			</div>
		</div>
		<?php expTheme::foot(); ?>
	</body>
</html>
