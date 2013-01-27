<!DOCTYPE HTML>
<html>
	<head>
	    <?php
        define("JQUERY_THEME",'1');
        if (!defined('SWATCH')) define('SWATCH',"''");
	    expTheme::head(array(
	        "xhtml"=>false,
		    "lesscss"=>array(
                "external/bootstrap/less/bootstrap.less",
                "external/bootstrap/less/responsive.less",
            ),
            "lessvars"=>array(
                'swatch'=>SWATCH,
                'special'=>"#FFEEDD"
            ),
	        "css_core"=>array("common"),
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
            <aside class="span3">
                <?php expTheme::module(array("controller"=>"container2","action"=>"showall","view"=>"showall","source"=>"@left")); ?>
            </aside>
            <div id="content" class="span9">
                <?php expTheme::main(); ?>
            </div>
        </div>
        <footer class="row">
            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"single","source"=>"@footer","chrome"=>1)) ?>
        </footer>
<!--        <script src="--><?php //echo JQUERY_SCRIPT; ?><!--"></script>-->
<!--        <script src="--><?php //echo PATH_RELATIVE; ?><!--external/bootstrap/js/bootstrap.min.js"></script>-->
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
