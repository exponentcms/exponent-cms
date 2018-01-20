<!DOCTYPE HTML>
<html>
<head>
	<?php 
    expTheme::head(array(
//    	"xhtml"=>false,
        "css_primer"=>array(
            YUI3_RELATIVE."cssreset/cssreset-min.css",
            YUI3_RELATIVE."cssfonts/cssfonts-min.css",
            YUI3_RELATIVE."cssgrids/cssgrids-min.css"
        ),
    	"css_core"=>array(
            "common"
        ),
//    	"css_links"=>true,
//    	"css_theme"=>true
        )
    );
	?>
<!--    <link href='http://fonts.googleapis.com/css?family=Verdana' rel='stylesheet' type='text/css'>  -->

	<!--[if IE]>
	<link href='<?php echo THEME_RELATIVE?>css/ie_fixes/ie_fixes.css' rel="stylesheet" type="text/css" />
	<![endif]-->	
	
</head>
<body>
	<div id="doc">
		<div id="hd">
		    <div id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">&nbsp;
            <!--   <?php echo ORGANIZATION_NAME; ?>  -->
			</a>
			</div>
            <?php //expTheme::module(array("controller"=>"login","action"=>"showlogin","view"=>"showlogin_flyoutYUI")); ?>
            <?php //expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
            <?php //expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall_flyoutsidebar","chrome"=>true,"source"=>"flyout")); ?>
            <?php //expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_YUI Top Nav")); ?>
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_mega","source"=>"mega","chrome"=>true)); ?>
			<?php expTheme::module(array("controller"=>"search","action"=>"show")) ?>
			<?php //expTheme::module(array("controller"=>"links","action"=>"showall","view"=>"showall_quicklinks")) ?>
			<?php expTheme::module(array("controller"=>"navigation","action"=>"breadcrumb","view"=>"Breadcrumb")); ?>
		</div>
		<div id="bd" class="yui3-g">
			
			
			<div id="centercol" class="yui3-u-3-4">
		        <div class="content">
				
        <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@page","scope"=>"sectional")); ?>	
		       
        				<?php require ("events_menu.html"); ?><br />
						
				    <?php expTheme::main(); ?><?php include ("eucookie.php"); ?>
					
    			</div>
			</div>
			
			<div id="leftcol" class="yui3-u-1-4">
			    <div class="content">
                   <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left")); ?> 
				   
			    </div>
			</div>			
			
			<div class="clear"></div>
		</div>
		<div id="ft">
            <?php include ("imbeds/copyright.inc"); ?>
		</div>
	</div>
<?php expTheme::foot(); ?>
<?php include ("imbeds/analytics.inc"); ?>
</body>
</html>
    <script type="text/javascript" language="javascript" src="<?php echo THEME_RELATIVE?>js/general.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo THEME_RELATIVE?>js/jquery.js"></script>
    <script type="text/javascript" language="javascript" src="<?php echo THEME_RELATIVE?>js/thickbox.js"></script>