<!DOCTYPE HTML>
<html>
<html lang="<?php echo (substr(LOCALE,0,2)) ?>">
	    <?php
            expTheme::head(array(
//                "xhtml"=>false,
                "normalize"=>true,
                "framework"=>"bootstrap",
                "css_core"=>array(
                    "common"
                ),
                "lessvars"=>array(
                    'menu_height'=>MENU_HEIGHT,
                    'menu_width'=>MENU_WIDTH,
                ),
//                "css_links"=>true,
//                "css_theme"=>true
            ));
	    ?>
	</head>
	<body>
        <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
        <div class="container <?php echo (MENU_LOCATION == 'fixed-top') ? 'fixedmenu' : '' ?>">
            <!-- optional flyout sidebar container -->
            <?php if (FLYOUT_SIDEBAR != 0) expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
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
