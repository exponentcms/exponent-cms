<!DOCTYPE HTML>
<html>
	<head>
	    <?php
	    expTheme::head(array(
	        "xhtml"=>false,
            "normalize"=>true,
            'framework'=>'bootstrap',
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
            <div id="content" class="span12">
                <?php expTheme::main(); ?>
            </div>
        </div>
        <footer class="row">
            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer","chrome"=>1)) ?>
            <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
        </footer>
        <?php
            expTheme::foot();
        ?>
	</body>
</html>
