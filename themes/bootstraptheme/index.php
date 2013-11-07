<!DOCTYPE HTML>
<html>
    <head>
        <?php
        expTheme::head(array(
            "xhtml"=>false,
            "normalize"=>true,
            "framework"=>"bootstrap",
            // these viewport settings are the defaults so they are not really needed except to customize
            "viewport"=>array(
                "width"=>"device-width",
                "height"=>"device-height",
                "initial_scale"=>1,
                "minimum_scale"=>0.25,
                "maximum_scale"=>5.0,
                "user_scalable"=>true,
            ),
            "css_core"=>array(
                "common"
            ),
            // bootstrap (system) variables are overridden in the /less/variables.less file
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
        <!-- navigation bar/menu -->
        <div class="navigation navbar <?php echo (MENU_LOCATION) ? 'navbar-'.MENU_LOCATION : '' ?>">
            <div class="navbar-inner">
                <div class="container">
                    <!-- toggle for collapsed/mobile navbar content -->
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <!-- menu header -->
                    <a class="brand" href="<?php echo URL_FULL ?>"><?php echo ORGANIZATION_NAME ?></a>
                    <!-- menu -->
                    <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
                </div>
            </div>
        </div>
        <div class="navbar-spacer"></div>
        <div class="navbar-spacer-bottom"></div>
        <!-- main page body -->
        <div class="container <?php echo (MENU_LOCATION) ? 'fixedmenu' : '' ?>">
            <!-- optional flyout sidebar container -->
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
            <!-- left column -->
            <section id="main" class="row">
                <aside id="sidebar" class="span3">
                    <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left")); ?>
                </aside>
                <!-- main column -->
                <section id="content" class="span9">
                    <?php expTheme::main(); ?>
                </section>
            </section>
            <!-- footer -->
            <footer class="row">
                <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer","chrome"=>1)) ?>
                <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
            </footer>
        </div>
        <?php expTheme::foot(); ?>
    </body>
</html>
