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
<?php require ("mobile_code.html"); ?>	
</head>
<body>
	<div id="doc" class="orientation">
		<div id="hd">
		    <div id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
            
			</a>
			</div></div>
 		<div style="align:center;"><strong><?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Top Nav","source"=>"@top")); ?></strong></div>
		
		<div id="bd" class="yui3-g">
			
			
			<div id="centercol" class="yui3-u-3-4">
		        <div class="content">
				<?php require ("events_menu.html"); ?><br />
        <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@page","scope"=>"sectional")); ?>

						
        				<p class="event-return"><strong>After viewing an event, <a href="http://www.sandtoft.org/events">click here</a> to return to the full list.</strong></p>
												
				    <?php expTheme::main(); ?><?php include ("eucookie.php"); ?>
					
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