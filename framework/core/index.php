<!DOCTYPE HTML>
<html>
	<head>
	    <?php
	    expTheme::head(array(
                "xhtml"=>false,
       		    "css_primer"=>array(),
       	        "css_core"=>array("common"),
       	        "css_links"=>true,
       	        "css_theme"=>false
	        )
	    );
	    ?>
        <style>
            #hd, #bd, #ft {
                position:relative;
            }
            #hd {
                z-index:3;
            }
            #hd .top-nav {
                position:relative;
                z-index:5;
            }
            #bd {
                z-index:2;
            }
            #ft {
                z-index:1;
            }
        </style>
	</head>
	<body>
		<div id="doc4" class="yui-t2">
			<div id="hd" style="width:100%;display:inline-block;margin:5px">
				<h1>
				    <a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>"><?php echo ORGANIZATION_NAME; ?></a> <sub><?php echo SITE_HEADER; ?></sub>
				</h1>
                <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"YUI Top Nav")); ?>
			</div>
			<div id="bd" style="width:100%;display:inline-block;margin:5px;">
				<div style="width:25%;float:left;margin:5px;">
	                <?php expTheme::module(array("module"=>"container","action"=>"showall","source"=>"@left")); ?>
				</div>
				<div style="width:70%;float:right;margin:5px;">
                    <?php expTheme::main(); ?>
				</div>
			</div>
			<div id="ft" style="width:100%;display:inline-block;margin:5px;">
	            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"single","source"=>"@footer","chrome"=>1)) ?>
			</div>
		</div>
        <div style="font-weight:bold;color:red;text-align:center;background-color:black;">
        <?php echo gt('There is a problem using the current theme').'('.DISPLAY_THEME.'), '.gt('this is the system fallback theme!'); ?>
        </div>
	    <?php expTheme::foot(); ?>
	</body>
</html>
