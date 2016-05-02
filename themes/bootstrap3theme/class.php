<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
	function description() { return "An HTML5 responsive grids theme based on <a href=\"http://getbootstrap.com/\" target=\"_blank\">Twitter Bootstrap v 3</a>"; }

    function configureTheme() {
   		//BOOTSTRAP SWATCHES
        $swatches = array();
       	if (is_readable(BASE.'external/bootstrap3/less')) {
       		$dh = opendir(BASE.'external/bootstrap3/less');
       		while (($file = readdir($dh)) !== false) {
       			if ($file != '.' && $file != '..' && is_dir(BASE."external/bootstrap3/less/$file")) {
                    if ($file != 'mixins' && $file != 'fonts')
                        $swatches[$file] = ucfirst($file);
       			}
       		}
       	}

		// Style width
        $style_widths = array(
            ""=>'Fixed',
            "-fluid"=>'Fluid',
		);

		// Button Sizes
        $icon_sizes = array(
            "extrasmall"=>'Extra Small',
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
            "center"=>'Center',
			"right"=>'Right',
		);

   		$settings = expSettings::parseFile(BASE."themes/".$this->params['theme']."/config.php");
        if (empty($settings['SWATCH'])) $settings['SWATCH'] = 'custom';
   		$form = new form();
   		$form->meta('controller','administration');
   		$form->meta('action','update_theme');
   		$form->meta('theme',$this->params['theme']);
        $form->meta('BTN_COLOR','btn');
   		$form->register('swatch',gt('Theme Style').': ',new dropdowncontrol($settings['SWATCH'],$swatches));
        $form->register('enhanced_style',gt('Add Bootstrap v2 Style Effects'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE'])?$settings['ENHANCED_STYLE']:0)));
        $form->register('style_width',gt('Style Width Type').': ',new dropdowncontrol($settings['STYLE_WIDTH'],$style_widths));
        $form->register('btn_size',gt('Button Size').': ',new dropdowncontrol($settings['BTN_SIZE'],$icon_sizes));
        $form->register('menu_location',gt('Menu Location').': ',new dropdowncontrol($settings['MENU_LOCATION'],$menu_locations));
        if (empty($settings['MENU_HEIGHT'])) $settings['MENU_HEIGHT'] = 1;
        $form->register('menu_height',gt('Fixed Menu Height Adjustment').': ',new textcontrol($settings['MENU_HEIGHT'],3,false,'integer'));
        if (empty($settings['MENU_WIDTH'])) $settings['MENU_WIDTH'] = 979;
        $form->register('menu_width',gt('Mobile Menu Collapse Width').': ',new textcontrol($settings['MENU_WIDTH'],4,false,'integer'));
        $form->register('menu_align',gt('Menu Alignment').': ',new dropdowncontrol($settings['MENU_ALIGN'],$menu_alignments));
        if (empty($settings['MENU_LENGTH'])) $settings['MENU_LENGTH'] = 2;
        $form->register('menu_length',gt('Maximum Menu Levels').': ',new textcontrol($settings['MENU_LENGTH'],3,false,'integer'));
        $form->register('flyout_sidebar',gt('Enable Sidebar Flyout Container'),new checkboxcontrol((!empty($settings['FLYOUT_SIDEBAR'])?$settings['FLYOUT_SIDEBAR']:0)));
   		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
   		assign_to_template(array(
            'name'=>$this->name().(!empty($this->params['sv'])?' '.$this->params['sv']:''),
            'form_html'=>$form->tohtml()
        ));
   	}

    function saveThemeConfig ($params) {
        if (empty($params['swatch'])) $params['swatch'] = "custom";
        if (empty($params['enhanced_style'])) $params['enhanced_style'] = '0';
        if (empty($params['style_width'])) $params['style_width'] = "";
        if (empty($params['btn_size'])) $params['btn_size'] = "";
        if (empty($params['menu_height'])) $params['menu_height'] = "1";
        if (empty($params['menu_length'])) $params['menu_length'] = "2";
        if (empty($params['flyout_sidebar'])) $params['flyout_sidebar'] = '0';
        parent::saveThemeConfig($params);
   	}

}

?>
