<!DOCTYPE HTML>
<html>
	<head>
	    <?php
        define("JQUERY_THEME",'1');
        define("BOOTSTRAP_THEME",'1');
	    expTheme::head(array(
	        "xhtml"=>false,
            "css_primer"=>array(
                PATH_RELATIVE."external/normalize/normalize.css",
            ),
		    "lesscss"=>array(
                "external/bootstrap/less/bootstrap.less",
                "external/bootstrap/less/responsive.less",
            ),
            "lessvars"=>array(
                'swatch'=>SWATCH,
            ),
	        "css_core"=>array(
                "common"
            ),
	        "css_links"=>true,
	        "css_theme"=>true
        ));
	    ?>
	</head>
	<body>
        <nav class="row">
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Bootstrap Top Nav")); ?>
        </nav>
        <div id="main" class="row">
            <aside id="sidebar" class="span3">
                <?php expTheme::module(array("controller"=>"container2","action"=>"showall","view"=>"showall","source"=>"@left","scope"=>"top-sectional")); ?>
            </aside>
            <div id="content" class="span9">
                <?php expTheme::main(); ?>
            </div>
        </div>
        <footer class="row">
            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer","chrome"=>1)) ?>
            <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
        </footer>
        <?php
            expJavascript::pushToFoot(array(
                "unique"=>'bootstraptheme',
                "jquery"=>1,
                "src"=>PATH_RELATIVE."external/bootstrap/js/bootstrap.min.js",
            ));
            expTheme::foot();
        ?>
	</body>
</html>
