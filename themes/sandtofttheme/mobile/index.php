<!DOCTYPE HTML>
<html>
<head>
	<?php 
    expTheme::head(array(
    	"xhtml"=>false,
        "css_primer"=>array(
            YUI3_RELATIVE."cssreset/cssreset-min.css",
            YUI3_RELATIVE."cssfonts/cssfonts-min.css",
            YUI3_RELATIVE."cssgrids/cssgrids-min.css"
        ),
    	"css_core"=>array(
            "common"
        ),
    	"css_links"=>true,
    	"css_theme"=>true
        )
    );
	?>
	
	<?php require ("mobile_code.html"); ?>

	
</head>
<body>
	<div id="doc" class="orientation">
		<div id="hd">
		    <div id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
                
			</a>
			</div>
			
		</div>
	<div style="align:center;">
		<strong><?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Top Nav","source"=>"@top")); ?></strong>
	</div>		
	
		<div id="bd" class="yui3-g">
			<div id="centercol" class="yui3-u-1" style="width: 970px;">
		        <div class="content">
				    <?php expTheme::main(); ?>
    			</div>
			</div>
<!--			<div id="leftcol" class="yui3-u-1-4">
			    <div class="content"> -->
                    <?php // expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left")); ?>
<!--			    </div>
			</div>			-->	
			
		</div>
	</div>

	<?php expTheme::foot(); ?>
</body>
</html>
