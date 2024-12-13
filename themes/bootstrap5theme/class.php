<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

if (class_exists('bootstrap5theme')) return;

class bootstrap5theme extends theme {
    public $user_configured = true;
    public $stock_theme = true;

	function name() { return "Bootstrap 5 Theme"; }
	function author() { return "David Leffler"; }
	function description() {
        $settings = expSettings::parseFile(__DIR__ . "/config.php");
        if (empty($settings['SWATCH']))
            $settings['SWATCH'] = 'default';
        $ret = "An HTML5 responsive grids theme based on <a href=\"https://getbootstrap.com/docs/5.2/\" target=\"_blank\">Bootstrap v5</a> and ";
        if (USE_BOOTSTRAP_ICONS) {
            $ret .= "<a href=\"https://icons.getbootstrap.com/\" target=\"_blank\">Bootstrap Icons</a>";
        } else {
            $ret .= "<a href=\"https://fontawesome.com/\" target=\"_blank\">Font Awesome v6</a>";
        }
        if ($settings['SWATCH'] !== 'default')
            $ret .= " using the <strong>" . ucfirst($settings['SWATCH']) . "</strong> Swatch.";
	    return $ret;
	}

    /**
     * are all prerequisites available?
     */
    function supported() {
        return (is_file(BASE . 'external/bootstrap5/scss/newui.scss') && is_file(BASE . 'external/font-awesome6/scss/fontawesome.scss'));
    }

    function configureTheme() {
   		//BOOTSTRAP SWATCHES
        $swatches = array();
       	if (is_readable(BASE.'external/bootstrap5/scss')) {
       		$dh = opendir(BASE.'external/bootstrap5/scss');
       		while (($file = readdir($dh)) !== false) {
       			if ($file !== '.' && $file !== '..' && is_dir(BASE."external/bootstrap5/scss/$file")) {
                    if ($file !== 'forms' && $file !== 'helpers' && $file !== 'mixins' && $file !== 'utilities' && $file !== 'vendor')
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
//            "extrasmall"=>'Extra Small',
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
        if (empty($settings['SWATCH'])) $settings['SWATCH'] = 'default';
   		$form = new form();
   		$form->meta('controller','administration');
   		$form->meta('action','update_theme');
   		$form->meta('theme',$this->params['theme']);
        $form->meta('BTN_COLOR','btn');
   		$form->register('swatch',gt('Theme Style').': ',new dropdowncontrol($settings['SWATCH'],$swatches));
        $form->register('text1','',new customcontrol("<div class=\"control\"><label class=\"form-label\" style=\"margin-bottom: 0;\">".gt('Theme Style Variations')."</label></div><div role=\"group\" class=\"group-controls\">"));
        $form->register('enhanced_style',gt('Add Gradient Effects'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE']))));
        $form->register('enhanced_style2',gt('Add Shadow Effects'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE2']))));
        $form->register('enhanced_style3',gt('Add Transition Effects'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE3']))));
        $form->register('enhanced_style4',gt('Add Rounded Styles'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE4']))));
        $form->register('enhanced_style5',gt('Use Responsive Font Size'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE5']))));
        $form->register('enhanced_style6',gt('Enable Validation Icons'),new checkboxcontrol((!empty($settings['ENHANCED_STYLE6']))));
//        $form->register('use_bootstrap_icons',gt('Use Bootstrap Icons'),new checkboxcontrol((!empty($settings['USE_BOOTSTRAP_ICONS']))));
        $form->register('text2','',new customcontrol("</div><div class=\"control\" style=\"margin-top: -5px;margin-bottom: 6px;\"><small class=\"form-text text-muted\">".gt('These settings will override the Theme Style settings')."</small></div>"));
        $form->register('theme_color',gt('Browser Theme Color').': ',new colorcontrol($settings['THEME_COLOR'],'#000000'));
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
        if (empty($params['swatch'])) $params['swatch'] = "default";
        if (empty($params['enhanced_style'])) $params['enhanced_style'] = '0';
        if (empty($params['enhanced_style2'])) $params['enhanced_style2'] = '0';
        if (empty($params['enhanced_style3'])) $params['enhanced_style3'] = '0';
        if (empty($params['enhanced_style4'])) $params['enhanced_style4'] = '0';
        if (empty($params['enhanced_style5'])) $params['enhanced_style5'] = '0';
        if (empty($params['enhanced_style6'])) $params['enhanced_style6'] = '0';
//        if (empty($params['use_bootstrap_icons'])) $params['use_bootstrap_icons'] = '0';
        if (empty($params['theme_color'])) $params['theme_color'] = '#000000';
        if (empty($params['style_width'])) $params['style_width'] = "";
        if (empty($params['btn_size'])) $params['btn_size'] = "";
        if (empty($params['menu_height'])) $params['menu_height'] = "1";
        if (empty($params['menu_width'])) $params['menu_width'] = "979";
        if (empty($params['menu_length'])) $params['menu_length'] = "2";
        if (empty($params['flyout_sidebar'])) $params['flyout_sidebar'] = '0';
        parent::saveThemeConfig($params);
   	}

}

?>
