<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

if (class_exists('multioptionstheme')) return;

class multioptionstheme extends theme {
	public $user_configured = true;

	function name() { return "Multi-Options Theme"; }
	function author() { return "David Leffler"; }
	function description() { return "A user configurable simple theme from <a href=\"http://andreasviklund.com/\" target=\"_blank\">andreasviklund.com</a>"; }

	function configureTheme() {
		//THEME COLORS
		$theme_colors = array(
			"orange"=>'Orange',
			"blue"=>'Blue',
			"red"=>'Red',
			"green"=>'Green',
			"purple"=>'Purple',
			"black"=>'Black',
			"yellow"=>'Yellow',
			"pink"=>'Pink',
			"grey"=>'Grey',
			"magenta"=>'Magenta',
		);
		//THEME WIDTHS
		$button_sizes = array(
			"small"=>'Small',
			"medium"=>'Medium',
			"large"=>'Large',
		);
		//THEME WIDTHS
		$theme_widths = array(
			""=>'980px',
			"w1200px"=>'1200px',
			"w760px"=>'760px',
			"w600px"=>'600px',
		);
		//THEME FONTS
		$theme_fonts = array(
			""=>'Tahoma',
			"georgia"=>'Georga',
			"times"=>'Times',
			"helvetica"=>'Helvetica',
			"verdana"=>'Verdana',
			"arial"=>'Arial',
			"courier"=>'Courier',
		);

		$settings = expSettings::parseFile(BASE."themes/".$_GET['theme']."/config.php");
		$form = new form();
		$form->meta('controller','administration');
		$form->meta('action','update_theme');
		$form->meta('theme',$_GET['theme']);
		$form->register('multi_size',gt('Theme Width').': ',new dropdowncontrol($settings['MULTI_SIZE'],$theme_widths));
		$form->register('multi_font',gt('Theme Font').': ',new dropdowncontrol($settings['MULTI_FONT'],$theme_fonts));
		$form->register('multi_color',gt('Theme Color').': ',new dropdowncontrol($settings['MULTI_COLOR'],$theme_colors));
		$form->register('btn_color',gt('Button Color').': ',new dropdowncontrol($settings['BTN_COLOR'],$theme_colors));
		$form->register('btn_size',gt('Button Size').': ',new dropdowncontrol($settings['BTN_SIZE'],$button_sizes));
		$form->register(null,'',new htmlcontrol('<br>'));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		assign_to_template(array('name'=>self::name(),'form_html'=>$form->tohtml()));
	}

}

?>