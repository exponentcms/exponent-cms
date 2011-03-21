<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

class administrationController extends expController {
    public $basemodel_name = 'expRecord';
    public $useractions = array();
    public $add_permissions = array('administrate'=>'Manage Administration','toggle_minify'=>'Configure Website Settings',"switch_themes"=>"change themes");
    
    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Administration Controls"; }
    function description() { return "This is the beginnings of the new Administration Module"; }
    function author() { return "Adam Kessler - OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }
    
    
    public function manage_unused_tables() {
        global $db;
        
        expHistory::set('managable', $this->params);
        $unused_tables = array();
        $tables = $db->getTables();
        //eDebug($tables);
        foreach($tables as $table) {
            $basename = str_replace(DB_TABLE_PREFIX.'_', '', $table);
            $oldpath = BASE.'datatypes/definitions/'.$basename.'.php';
            $mvcpath = BASE.'framework/core/database/definitions/'.$basename.'.php';
            if (!file_exists($oldpath) && !file_exists($mvcpath) && !stristr($basename, 'formbuilder')) {
                $unused_tables[$basename]->name = $table;
                $unused_tables[$basename]->rows = $db->countObjects($basename);
            }
        }
        
        assign_to_template(array('unused_tables'=>$unused_tables));
    }
    
    public function delete_unused_tables() {
        global $db;

        $count = 0;
        foreach($this->params['tables'] as $del=>$table) {
            $basename = str_replace(DB_TABLE_PREFIX.'_', '', $table);
            $count += $db->dropTable($basename);
        }
        
        flash('message', 'Deleted '.$count.' unused tables.');
        expHistory::back();
    }
    
    public function toolbar() {
        global $user;
        $menu = array();
		$dirs = array(BASE.'framework/modules/administration/menus', BASE.'themes/'.DISPLAY_THEME_REAL.'/modules/administration/menus');
		foreach ($dirs as $dir) {
		    if (is_readable($dir)) {
			    $dh = opendir($dir);
			    while (($file = readdir($dh)) !== false) {
				    if (substr($file,-4,4) == '.php' && is_readable($dir.'/'.$file) && is_file($dir.'/'.$file)) {
					    $menu[substr($file,0,-4)] = include($dir.'/'.$file);
				    }
			    }
		    }
		}

        // sort the menus alphabetically by filename
		ksort($menu);		
		$sorted = array();
		foreach($menu as $m) $sorted[] = $m;
        
        
        //slingbar position
        if (expSession::exists("slingbar_top")){
            $top = expSession::get("slingbar_top");
        } else {
            $top = SLINGBAR_TOP;
        }
        
        
		assign_to_template(array('menu'=>json_encode($sorted),"top"=>$top));
    }
    
    public function index() {
        redirect_to(array('controller'=>'administration', 'action'=>'toolbar'));
    }
    
    public function update_SetSlingbarPosition() {
       expSession::set("slingbar_top",$this->params['top']);
    }
    
    public function toggle_minify() {
        if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');
    	$value = (MINIFY == 1) ? 0 : 1;
    	exponent_config_change('MINIFY', $value);
    	$message = (MINIFY != 1) ? "Exponent is now minifying Javascript and CSS" : "Exponent is no longer minifying Javascript and CSS" ;
    	flash('message',$message);
    	expHistory::back();
    }
    
    public function manage_themes() {
        expHistory::set('managable', $this->params);
    	$themes = array();
    	if (is_readable(BASE.'themes')) {
    		$dh = opendir(BASE.'themes');
    		while (($file = readdir($dh)) !== false) {
    			if (is_readable(BASE."themes/$file/class.php")) {
    				include_once(BASE."themes/$file/class.php");
    				$theme = new $file();
    				$t = null;
    				$t->name = $theme->name();
    				$t->description = $theme->description();
    				$t->author = $theme->author();
    				if (is_dir(BASE."themes/$file/css_default")) {
                		$sv = opendir(BASE.'themes/'.$file);
                		while (($s = readdir($sv)) !== false) {
                            if (substr($s,0,4) == "css_") {
                                $t->style_variations[str_replace("css_","",$s)] = str_replace("css_","",$s);
                            }
                        }
        			}
    				$t->preview = is_readable(BASE."themes/$file/preview.jpg") ? "themes/$file/preview.jpg" : "themes/" . DISPLAY_THEME . "/noprev.jpg";
    				$themes[$file] = $t;
    			}
    		}
    	}

        assign_to_template(array('themes'=>$themes));
    }
    
    public function switch_themes() {
        if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');

    	exponent_config_change('DISPLAY_THEME_REAL', $this->params['theme']);
    	
    	if (isset($this->params['sv']) && THEME_STYLE!=$this->params['sv']) {
        	exponent_config_change('THEME_STYLE', $this->params['sv']);
    	    if (expFile::recurse_copy(BASE."themes/".$this->params['theme']."/css", BASE."themes/".$this->params['theme']."/styles_backup/css")
    	        && expFile::recurse_copy(BASE."themes/".$this->params['theme']."/images", BASE."themes/".$this->params['theme']."/styles_backup/images")) {

        	    if (!expFile::recurse_copy(BASE."themes/".$this->params['theme']."/css_".$this->params['sv'], BASE."themes/".$this->params['theme']."/css")) {
                    flash('error',expLang::gettext('Couldn\'t copy') . "css_".$this->params['sv']);
        	    }
        	    if (!expFile::recurse_copy(BASE."themes/".$this->params['theme']."/images_".$this->params['sv'], BASE."themes/".$this->params['theme']."/images")) {
                    flash('error',expLang::gettext('Couldn\'t copy') . "images_".$this->params['sv']);
        	    }

                flash('message',expLang::gettext('Your website\'s theme has been updated'));
    	    } else {
                flash('error',expLang::gettext('Exponent could not not switch your theme style variation because it wasn unable to cak up your current css and images directories. Create a directory called styles_backup within your theme, and try again.'));
    	    }
            //copy(BASE."themes/".DISPLAY_THEME_REAL."/css_".$this->params['sv'], BASE."themes/".DISPLAY_THEME_REAL."/css");
            //copy(BASE."themes/".DISPLAY_THEME_REAL."css_".$this->params['sv'], BASE."themes/".DISPLAY_THEME_REAL."css")
    	}
     
        // $message = (MINIFY != 1) ? "Exponent is now minifying Javascript and CSS" : "Exponent is no longer minifying Javascript and CSS" ;
        // flash('message',$message);
    	expHistory::returnTo('managable');
    }	
    
    public function configure_site () {
        // little glue to help things move along
        if (!defined('SYS_CONFIG')) require_once(BASE.'subsystems/config.php');
        
        // TYPES OF ANTISPAM CONTROLS... CURRENTLY ONLY ReCAPTCHA
        $as_types = array(
            '0'=>'-- Please Select an Anti-Spam Control --',
            "recaptcha"=>'reCAPTCHA'
        );
        
        //THEMES FOR RECAPTCHA
        $as_themes = array(
            "red"=>'DEFAULT RED',
        	"white"=>'White',
        	"blackglass"=>'Black Glass',
        	"clean"=>'Clean (very generic)',
        	//"custom"=>'Custom' --> THIS MAY BE COOL TO ADD LATER...
        );
        
        // Available Themes
        $themes = array();
        if (is_readable(BASE.'themes')) {
        	$theme_dh = opendir(BASE.'themes');
        	while (($theme_file = readdir($theme_dh)) !== false) {
        		if (is_readable(BASE.'themes/'.$theme_file.'/class.php')) {
        			// Need to avoid the duplicate theme problem.
        			if (!class_exists($theme_file)) {
        				include_once(BASE.'themes/'.$theme_file.'/class.php');
        			}

        			if (class_exists($theme_file)) {
        				// Need to avoid instantiating non-existent classes.
        				$t = new $theme_file();
        				$themes[$theme_file] = $t->name();
        			}
        		}
        	}
        }
        uasort($themes,'strnatcmp');
        
        // Available Languages
        $langs = array();
        if (is_readable(BASE.'framework/core/lang')) {
        	$lang_dh = opendir(BASE.'framework/core/lang');
        	while (($lang_file = readdir($lang_dh)) !== false) {
    			if (substr($lang_file, -4) == '.php') {
    				$langs[str_replace(".php","",$lang_file)] = str_replace(".php","",$lang_file);
    			}
        	}
        }
        ksort($langs);
        
        // attribution 
        $attribution = array('firstlast'=>'John Doe','lastfirst'=>'Doe, John','first'=>'John','username'=>'jdoe');
        
        // These funcs need to be moved up in to new subsystems
        
        // Date Format
        $date_format = exponent_config_dropdownData('date_format');
        
        // Time Format
        $time_format = exponent_config_dropdownData('time_format');
        
        // Start of Week
        $start_of_week = exponent_config_dropdownData('start_of_week');

        // File Permissions
        $file_permisions = exponent_config_dropdownData('file_permissions');
        
        // File Permissions
        $dir_permissions = exponent_config_dropdownData('dir_permissions');

        // Homepage Dropdown
        $section_dropdown = navigationmodule::levelDropDownControlArray(0);

        assign_to_template(array('as_types'=>$as_types,
                                'as_themes'=>$as_themes,
                                'themes'=>$themes,
                                'langs'=>$langs,
                                'attribution'=>$attribution,
                                'date_format'=>$date_format,
                                'time_format'=>$time_format,
                                'start_of_week'=>$start_of_week,
                                'file_permisions'=>$file_permisions,
                                'dir_permissions'=>$dir_permissions,
                                'section_dropdown'=>$section_dropdown
                                ));
    }
    
    public function update_siteconfig () {
        if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');

        foreach ($this->params['sc'] as $key => $value) {
            exponent_config_change($key, stripslashes($value));
        }
        
        flash('message', "Your Website Configuration has been updated");
        expHistory::back();
    }    
}

?>
