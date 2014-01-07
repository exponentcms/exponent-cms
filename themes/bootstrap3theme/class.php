<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (class_exists('bootstrap3theme')) return;

class bootstrap3theme extends theme {
    public $user_configured = true;
    public $stock_theme = true;

	function name() { return "Twitter Bootstrap 3 Theme"; }
	function author() { return "David Leffler"; }
	function description() { return "An HTML5 responsive grids theme based on <a href=\"http://http://twitter.github.com/bootstrap/\" target=\"_blank\">Twitter Bootstrap v 3</a>"; }

    function configureTheme() {
   		//BOOTSTRAP SWATCHES
//        $swatches = array();
        $swatches[] = gt('Base');
       	if (is_readable(BASE.'external/bootstrap3/less')) {
       		$dh = opendir(BASE.'external/bootstrap3/less');
       		while (($file = readdir($dh)) !== false) {
       			if ($file != '.' && $file != '..' && is_dir(BASE."external/bootstrap3/less/$file")) {
                    $swatches[$file] = ucfirst($file);
       			}
       		}
       	}
		// Button Sizes
        $icon_sizes = array(
            "small"=>'Small',
            "medium"=>'Medium',
			"large"=>'Large',
		);

		// Menu Locations
        $menu_locations = array(
            "fixed-top"=>'Fixed Top',
            "static-top"=>'Static Top',
			"fixed-bottom"=>'Fixed Bottom',
		);

		// Menu Alignments
        $menu_alignments = array(
            "left"=>'Left',
			"right"=>'Right',
		);

   		$settings = expSettings::parseFile(BASE."themes/".$_GET['theme']."/config.php");
        if (empty($settings['SWATCH'])) $settings['SWATCH'] = '';
   		$form = new form();
   		$form->meta('controller','administration');
   		$form->meta('action','update_theme');
   		$form->meta('theme',$_GET['theme']);
        $form->meta('BTN_COLOR','btn');
   		$form->register('swatch',gt('Theme Style').': ',new dropdowncontrol($settings['SWATCH'],$swatches));
        $form->register('btn_size',gt('Button Size').': ',new dropdowncontrol($settings['BTN_SIZE'],$icon_sizes));
        $form->register('menu_location',gt('Menu Location').': ',new dropdowncontrol($settings['MENU_LOCATION'],$menu_locations));
        if (empty($settings['MENU_HEIGHT'])) $settings['MENU_HEIGHT'] = 1;
        $form->register('menu_height',gt('Fixed Menu Height Adjustment').': ',new textcontrol($settings['MENU_HEIGHT'],3,false,'integer'));
        if (empty($settings['MENU_HEIGHT'])) $settings['MENU_HEIGHT'] = 979;
        $form->register('menu_width',gt('Mobile Menu Collapse Width').': ',new textcontrol($settings['MENU_WIDTH'],4,false,'integer'));
        $form->register('menu_align',gt('Menu Alignment').': ',new dropdowncontrol($settings['MENU_ALIGN'],$menu_alignments));
   		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
   		assign_to_template(array(
            'name'=>$this->name().(!empty($_GET['sv'])?' '.$_GET['sv']:''),
            'form_html'=>$form->tohtml()
        ));
   	}

    function saveThemeConfig ($params) {
   		if (empty($params['swatch'])) $params['swatch'] = "''";
        if (empty($params['btn_size'])) $params['btn_size'] = "''";
        if (empty($params['menu_height'])) $params['menu_height'] = "1";
        parent::saveThemeConfig($params);
   	}

}

?>
