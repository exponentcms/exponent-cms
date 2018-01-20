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
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
	  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-73127984-1', 'auto');
	  ga('send', 'pageview');
	
	</script>
	</head>
<body>
<!-- HEADER CONTAINER -->
<div class="row header">
   
	<!-- NAV --> 
  
	<nav class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
				<h1 class="brand hidden-phone hidden-tablet">
				    <a href="<?php echo URL_FULL; ?>" title="<?php echo SITE_TITLE; ?>">
				        <img src="<?php echo THEME_RELATIVE; ?>images/bootmetrologo.png" alt="">
				    </a>                            
				</h1>
			<!-- navigation bar/menu -->
			<div class="navigation navbar <?php echo (MENU_LOCATION) ? 'navbar-'.MENU_LOCATION : '' ?>">
			    <div class="navbar-inner">
			        <div class="valikko">
			            <!-- toggle for collapsed/mobile navbar content -->
			            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
			                <span class="icon-bar"></span>
			                <span class="icon-bar"></span>
			                <span class="icon-bar"></span>
			            </a>
			            <!-- menu -->
			            <?php expTheme::module(array("controller"=>"navigation","action"=>"showall","view"=>"showall_Flydown")); ?>
			        </div>
			        <div class="languages pull-right"><a href="http://www.primexpharma.com/en/">EN</a> | <a href="http://www.primexpharma.com/ru/">RU</a> | <a href="http://www.primexpharma.com/sp/">SP</a> | <a href="http://www.primexpharma.com/pt/">PT</a>
			        </div>
			    </div>
			</div>
			<div class="navbar-spacer"></div>
			<div class="navbar-spacer-bottom"></div>
	         </div>
	      </div>
	</nav>
</div>
<!-- end Header -->
<div class="pagination-centered mobilelogo hidden-desktop">                  
      <a href="index.html">
          <img src="<?php echo THEME_RELATIVE; ?>images/barlogo-sm.png" alt="">
      </a>
</div>
<!-- Start Callout -->

<div class="full callout">

  <div class="container whitebg">
  	  <div class="row">
  	  	<div class="span8">
  	  		<div class="well">
  	  		<?php expTheme::main(); ?>
  	  		</div>
  	  	</div>
  	  	<div class="span4">
  	  		<div class="well">
  	  	<?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@sidebar1", "scope"=>"sectional")); ?>
  	  		</div>
  	  	</div>
  	  </div>
  </div>
      <!-- FOOTER -->
           <div class="row">

            <div class="footer">

              <!-- Copyright and contact -->

              <div class="span4 address">

<?php expTheme::module(array("controller"=>"container","action"=>"showall","view"=>"showall","source"=>"@footer")); ?>

              </div>

              <!-- Social media buttons -->

              <div class="span8 hidden-phone">
<!--
              <ul class="footerlinks pull-right">
                <li><a href="#"><i class="icon-twitter-sign icon-2x"></i></a></li>
                <li><a href="#"><i class="icon-facebook-sign icon-2x"></i></a></li>
                <li><a href="#"><i class="icon-google-plus-sign icon-2x"></i></a></li>
              </ul>
 -->
              </div>

            </div>
            </div>
</div>
<!-- Javascripts -->


<?php expTheme::foot(); ?>
</body>
</html>
