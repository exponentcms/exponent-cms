<!DOCTYPE HTML>
<html>
	<head>
	    <?php
            expTheme::head(array(
                "xhtml"=>false,
                "normalize"=>true,
                "framework"=>"bootstrap",
                "css_core"=>array(
                    "common"
                ),
                "lessvars"=>array(
                    'menu_height'=>MENU_HEIGHT,
                    'menu_width'=>MENU_WIDTH,
                ),
                "css_links"=>true,
                "css_theme"=>true
            ));
	    ?>
	</head>
	<body>
        <div class="navigation navbar <?php echo (MENU_LOCATION) ? 'navbar-'.MENU_LOCATION : '' ?>">
            <div class="navbar-inner">
                <div class="container">
                    <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo URL_FULL ?>"><?php echo ORGANIZATION_NAME ?></a>
                    <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
                    <?php //expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Responsive Nav")); ?>
                </div>
            </div>
        </div>
        <div class="navbar-spacer"></div>
        <div class="navbar-spacer-bottom"></div>
        <div class="container <?php echo (MENU_LOCATION) ? 'fixedmenu' : '' ?>">
            <section id="main" class="row">
                <section id="content" class="span12">
                    <?php expTheme::main(); ?>
                </section>
            </section>
            <footer class="row">
                <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer","chrome"=>1)) ?>
                <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
            </footer>
        </div>
        <?php expTheme::foot(); ?>
	</body>
</html>
