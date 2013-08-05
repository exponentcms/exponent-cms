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
    <link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="doc">
		<div id="hd">
		    <div id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
                <?php echo ORGANIZATION_NAME; ?>
			</a>
			</div>
			<?php expTheme::module(array("controller"=>"search","action"=>"show","src"=>"@top")) ?>
		</div>
		<div id="bd" class="yui3-g">
			<div id="centercol" class="yui3-u-1">
		        <div class="content">
				    <?php expTheme::main(); ?>
    			</div>
			</div>
		</div>
	</div>
	<div style="align:center;">
		<strong><?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Top Nav","source"=>"@top")); ?></strong>
	</div>
	<?php expTheme::foot(); ?>
</body>
</html>
