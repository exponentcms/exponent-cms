<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../../../.." */

class administrationController extends expController {
    public $basemodel_name = 'expRecord';
    public $useractions = array();
    public $add_permissions = array(
	    'administrate'=>'Manage Administration',
	    'clear_all_cache'=>'Clear All Caches',
	    'clear_css_cache'=>'Clear CSS Cache',
	    'clear_image_cache'=>'Clear Image Cache',
	    'clear_rss_cache'=>'Clear RSS Cache',
	    'clear_smarty_cache'=>'Clear Smarty Cache',
	    'configure_site'=>'Configure Site',
	    'delete_unused_tables'=>'Delete Unused Tables',
	    "fix_database"=>"Fix Database",
	    "fix_sessions"=>"Fix Sessions",
	    "install_tables"=>"Install Tables",
	    'manage_unused_tables'=>'Manage Unused Tables',
	    'optimize_database'=>'Optimize Database',
	    'toggle_dev'=>'Toggle Development Mode',
	    'toggle_maintenance'=>'Toggle Maintenance Mode',
	    'toggle_minify'=>'Toggle Minify Mode',
	    "switch_themes"=>"Change Themes",
	    "upload_extension"=>"Upload Extension",
        );
	public $codequality = 'beta';
    
    function name() { return $this->displayname(); } //for backwards compat with old modules
    function displayname() { return "Administration Controls"; }
    function description() { return "This is the beginnings of the new Administration Module"; }
    function author() { return "OIC Group, Inc"; }
    function hasSources() { return true; }
    function hasViews() { return true; }
    function hasContent() { return true; }
    function supportsWorkflow() { return false; }
    function isSearchable() { return false; }


	public function install_tables() {
	    global $db;

		define("TMP_TABLE_EXISTED",		1);
		define("TMP_TABLE_INSTALLED",	2);
		define("TMP_TABLE_FAILED",		3);
		define("TMP_TABLE_ALTERED",		4);

		$dirs = array(
			BASE."datatypes/definitions",
			BASE."framework/core/database/definitions",
			);

		$tables = array();
		foreach ($dirs as $dir) {
			if (is_readable($dir)) {
				$dh = opendir($dir);
				while (($file = readdir($dh)) !== false) {
					if (is_readable("$dir/$file") && is_file("$dir/$file") && substr($file,-4,4) == ".php" && substr($file,-9,9) != ".info.php") {
						$tablename = substr($file,0,-4);
						$dd = include("$dir/$file");
						$info = null;
						if (is_readable("$dir/$tablename.info.php")) $info = include("$dir/$tablename.info.php");
						if (!$db->tableExists($tablename)) {
							foreach ($db->createTable($tablename,$dd,$info) as $key=>$status) {
								$tables[$key] = $status;
							}
						} else {
							foreach ($db->alterTable($tablename,$dd,$info) as $key=>$status) {
								if (isset($tables[$key])) echo "$tablename, $key<br>";
								if ($status == TABLE_ALTER_FAILED){
									$tables[$key] = $status;
								}else{
									$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
								}

							}
						}
					}
				}
			}
		}

		$newdef = BASE."framework/modules";

		if (is_readable($newdef)) {
			$dh = opendir($newdef);
			while (($file = readdir($dh)) !== false) {
				if (is_dir($newdef.'/'.$file) && ($file != '..' && $file != '.')) {
					$dirpath = $newdef.'/'.$file.'/definitions';
					if (file_exists($dirpath)) {
						$def_dir = opendir($dirpath);
						while (($def = readdir($def_dir)) !== false) {
							eDebug("$dirpath/$def");
							if (is_readable("$dirpath/$def") && is_file("$dirpath/$def") && substr($def,-4,4) == ".php" && substr($def,-9,9) != ".info.php") {
								$tablename = substr($def,0,-4);
								$dd = include("$dirpath/$def");
								$info = null;
								if (is_readable("$dirpath/$tablename.info.php")) $info = include("$dirpath/$tablename.info.php");
								if (!$db->tableExists($tablename)) {
									foreach ($db->createTable($tablename,$dd,$info) as $key=>$status) {
										$tables[$key] = $status;
									}
								} else {
									foreach ($db->alterTable($tablename,$dd,$info) as $key=>$status) {
										if (isset($tables[$key])) echo "$tablename, $key<br>";
										if ($status == TABLE_ALTER_FAILED){
											$tables[$key] = $status;
										}else{
											$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
										}

									}
								}
							}
						}
					}
				}
			}
		}
    	exponent_sessions_clearCurrentUserSessionCache();
		ksort($tables);
      assign_to_template(array('status'=>$tables));
	}

    public function manage_unused_tables() {
        global $db;
        
        expHistory::set('managable', $this->params);
        $unused_tables = array();
        $tables = $db->getTables();
        //eDebug($tables);
	    //FIXME Need to update for definitions moving into controller folder
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

	public function optimize_database() {
	    global $db;

		$before = $db->databaseInfo();
		foreach (array_keys($before) as $table) {
			$db->optimize($table);
		}
		$after = $db->databaseInfo();
	    assign_to_template(array('before'=>$before,'after'=>$after));
	}

	public function fix_sessions() {
	    global $db;

//		$test = $db->sql('CHECK TABLE '.DB_TABLE_PREFIX.'sessionticket');
		$fix = $db->sql('REPAIR TABLE '.DB_TABLE_PREFIX.'sessionticket');
		flash('message', 'Sessions Table was Repaired');
		expHistory::back();
	}


	public function fix_database() {
	    global $db;

	    print_r("<h1>Attempting to Fix the Exponent Database</h1>");
	    print_r("<h3>Some Error Conditions can NOT be repaired by this Procedure!</h3><br>");
		print_r("<pre>");
	// upgrade sectionref's that have lost their originals
		print_r("<b>Searching for sectionrefs that have lost their originals</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"is_original=0");
		if (count($sectionrefs)) {
			print_r("Found: ".count($sectionrefs)." copies (not originals)<br>");
		} else {
			print_r("None Found: Good!<br>");
		}
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
			// There is no original for this sectionref so change it to the original
				$sectionref->is_original = 1;
				$db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");

		print_r("<pre>");
	// upgrade sectionref's that point to missing sections (pages)
		print_r("<b>Searching for sectionrefs pointing to missing sections/pages <br>to fix for the Recycle Bin</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"refcount!=0");
		$found = 0;
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$db->updateObject($sectionref,"sectionref");
				print_r("Fixed: ".$sectionref->module." - ".$sectionref->source."<br>");
				$found += 1;
			}
		}
		if (!$found) {
			print_r("None Found: Good!<br>");
		}
		print_r("</pre>");

// FIXME Not needed when locationrefs are removed
//		 print_r("<pre>");
//	 // add missing locationref's based on existing sectionref's
//		 print_r("<b>Searching for detached modules with no original (no matching locationref)</b><br><br>");
//		 $sectionrefs = $db->selectObjects('sectionref',1);
//		 foreach ($sectionrefs as $sectionref) {
//			 if ($db->selectObject('locationref',"module='".$sectionref->module."' AND source='".$sectionref->source."'") == null) {
//			 // There is no locationref for sectionref.  Populate reference
//				 $newLocRef = null;
//				 $newLocRef->module   = $sectionref->module;
//				 $newLocRef->source   = $sectionref->source;
//				 $newLocRef->internal = $sectionref->internal;
//				 $newLocRef->refcount = $sectionref->refcount;
//				 $db->insertObject($newLocRef,'locationref');
//				 print_r("Copied: ".$sectionref->module." - ".$sectionref->source."<br>");
//			 }
//		 }
//		 print_r("</pre>");

		 print_r("<pre>");
	 // delete sectionref's & locationref's that have empty sources since they are dead
		 print_r("<b>Searching for unassigned modules (no source)</b><br><br>");
		 $sectionrefs = $db->selectObjects('sectionref','source=""');
		 if ($sectionrefs != null) {
			 print_r("Removing: ".count($sectionrefs)." empty sectionref's (no source)<br>");
			 $db->delete('sectionref','source=""');
		 } else {
			 print_r("No Empties Found: Good!<br>");
		 }
// FIXME Not needed when locationrefs are removed
//		 $locationrefs = $db->selectObjects('locationref','source=""');
//		 if ($locationrefs != null) {
//			 print_r("Removing: ".count($locationrefs)." empty locationref's (no source)<br>");
//			 $db->delete('locationref','source=""');
//		 } else {
//			 print_r("No Empties Found: Good!<br>");
//		 }
//		 print_r("</pre>");

		print_r("<pre>");
	// add missing sectionrefs based on existing containers (fixes aggregation problem)
		print_r("<b>Searching for missing sectionref's based on existing container's</b><br><br>");
		$containers = $db->selectObjects('container',1);
		foreach ($containers as $container) {
			$iloc = expUnserialize($container->internal);
			if ($db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."'") == null) {
			// There is no sectionref for this container.  Populate sectionref
				$newSecRef = null;
				$newSecRef->module   = $iloc->mod;
				$newSecRef->source   = $iloc->src;
				$newSecRef->internal = '';
				$newSecRef->refcount = 1;
				$newSecRef->is_original = 1;
				if ($container->external != "N;") {
					$eloc = expUnserialize($container->external);
					$section = $db->selectObject('sectionref',"module='containermodule' AND source='".$eloc->src."'");
					if (!empty($section)) {
						$newSecRef->section = $section->id;
						$db->insertObject($newSecRef,"sectionref");
						print_r("Missing sectionref for container replaced: ".$iloc->mod." - ".$iloc->src." - PageID #".$section->id."<br>");
					} else {
						print_r("Cant' find the container page for container: ".$iloc->mod." - ".$iloc->src."<br>");
					}
				}
			}
		}
		print_r("</pre>");
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
    
	public function toggle_dev() {
	    if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');
	    $value = (DEVELOPMENT == 1) ? 0 : 1;
	    exponent_config_change('DEVELOPMENT', $value);
	    exponent_theme_remove_css();
		$message = (DEVELOPMENT != 1) ? "Exponent is now in 'Development' mode" : "Exponent is no longer in 'Development' mode" ;
		flash('message',$message);
		expHistory::back();
	}

	public function toggle_maintenance() {
		if (!defined('SYS_CONFIG')) include_once(BASE.'subsystems/config.php');
		$value = (MAINTENANCE_MODE == 1) ? 0 : 1;
		exponent_config_change('MAINTENANCE_MODE', $value);
		MAINTENANCE_MODE == 1 ? flash('message',"Exponent is no longer in 'Maintenance' mode") : "" ;
		expHistory::back();
	}

	public function clear_smarty_cache() {
		exponent_theme_remove_smarty_cache();
		$message = "Smarty Cache has been cleared" ;
		flash('message',$message);
		expHistory::back();
	}

	public function clear_css_cache() {
		exponent_theme_remove_css();
		$message = "CSS/Minfy Cache has been cleared" ;
		flash('message',$message);
		expHistory::back();
	}

	public function clear_image_cache() {
		if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
//		exponent_files_remove_files_in_directory(BASE.'tmp/pixidou');  // alt location for pixidou cache
		exponent_files_remove_files_in_directory(BASE.'framework/modules/pixidou/images');  // location for pixidou cache
		// phpThumb cache includes subfolders
		if (file_exists(BASE.'tmp/img_cache')) exponent_files_remove_files_in_directory(BASE.'tmp/img_cache');
		$message = "Image/Pixidou Cache has been cleared" ;
		flash('message',$message);
		expHistory::back();
	}

	public function clear_rss_cache() {
		if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
		exponent_files_remove_files_in_directory(BASE.'tmp/rsscache');
		$message = "RSS/Podcast Cache has been cleared" ;
		flash('message',$message);
		expHistory::back();
	}

	public function clear_all_caches() {
		if (!defined('SYS_FILES')) include_once(BASE.'subsystems/files.php');
		exponent_theme_remove_smarty_cache();
		exponent_theme_remove_css();
//		exponent_files_remove_files_in_directory(BASE.'tmp/pixidou');  // alt location for pixidou cache
		exponent_files_remove_files_in_directory(BASE.'framework/modules/pixidou/images');  // location for pixidou cache
		if (file_exists(BASE.'tmp/img_cache')) exponent_files_remove_files_in_directory(BASE.'tmp/img_cache');
		exponent_files_remove_files_in_directory(BASE.'tmp/rsscache');
		$message = "All the System Caches have been cleared" ;
		flash('message',$message);
		expHistory::back();
	}

	public function upload_extension() {
		if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
		exponent_forms_initialize();
		$form = new form();
		$form->register(null,'',new htmlcontrol(exponent_core_maxUploadSizeMessage()));
		$form->register('mod_archive','Module Archive',new uploadcontrol());
		$form->register('submit','',new buttongroupcontrol('Install'));
		$form->meta('module','administration');
		$form->meta('action','install_extension');

		assign_to_template(array('form_html'=>$form->toHTML()));
	}

	public function install_extension() {

		$i18n = exponent_lang_loadFile('modules/administrationmodule/actions/install_extension.php');
		if ($_FILES['mod_archive']['error'] != UPLOAD_ERR_OK) {
			switch($_FILES['mod_archive']['error']) {
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					echo $i18n['file_too_large'].'<br />';
					break;
				case UPLOAD_ERR_PARTIAL:
					echo $i18n['partial_file'].'<br />';
					break;
				case UPLOAD_ERR_NO_FILE:
					echo $i18n['no_file'].'<br />';
					break;
			}
		} else {
			$basename = basename($_FILES['mod_archive']['name']);
			// Check future radio buttons
			// for now, try auto-detect
			$compression = null;
			$ext = '';
			if (substr($basename,-4,4) == '.tar') {
				$compression = null;
				$ext = '.tar';
			} else if (substr($basename,-7,7) == '.tar.gz') {
				$compression = 'gz';
				$ext = '.tar.gz';
			} else if (substr($basename,-4,4) == '.tgz') {
				$compression = 'gz';
				$ext = '.tgz';
			} else if (substr($basename,-8,8) == '.tar.bz2') {
				$compression = 'bz2';
				$ext = '.tar.bz2';
			} else if (substr($basename,-4,4) == '.zip') {
				$compression = 'zip';
				$ext = '.zip';
			}

			if ($ext == '') {
				echo $i18n['bad_archive'].'<br />';
			} else {
				if (!defined('SYS_FILES')) require_once(BASE.'subsystems/files.php');

				// Look for stale sessid directories:
				$sessid = session_id();
				if (file_exists(BASE."extensionuploads/$sessid") && is_dir(BASE."extensionuploads/$sessid")) exponent_files_removeDirectory("extensionuploads/$sessid");
				$return = exponent_files_makeDirectory("extensionuploads/$sessid");
				if ($return != SYS_FILES_SUCCESS) {
					switch ($return) {
						case SYS_FILES_FOUNDFILE:
						case SYS_FILES_FOUNDDIR:
							echo $i18n['file_in_parh'].'<br />';
							break;
						case SYS_FILES_NOTWRITABLE:
							echo $i18n['dest_not_w'].'<br />';
							break;
						case SYS_FILES_NOTREADABLE:
							echo $i18n['dest_not_r'].'<br />';
							break;
					}
				}

				$dest = BASE."extensionuploads/$sessid/archive$ext";
				move_uploaded_file($_FILES['mod_archive']['tmp_name'],$dest);

				if ($compression != 'zip') {// If not zip, must be tar
					include_once(BASE.'external/Tar.php');

					$tar = new Archive_Tar($dest,$compression);

					PEAR::setErrorHandling(PEAR_ERROR_PRINT);
					$return = $tar->extract(dirname($dest));
					if (!$return) {
						echo '<br />'.$i18n['error_tar'].'<br />';
					} else {
						header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=tar');
					}
				} else { // must be zip
					include_once(BASE.'external/Zip.php');

					$zip = new Archive_Zip($dest);

					PEAR::setErrorHandling(PEAR_ERROR_PRINT);
					if ($zip->extract(array('add_path'=>dirname($dest))) == 0) {
						echo '<br />'.$i18n['error_zip'].':<br />';
						echo $zip->_error_code . ' : ' . $zip->_error_string . '<br />';
					} else {
						header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=zip');
					}
				}
			}
		}
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
            exponent_config_change($key, $value);
        }
        
        flash('message', "Your Website Configuration has been updated");
        expHistory::back();
    }    
}

?>
