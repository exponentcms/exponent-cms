<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<?php
		$config = array(
			"reset-fonts-grids"=>false,
			"include-common-css"=>true,
			"include-theme-css"=>true
		);
		echo exponent_theme_headerInfo($config);
	?>
	<style>
	    html,body {
	        background: none !important;
	    }     
	    .printer-button-bar {
	        text-align:center;
	        padding:5px;
	        border:2px solid #555588;
	        background:#ddddff;
	    }
	    @media print {
          .printer-button-bar {
              display:none;
          }
        }
	    
	</style>
</head>
<body style="background:none;text-align: left;">
	<div class="printer-button-bar">
	    <a href="#" onclick="window.print();return false;" class="btn" title="Print this page"><strong><em><?php echo exponent_lang_getText("Print This Page"); ?></em></strong></a>
	</div>
	<?php exponent_theme_main(); ?>
</body>
<?php echo exponent_theme_footerInfo(); ?>
</html>
