<!DOCTYPE HTML>
<html lang="en">
	<head>
	    <?php
        if (ENHANCED_STYLE == 1) {
            $enhanced = "external/bootstrap3/less/theme.less";
        } else {
            $enhanced = "";
        }
        expTheme::head(array(
            "xhtml"=>false,
            "framework"=>"bootstrap3",
            "lesscss"=>$enhanced,
            // these viewport settings are the defaults so they are not really needed except to customize
            "viewport"=>array(
                "width"=>"device-width",
                "height"=>"device-height",
                "initial_scale"=>1,
                "minimum_scale"=>0.25,
                "maximum_scale"=>5.0,
                "user_scalable"=>true,
            ),
            "meta"=>array(
                "keywords"=>false,
            ),
            "css_core"=>array(
                "common"
            ),
            // bootstrap (system) variables are overridden in the /less/variables.less file
            "lessvars"=>array(
                'menu_height'=>MENU_HEIGHT,
                'menu_width'=>MENU_WIDTH,
                'menu_align_center'=>(MENU_ALIGN == 'center'),
            ),
            "css_links"=>true,
            "css_theme"=>true
        ));
	    ?>
        <!-- favicons -->
        <link rel="shortcut icon" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon.ico">
       	<link rel="icon" sizes="16x16 32x32 64x64" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon.ico">
       	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-192.png">
       	<link rel="icon" type="image/png" sizes="160x160" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-160.png">
       	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-96.png">
       	<link rel="icon" type="image/png" sizes="64x64" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-64.png">
       	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-32.png">
       	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-16.png">
       	<link rel="apple-touch-icon" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-57.png">
       	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-114.png">
       	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-72.png">
       	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-144.png">
       	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-60.png">
       	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-120.png">
       	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-76.png">
       	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-152.png">
       	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-180.png">
       	<meta name="msapplication-TileColor" content="#FFFFFF">
       	<meta name="msapplication-TileImage" content="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/favicon-144.png">
       	<meta name="msapplication-config" content="<?php echo (PATH_RELATIVE) ?>themes/<?php echo (DISPLAY_THEME) ?>/images/browserconfig.xml">
        <!-- Chrome, Firefox OS, Opera and Vivaldi -->
        <meta name="theme-color" content="#7b098f">
        <!-- Windows Phone -->
        <meta name="msapplication-navbutton-color" content="#7b098f">
        <!-- iOS Safari -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="#7b098f">
	</head>
	<body>
        <header class="masthead hidden-xs">
            <div class="container">
                <div class="row">
                    <div class="col-sm-5">
                        <a href="<?php echo URL_FULL; ?>index.php" title="<?php echo SITE_TITLE; ?>"><img src="<?php echo (THEME_RELATIVE) ?>images/logo.png" alt="<?php echo SITE_TITLE; ?> />"></a>
                    </div>
                    <div class="col-sm-7 pull-right">
                        <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_slogan","source"=>"@slogan")) ?>
                    </div>
                </div>
            </div>
        </header>
        <!-- navigation bar/menu -->
        <div class="container<?php echo (STYLE_WIDTH) ?> main-menu" title="<?php echo strip_tags(SITE_HEADER); ?>">
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
        </div>
        <!-- main page body -->
        <div class="container<?php echo (STYLE_WIDTH) ?> <?php echo (MENU_LOCATION == 'fixed-top') ? 'fixedmenu' : '' ?>">
            <!-- optional flyout sidebar container -->
            <?php global $user; if (FLYOUT_SIDEBAR != 0 && $user->isSuperAdmin()) expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
            <section id="main" class="row">
                <!-- main column -->
                <section id="content" class="col-sm-12">
                    <?php expTheme::main(); ?>
                </section>
            </section>
            <!-- footer -->
            <footer class="row">
                <div class="content col-sm-12 well">
                    <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer")) ?>
                    <?php if (MENU_LOCATION == 'fixed-bottom') echo '<div class="menu-spacer-bottom"></div>'; ?>
                </div>
            </footer>
        </div>
        <?php
        global $user;
        if ($user->getsToolbar && SLINGBAR_TOP) {
            $top = 50;
        } else {
            $top = 0;
        }
        expCSS::pushToHead(array(
            'unique' => 'seibatheme',
            'css' => "
                #topnavbar.affix {
                    position: fixed;
                    top: ".$top."px;
                    width: 100%;
                    z-index:10;
                }
                @media (min-width: ".MENU_WIDTH."px) {
                    #topnavbar.affix {
                        width: inherit;
                    }
                }           ",
        ));
        expTheme::foot(array(
            'unique' => 'hhbctheme',
            'content' => "
                $('#topnavbar').affix({
                      offset: {
                        top: $('header').height()
                      }
                });
            ",
            'bootstrap' => 'affix',
        ));
        ?>
	</body>
</html>
