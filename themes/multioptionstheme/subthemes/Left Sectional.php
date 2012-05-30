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
</head>
<body class="<?php echo MULTI_COLOR.' '.MULTI_SIZE.' '.MULTI_FONT; ?>">
	<div id="wrap">
		<div id="header">
			<h1 id="logo">
				<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>"><?php echo ORGANIZATION_NAME; ?></a> <sub><?php echo SITE_HEADER; ?></sub>
			</h1>
			<?php expTheme::module(array("controller"=>"login","action"=>"showlogin","view"=>"showlogin_flyoutYUI")); ?>
			<?php expTheme::module(array("module"=>"navigation","view"=>"YUI Top Nav","source"=>"@top")); ?>
		</div>
		<div id="content-wrap">
			<div id="content">
				<?php expTheme::main(); ?>
			</div>
			<div id="sidebar">
				<?php expTheme::module(array("module"=>"container","view"=>"Default","source"=>"@left","scope"=>"sectional")); ?>
			</div>
		</div>
	</div>
	<div id="footer">
		<?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"single","source"=>"@footer","chrome"=>1)) ?>
	</div>
    <?php expTheme::foot(); ?>
</body>
</html>
