<!DOCTYPE HTML>
<html>
<head>
<?php
    expTheme::head(array(
    	"css_core"=>array('button'),
        "lessvars"=>array(
            'menu_height'=>MENU_HEIGHT,
            'menu_width'=>MENU_WIDTH,
            'menu_align_center'=>(MENU_ALIGN === 'center'),
            'enable-gradients'=>(ENHANCED_STYLE == 1),
            'enable-shadows'=>(ENHANCED_STYLE2 == 1),
            'enable-transitions'=>(ENHANCED_STYLE3 == 1),
            'enable-rounded'=>(ENHANCED_STYLE4 == 1),
            'enable-responsive-font-sizes'=>(ENHANCED_STYLE5 == 1),
            'enable-validation-icons'=>(ENHANCED_STYLE6 == 1),
        ),
        "css_links"=>true,
//    	"css_theme"=>false,
        "framework"=>'bootstrap4'
    ));
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
        .module-actions, .item-actions {
            display:none !important;
        }
        <?php if (EXPORT_AS_PDF) { ?>
        table {
            border-collapse:collapse;
        }
        table td, table th {
            border:1px solid black;
            padding: 5px;
        }
        <?php } ?>
	    @media print {
          .printer-button-bar {
              display:none;
          }
        }
	</style>
</head>
<body style="background:none;text-align: left;">
    <?php if (!EXPORT_AS_PDF) { ?>
	<div class="printer-button-bar">
	    <a href="#" onclick="window.print();return false;" class="awesome small blue" title=<?php echo gt("Print this page"); ?>><?php echo gt("Print This Page"); ?></a>
	</div>
    <?php } ?>
    <div class="container">
    	<?php expTheme::main(); ?>
    </div>
</body>
<?php expTheme::foot(); ?>
</html>