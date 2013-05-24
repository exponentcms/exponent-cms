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
                "css_links"=>true,
                "css_theme"=>true
            ));
	    ?>
	</head>
	<body>
        <div class="navigation navbar <?php echo (MENU_LOCATION) ? 'navbar-'.MENU_LOCATION : '' ?>">
  			<div class="navbar-inner">
  				<div class="container">
  					<a class="brand" href="<?php echo URL_FULL ?>"><?php echo ORGANIZATION_NAME ?></a>
  					<?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
  				</div>
  			</div>
  		</div>
        <div class="navbar-spacer"></div>
        <div class="navbar-spacer-bottom"></div>
        <div class="container <?php echo (MENU_LOCATION) ? 'fixedmenu' : '' ?>">
            <section id="main" class="row">
                <aside id="sidebar" class="span3">
                    <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left","scope"=>"sectional")); ?>
                </aside>
                <section id="content" class="span9">
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
