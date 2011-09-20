<!DOCTYPE HTML>
<html>
<head>
	<?php 
    expTheme::head(array(
    	"xhtml"=>false,
        "css_primer"=>array(YUI3_PATH."cssreset/reset-min.css",
                            YUI3_PATH."cssfonts/fonts-min.css",
                            YUI3_PATH."cssgrids/grids-min.css"),
    	"css_core"=>array("common"),
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
		    <h1><?php echo SITE_TITLE; ?>"><?php echo ORGANIZATION_NAME; ?></h1>
		</div>
		<div id="bd" class="yui3-g">
			<div id="centercol" class="yui3-u-3-4">
		        <div class="content">
				    <?php expTheme::main(); ?>
    			</div>
			</div>
		</div>
	</div>
<?php echo expTheme::foot(); ?>
</body>
</html>
