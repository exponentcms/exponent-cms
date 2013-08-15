<!DOCTYPE HTML>
<html>
<head>
	<?php 
    expTheme::head(array(
    	"xhtml"=>false,
        "css_primer"=>array(
            YUI3_RELATIVE."cssreset/cssreset-min.css",
            YUI3_RELATIVE."cssfonts/cssfonts-min.css",
            YUI3_RELATIVE."cssgrids/cssgrids-min.css"
        ),
    	"css_core"=>array(
            "common"
        ),
    	"css_links"=>true,
    	"css_theme"=>true
        )
    );
	?>
    <link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
<!--    <script>document.cookie='resolution='+Math.max(screen.width,screen.height)+'; path=/';</script>-->
</head>
<body>
	<div id="doc">
		<div id="hd">
		    <div id="logo">
			<a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
                <?php echo ORGANIZATION_NAME; ?>
			</a>
			</div>
            <?php //expTheme::module(array("controller"=>"login","action"=>"showlogin","view"=>"showlogin_flyoutYUI")); ?>
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_flyout_sidebar","source"=>"navsidebar","chrome"=>true)); ?>
            <?php //expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall_flyoutsidebar","chrome"=>true,"source"=>"flyout")); ?>
            <?php //expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_YUI Top Nav")); ?>
            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_mega","source"=>"mega","chrome"=>true)); ?>
			<?php //expTheme::module(array("controller"=>"links","action"=>"showall","view"=>"showall_quicklinks")) ?>
			<?php expTheme::module(array("controller"=>"search","action"=>"show")) ?>
		</div>
		<div id="bd" class="yui3-g">
			<div id="leftcol" class="yui3-u-1-4">
			    <div class="content">
                    <?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@left")); ?>
			    </div>
			</div>
			<div id="centercol" class="yui3-u-3-4">
		        <div class="content">
				    <?php expTheme::main(); ?>
    			</div>
			</div>
		</div>
		<div id="ft">
            <?php expTheme::module(array("controller"=>"text","action"=>"showall","view"=>"showall_single","source"=>"@footer")) ?>
		</div>
	</div>
<?php expTheme::foot(); ?>
</body>
</html>
