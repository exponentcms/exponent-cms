<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php 
    expTheme::head(array(
    	"css_primer"=>false,
    	"css_core"=>array('button'),
    	"css_links"=>true,
    	"css_theme"=>false
        )
    );
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
	    <a href="#" onclick="window.print();return false;" class="awesome small blue" title=<?php echo gt("Print this page"); ?>><?php echo gt("Print This Page"); ?></a>
	</div>
	<?php expTheme::main(); ?>
</body>
<?php expTheme::foot(); ?>
</html>