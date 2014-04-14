<!DOCTYPE HTML>
<html>
	<head>
	    <?php
	    expTheme::head(array(
            "xhtml"=>false,
            "normalize"=>true,
            "css_primer"=>array(),
            "css_core"=>array("common"),
            "css_links"=>true,
            "css_theme"=>false
        ));
        expCSS::pushToHead(array(
            "unique"=>'theme_style',
            "css"=> '
            #hd, #bd, #ft {
                position:relative;
                width:100%;
                display:inline-block;
                margin:5px;
            }
            #hd {
                z-index:3;
            }
            #hd .top-nav {
                z-index:5;
                position:relative;
            }
            #bd {
                z-index:2;
            }
            #ft {
                z-index:1;
            }'
        ));
        ?>
	</head>
	<body>
		<div>
			<div id="hd">
				<h1>
				    <a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>"><?php echo ORGANIZATION_NAME; ?></a> <sub><?php echo SITE_HEADER; ?></sub>
				</h1>
                <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"Flydown")); ?>
			</div>
			<div id="bd">
				<div style="width:25%;float:left;margin:5px;">
	                <?php expTheme::module(array("controller"=>"container","action"=>"showall","source"=>"@left")); ?>
				</div>
				<div style="width:70%;float:right;margin:5px;">
                    <?php expTheme::main(); ?>
				</div>
			</div>
			<div id="ft">
	            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"single","source"=>"@footer","chrome"=>1)) ?>
			</div>
		</div>
        <div style="font-weight:bold;color:red;text-align:center;background-color:black;">
        <?php echo gt('There is a problem using the current theme').'('.DISPLAY_THEME.'), '.gt('this is the system fallback theme!'); ?>
        </div>
	    <?php expTheme::foot(); ?>
	</body>
</html>
