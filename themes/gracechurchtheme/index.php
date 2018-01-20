<!DOCTYPE HTML>
<html>
    <head>
        <meta name="format-detection" content="telephone=no">
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
        <nav id="topnavbar" class="navigation navbar yamm navbar-default <?php if (MENU_LOCATION) echo 'navbar-'.MENU_LOCATION; ?>" role="navigation">
            <div class="container">
                <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
                <div id="nav_text">
                <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall","source"=>"@nav_text","chrome"=>1)); ?>
                </div>
            </div>
        </nav>
        <div class="navbar-spacer"></div>
        <div class="navbar-spacer-bottom"></div>
        
        <!-- main page body --> 
        <div class="container"> 
            <section id="main" class="row"> 
                <!-- main column wanted on top if collapsed --> 
                <section id="content" class="col-sm-12 rightsectional">
                    
                    <section id="content" class="col-sm-9">
                        <div class="clear20"></div>
                        <!-- photo album slideshow --> 
                        <?php expTheme::module(array("controller"=>"photo","action"=>"slideshow","view"=>"slideshow_right sectional","source"=>"@right_slideshow",'scope'=>'sectional',"chrome"=>1,)) ?>  
                        <?php expTheme::main(); ?>
                     </section>

                     <aside id="sidebar" class="col-sm-3">
                        <div class="clear20"></div>
                        <!-- Sectional Container --> 
                        <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@rightTop_sectional",'scope'=>'sectional',"chrome"=>1)) ?>
                        <!-- link manager grayscale --> 
                        <?php expTheme::module(array("controller"=>"links","action"=>"showall","view"=>"showall_Grayscale","source"=>"@link_grayscale","chrome"=>1)) ?>
                        <!-- Sectional Container --> 
                        <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@rightBottom_sectional",'scope'=>'sectional',"chrome"=>1)) ?>  
                    </aside>
                </section> 
            </section> 
        </div> 
            
        <footer>
            <img src="<?php echo THEME_RELATIVE . 'images/footer.jpg'; ?> " />
            <div class="container">
                <?php expTheme::module(array("controller"=>"search","action"=>"show","view"=>"show","source"=>"@search",)); ?>
                <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@footer","chrome"=>1)) ?>
            </div>
        </footer>
        <?php expTheme::foot(); ?>
    </body>
</html>
