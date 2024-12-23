<!DOCTYPE HTML>
<html lang="<?php echo (substr(LOCALE,0,2)) ?>">
	<head>
	    <?php
        expTheme::head(array(
            "xhtml"=>false,
            "framework"=>"bootstrap4",
            // these viewport settings are the defaults so they are not really needed except to customize
            "viewport"=>array(
                "width"=>"device-width",
                "height"=>"device-height",
                "initial_scale"=>1,
                "minimum_scale"=>0.25,
//                "maximum_scale"=>5.0,
                "user_scalable"=>true,
            ),
            "css_core"=>array(
                "common"
            ),
            // bootstrap (system) variables are overridden in the /scss/_variables.scss file
            "lessvars"=>array(
                'menu_height'=>MENU_HEIGHT,
                'menu_width'=>MENU_WIDTH,
                'menu_align_center'=>(MENU_ALIGN == 'center'),
                'enable-gradients'=>(ENHANCED_STYLE == 1),
                'enable-shadows'=>(ENHANCED_STYLE2 == 1),
                'enable-transitions'=>(ENHANCED_STYLE3 == 1),
                'enable-rounded'=>(ENHANCED_STYLE4 == 1),
                'enable-responsive-font-sizes'=>(ENHANCED_STYLE5 == 1),
                'enable-validation-icons'=>(ENHANCED_STYLE6 == 1),
            ),
            "css_links"=>true,
            "css_theme"=>true
        ));
	    ?>
        <!-- Chrome, Firefox OS, Opera and Vivaldi -->
        <meta name="theme-color" content="<?php echo (THEME_COLOR) ?>">
        <!-- Windows Phone -->
        <meta name="msapplication-navbutton-color" content="<?php echo (THEME_COLOR) ?>">
        <!-- iOS Safari -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="<?php echo (THEME_COLOR) ?>">
	</head>
	<body>
        <!-- navigation bar/menu -->
        <div class="container<?php echo (STYLE_WIDTH) ?> main-menu">
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
        </div>
        <!-- main page body -->
        <div class="container<?php echo (STYLE_WIDTH) ?> <?php echo (MENU_LOCATION == 'fixed-top') ? 'fixedmenu' : '' ?>">
            <!-- optional flyout sidebar container -->
            <?php if (FLYOUT_SIDEBAR != 0) expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
            <section id="main" class="row">
                <!-- main column wanted on top if collapsed -->
                <section id="content" class="col-sm-9 order-md-last">
                    <?php expTheme::main(); ?>
                </section>
                <!-- left column -->
                <aside id="sidebar" class="card card-body col-sm-3 order-md-first">
                    <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left","scope"=>"top-sectional")); ?>
                </aside>
            </section>
            <!-- footer -->
            <footer class="row">
                <div class="content col-sm-12">
                    <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer","chrome"=>1)) ?>
                    <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
                </div>
            </footer>
        </div>
        <?php expTheme::foot(); ?>
	</body>
</html>
