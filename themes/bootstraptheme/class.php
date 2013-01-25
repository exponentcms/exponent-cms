<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

if (class_exists('bootstraptheme')) return;

class bootstraptheme extends theme {
    public $user_configured = true;

	function name() { return "Twitter Bootstrap Theme"; }
	function author() { return "David Leffler"; }
	function description() { return "An HTML5 responsive grids theme based on <a href=\"http://http://twitter.github.com/bootstrap/\" target=\"_blank\">Twitter Bootstrap</a>"; }

    function configureTheme() {
   		//BOOTSTRAP SWATCHES
//        $swatches = array();
        $swatches[] = gt('Base');
       	if (is_readable(BASE.'external/bootstrap/less')) {
       		$dh = opendir(BASE.'external/bootstrap/less');
       		while (($file = readdir($dh)) !== false) {
       			if ($file != '.' && $file != '..' && is_dir(BASE."external/bootstrap/less/$file")) {
                    $swatches[$file] = ucfirst($file);
       			}
       		}
       	}
		//Button Sizes
        $icon_sizes = array(
			'Medium',
			"icon-large"=>'Large',
		);

   		$settings = expSettings::parseFile(BASE."themes/".$_GET['theme']."/config.php");
        if (empty($settings['SWATCH'])) $settings['SWATCH'] = '';
   		$form = new form();
   		$form->meta('controller','administration');
   		$form->meta('action','update_theme');
   		$form->meta('theme',$_GET['theme']);
        $form->meta('BTN_COLOR','btn');
//        $form->meta('BTN_SIZE','icon-large');
   		$form->register('swatch',gt('Theme Style').': ',new dropdowncontrol($settings['SWATCH'],$swatches));
        $form->register('btn_size',gt('Icon Size').': ',new dropdowncontrol($settings['BTN_SIZE'],$icon_sizes));
   		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
   		assign_to_template(array(
           'name'=>self::name(),
           'form_html'=>$form->tohtml()
        ));
   	}

    function saveThemeConfig ($params) {
   		if (empty($params['swatch'])) $params['swatch'] = "''";
        if (empty($params['btn_size'])) $params['btn_size'] = "''";
        parent::saveThemeConfig($params);
   	}

}

?>
