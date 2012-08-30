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
		    <h1 id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
                <?php echo ORGANIZATION_NAME; ?>
			</a>
			</h1>
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_YUI Top Nav")); ?>
			<?php expTheme::module(array("controller"=>"search","action"=>"show")) ?>
		</div>
		<div id="bd" class="yui3-g">
			<div id="leftcol" class="yui3-u-1-4">
			    <div class="content">
    			    <?php expTheme::module(array("module"=>"container","view"=>"Default","source"=>"@left","scope"=>"sectional")); ?>
			    </div>
			</div>
			<div id="centercol" class="yui3-u-3-4">
		        <div class="content">
				    <?php expTheme::main(); ?>
    			</div>
			</div>
		</div>
		<div id="ft">
            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"single","source"=>"@footer")) ?>
		</div>
	</div>
<?php expTheme::foot(); ?>
</body>
</html>
