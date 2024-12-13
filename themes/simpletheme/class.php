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

if (class_exists('simpletheme')) return;

class simpletheme extends theme {
	public $user_configured = true;
    public $stock_theme = true;
	function name() { return "Simple Theme"; }
	function author() { return "Phillip Ball - Online Innovative Creations"; }
	function description() { return "A minimal, slick theme based on the <a href=\"http://yuilibrary.com/yui/docs/cssgrids/\" target=\"_blank\">YUI 3 Gridding System</a>"; }

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
   		//Button Sizes
   		$button_sizes = array(
   			"small"=>'Small',
   			"medium"=>'Medium',
   			"large"=>'Large',
   		);

   		$settings = expSettings::parseFile(BASE."themes/".$this->params['theme']."/config.php");
   		$form = new form();
   		$form->meta('controller','administration');
   		$form->meta('action','update_theme');
   		$form->meta('theme',$this->params['theme']);
   		$form->register('btn_color',gt('Button Color').': ',new dropdowncontrol($settings['BTN_COLOR'],$theme_colors));
   		$form->register('btn_size',gt('Button Size').': ',new dropdowncontrol($settings['BTN_SIZE'],$button_sizes));
   //		$form->register(null,'',new htmlcontrol('<br>'));
   		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
   		assign_to_template(array(
               'name'=>$this->name().(!empty($this->params['sv'])?' '.$this->params['sv']:''),
               'form_html'=>$form->tohtml()
           ));
   	}

}

?>