<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

/**
 * @subpackage Controllers
 * @package Modules
 */
/** @define "BASE" "../../../.." */

class administrationController extends expController {
    public $basemodel_name = 'expRecord';
    public $useractions = array();
    public $add_permissions = array(
//	    'administrate'=>'Manage Administration', //FIXME is this used? old 1.0 permission
	    'clear'=>'Clear Caches',
	    "fix"=>"Fix Database",
	    "install"=>"Installation",
	    "theme"=>"Manage Themes",
	    'test_smtp'=>'Test SMTP Server Settings',
	    'toggle'=>'Toggle Settings',
    );

    function displayname() { return "Administration Controls"; }
    function description() { return "This is the Administration Module"; }
    function author() { return "OIC Group, Inc"; }

	public static function install_dbtables() {
	    global $db;

		define('TMP_TABLE_EXISTED',		1);
		define('TMP_TABLE_INSTALLED',	2);
		define('TMP_TABLE_FAILED',		3);
		define('TMP_TABLE_ALTERED',		4);

		expSession::clearCurrentUserSessionCache();
		$tables = array();

		// first the core and 1.0 definitions
		$coredefs = BASE.'framework/core/definitions';
		if (is_readable($coredefs)) {
			$dh = opendir($coredefs);
			while (($file = readdir($dh)) !== false) {
				if (is_readable("$coredefs/$file") && is_file("$coredefs/$file") && substr($file,-4,4) == ".php" && substr($file,-9,9) != ".info.php") {
					$tablename = substr($file,0,-4);
					$dd = include("$coredefs/$file");
					$info = null;
					if (is_readable("$coredefs/$tablename.info.php")) $info = include("$coredefs/$tablename.info.php");
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

		// then search for module definitions
		$moddefs = array(
			BASE.'themes/'.DISPLAY_THEME.'/modules',
			BASE."framework/modules",
			);
		foreach ($moddefs as $moddef) {
			if (is_readable($moddef)) {
				$dh = opendir($moddef);
				while (($file = readdir($dh)) !== false) {
					if (is_dir($moddef.'/'.$file) && ($file != '..' && $file != '.')) {
						$dirpath = $moddef.'/'.$file.'/definitions';
						if (file_exists($dirpath)) {
							$def_dir = opendir($dirpath);
							while (($def = readdir($def_dir)) !== false) {
	//							eDebug("$dirpath/$def");
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
		}
		return $tables;
	}

	public function install_tables() {
		$tables = self::install_dbtables();
		ksort($tables);
        assign_to_template(array('status'=>$tables));
	}

    public function manage_unused_tables() {
        global $db;
        
        expHistory::set('manageable', $this->params);
        $unused_tables = array();
        $used_tables = array();
        $tables = $db->getTables();
        //eDebug($tables);

		$coredefs = BASE.'framework/core/definitions';
		if (is_readable($coredefs)) {
			$dh = opendir($coredefs);
			while (($file = readdir($dh)) !== false) {
				if (is_readable("$coredefs/$file") && is_file("$coredefs/$file") && substr($file,-4,4) == ".php" && substr($file,-9,9) != ".info.php") {
					$used_tables[]= strtolower(substr($file,0,-4));
				}
			}
		}

	    // then search for module definitions
        $moddefs = array(
            BASE.'themes/'.DISPLAY_THEME_REAL.'/modules',
//  			BASE.'themes/'.DISPLAY_THEME.'/modules',
            BASE."framework/modules",
            );
        foreach ($moddefs as $moddef) {
            if (is_readable($moddef)) {
                $dh = opendir($moddef);
                while (($file = readdir($dh)) !== false) {
                    if (is_dir($moddef.'/'.$file) && ($file != '..' && $file != '.')) {
                        $dirpath = $moddef.'/'.$file.'/definitions';
                        if (file_exists($dirpath)) {
                            $def_dir = opendir($dirpath);
                            while (($def = readdir($def_dir)) !== false) {
                                if (is_readable("$dirpath/$def") && is_file("$dirpath/$def") && substr($def,-4,4) == ".php" && substr($def,-9,9) != ".info.php") {
                                    if ((!in_array(substr($def,0,-4), $used_tables))) {
                                        $used_tables[] = strtolower(substr($def,0,-4));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach($tables as $table) {
            $basename = strtolower(str_replace(DB_TABLE_PREFIX.'_', '', $table));
            if (!in_array($basename, $used_tables) && !stristr($basename, 'formbuilder')) {
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
        
        flash('message', gt('Deleted').' '.$count.' '.gt('unused tables').'.');
        expHistory::back();
    }

	public function fix_optimize_database() {
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
		flash('message', gt('Sessions Table was Repaired'));
		expHistory::back();
	}


	public function fix_database() {
	    global $db;

	    print_r("<h1>".gt("Attempting to Fix the Exponent Database")."</h1>");
	    print_r("<h3>".gt("Some Error Conditions can NOT be repaired by this Procedure")."!</h3><br>");
		print_r("<pre>");
	// upgrade sectionref's that have lost their originals
		print_r("<b>".gt("Searching for sectionrefs that have lost their originals")."</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"is_original=0");
		if (count($sectionrefs)) {
			print_r(gt("Found").": ".count($sectionrefs)." ".gt("copies (not originals)")."<br>");
		} else {
			print_r(gt("None Found: Good")."!<br>");
		}
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
			// There is no original for this sectionref so change it to the original
				$sectionref->is_original = 1;
				$db->updateObject($sectionref,"sectionref");
				print_r(gt("Fixed").": ".$sectionref->module." - ".$sectionref->source."<br>");
			}
		}
		print_r("</pre>");

		print_r("<pre>");
	// upgrade sectionref's that point to missing sections (pages)
		print_r("<b>".gt("Searching for sectionrefs pointing to missing sections/pages")." <br>".gt("to fix for the Recycle Bin")."</b><br><br>");
		$sectionrefs = $db->selectObjects('sectionref',"refcount!=0");
		$found = 0;
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$db->updateObject($sectionref,"sectionref");
				print_r(gt("Fixed").": ".$sectionref->module." - ".$sectionref->source."<br>");
				$found += 1;
			}
		}
		if (!$found) {
			print_r(gt("None Found: Good")."!<br>");
		}
		print_r("</pre>");

		 print_r("<pre>");
	 // delete sectionref's that have empty sources since they are dead
		 print_r("<b>".gt("Searching for unassigned modules (no source)")."</b><br><br>");
		 $sectionrefs = $db->selectObjects('sectionref','source=""');
		 if ($sectionrefs != null) {
			 print_r(gt("Removing").": ".count($sectionrefs)." ".gt("empty sectionrefs (no source)")."<br>");
			 $db->delete('sectionref','source=""');
		 } else {
			 print_r(gt("No Empties Found: Good")."!<br>");
		 }

		print_r("<pre>");
	// add missing sectionrefs based on existing containers (fixes aggregation problem)
		print_r("<b>".gt("Searching for missing sectionrefs based on existing containers")."</b><br><br>");
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
						print_r(gt("Missing sectionref for container replaced").": ".$iloc->mod." - ".$iloc->src." - PageID #".$section->id."<br>");
					} else {
						print_r(gt("Cant' find the container page for container").": ".$iloc->mod." - ".$iloc->src."<br>");
					}
				}
			}
		}
		print_r("</pre>");
	}

    public function toolbar() {
        global $user;
        $menu = array();
		$dirs = array(
			BASE.'framework/modules/administration/menus',
			BASE.'themes/'.DISPLAY_THEME.'/modules/administration/menus'
		);
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
        if (isset($_COOKIE['slingbar-top'])){
            $top = $_COOKIE['slingbar-top'];
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
        expHistory::back();
    }

    public function manage_lang() {
        global $default_lang, $cur_lang;

        // Available Languages
	    $langs = expLang::langList();
        $num_missing = 0;
        foreach ($default_lang as $key => $value) {
            if (!array_key_exists($key,$cur_lang)) $num_missing++;
        }
        $num_untrans = 0;
        foreach ($cur_lang as $key => $value) {
            if ($key == $value) $num_untrans++;
        }
        assign_to_template(array('langs'=>$langs,'missing'=>$num_missing,"count"=>count($cur_lang),'untrans'=>$num_untrans));
   	}

    public function update_language() {
        expSettings::change('LANGUAGE', $_POST['newlang']);
        flash('message',gt('Display Language changed to').": ".$_POST['newlang']);
        redirect_to(array('controller'=>'administration', 'action'=>'manage_lang'));
   	}

    public function manage_lang_await() {
        global $cur_lang;

        $awaiting_trans = array();
        foreach ($cur_lang as $key => $value) {
            if ($key == $value) {
                $awaiting_trans[$key] = stripslashes($value);
            }
        }
        assign_to_template(array('await'=>$awaiting_trans));
   	}

    public function save_newlangfile() {
		$result = expLang::createNewLangFile($_POST['newlang']);
        flash($result['type'],$result['message']);
        if ($result['type'] != 'error') {
            expSettings::change('LANGUAGE', $_POST['newlang']);
            expLang::createNewLangInfoFile($_POST['newlang'],$_POST['newauthor'],$_POST['newcharset'],$_POST['newlocale']);
            flash('message',gt('Display Language changed to').": ".$_POST['newlang']);
        }
        redirect_to(array('controller'=>'administration', 'action'=>'manage_lang'));
   	}

	public function test_smtp() {
		$smtp = new expMail();
		$smtp->test();
	}

    public function toggle_minify() {
    	$value = (MINIFY == 1) ? 0 : 1;
    	expSettings::change('MINIFY', $value);
    	$message = (MINIFY != 1) ? gt("Exponent is now minifying Javascript and CSS") : gt("Exponent is no longer minifying Javascript and CSS") ;
    	flash('message',$message);
    	expHistory::back();
    }
    
	public function toggle_dev() {
	    $value = (DEVELOPMENT == 1) ? 0 : 1;
	    expSettings::change('DEVELOPMENT', $value);
	    expTheme::removeCss();
		$message = (DEVELOPMENT != 1) ? gt("Exponent is now in 'Development' mode") : gt("Exponent is no longer in 'Development' mode") ;
		flash('message',$message);
		expHistory::back();
	}

	public function toggle_maintenance() {
		$value = (MAINTENANCE_MODE == 1) ? 0 : 1;
		expSettings::change('MAINTENANCE_MODE', $value);
		MAINTENANCE_MODE == 1 ? flash('message',gt("Exponent is no longer in 'Maintenance' mode")) : "" ;
		expHistory::back();
	}

	public function toggle_preview() {
		$level = 99;
		if (expSession::is_set('uilevel')) {
			$level = expSession::get('uilevel');
		}
		if ($level == UILEVEL_PREVIEW) {
			expSession::un_set('uilevel');
		} else { //edit mode
			expSession::set("uilevel",0);
		}
		$message = ($level == UILEVEL_PREVIEW) ? gt("Exponent is no longer in 'Preview' mode") : gt("Exponent is now in 'Preview' mode") ;
		flash('message',$message);
		expHistory::back();
	}

	public function clear_smarty_cache() {
		expTheme::clearSmartyCache();
	}

	public function clear_css_cache() {
		expTheme::removeCss();
		flash('message',gt("CSS/Minfy Cache has been cleared"));
		expHistory::back();
	}

	public function clear_image_cache() {
		expFile::removeFilesInDirectory(BASE.'tmp/pixidou');
		if (file_exists(BASE.'tmp/img_cache')) expFile::removeFilesInDirectory(BASE.'tmp/img_cache');
		flash('message',gt("Image/Pixidou Cache has been cleared"));
		expHistory::back();
	}

	public function clear_rss_cache() {
		expFile::removeFilesInDirectory(BASE.'tmp/rsscache');
		flash('message',gt("RSS/Podcast Cache has been cleared"));
		expHistory::back();
	}

	public function clear_all_caches() {
		expTheme::removeSmartyCache();
		expTheme::removeCss();
		expFile::removeFilesInDirectory(BASE.'tmp/pixidou');
		if (file_exists(BASE.'tmp/img_cache')) expFile::removeFilesInDirectory(BASE.'tmp/img_cache');
		if (file_exists(BASE.'tmp/extensionuploads')) expFile::removeFilesInDirectory(BASE.'tmp/extensionuploads');
		expFile::removeFilesInDirectory(BASE.'tmp/rsscache');
		flash('message',gt("All the System Caches have been cleared"));
		expHistory::back();
	}

	public function install_extension() {

		$modsurl =array(
			'themes'=>'http://www.exponentcms.org/site_rss.php?module=filedownload&src=%40random4e6a70cebdc96',
			'fixes'=>'http://www.exponentcms.org/site_rss.php?module=filedownload&src=%40random4e6a710126abf',
			'mods'=>'http://www.exponentcms.org/site_rss.php?module=filedownload&src=%40random4e6a7148c84a9'
		);

		$RSS = new SimplePie();
		$RSS->set_cache_location(BASE.'tmp/rsscache');  // default is ./cache
//	        $RSS->set_cache_duration(3600);  // default if 3600
		$RSS->set_timeout(20);  // default is 10
//	        $RSS->set_output_encoding('UTF-8');  // which is the default
		$items['themes'] = array();
		$items['fixes'] = array();
		$items['mods'] = array();
		foreach($modsurl as $type=>$url) {
		    $RSS->set_feed_url($url);
		    $feed = $RSS->init();
		    if (!$feed) {
		        // an error occurred in the rss.
		        continue;
		    }
			$RSS->handle_content_type();
		    foreach ($RSS->get_items() as $rssItem) {
		        $rssObject = new stdClass();
		        $rssObject->title = $rssItem->get_title();
		        $rssObject->body = $rssItem->get_description();
		        $rssObject->rss_link = $rssItem->get_permalink();
		        $rssObject->publish = $rssItem->get_date('U');
		        $rssObject->publish_date = $rssItem->get_date('U');
				foreach ($rssItem->get_enclosures() as $enclosure) {
					$rssObject->enclosure = $enclosure->get_link();
				}
		        $items[$type][] = $rssObject;
		    }
		}

		$form = new form();
		$form->register(null,'',new htmlcontrol(expCore::maxUploadSizeMessage()));
		$form->register('mod_archive','Extension Archive',new uploadcontrol());
        $form->register('patch',gt('Patch Exponent CMS?'),new checkboxcontrol(null,false));
        $form->register('submit','',new buttongroupcontrol(gt('Upload Extension')));
		$form->meta('module','administration');
		$form->meta('action','install_extension_confirm');

		assign_to_template(array('themes'=>$items['themes'],'fixes'=>$items['fixes'],'mods'=>$items['mods'],'form_html'=>$form->toHTML()));
	}

	public function install_extension_confirm() {
        if (!empty($_POST['files'])) {
            foreach ($_POST['files'] as $title=>$url) {
                $filename = tempnam("tmp/extensionuploads/",'tmp');
                expCore::saveData($url,$filename);
                $_FILES['mod_archive']['name'] = end(explode("/", $url));
//                $finfo = finfo_open(FILEINFO_MIME);
//                $mimetype = finfo_file($finfo, $filename);
//                finfo_close($finfo);
//                $_FILES['mod_archive']['type'] = $mimetype;
                $_FILES['mod_archive']['tmp_name'] = $filename;
                $_FILES['mod_archive']['error'] = 0;
                $_FILES['mod_archive']['size'] = filesize($filename);
            }
        }
        if ($_FILES['mod_archive']['error'] != UPLOAD_ERR_OK) {
			switch($_FILES['mod_archive']['error']) {
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					flash('error', gt('The file you uploaded exceeded the size limits for the server.'));
					break;
				case UPLOAD_ERR_PARTIAL:
					flash('error', gt('The file you uploaded was only partially uploaded.'));
					break;
				case UPLOAD_ERR_NO_FILE:
					flash('error', gt('No file was uploaded.'));
					break;
			}
		} else {
			$basename = basename($_FILES['mod_archive']['name']);
			// Check future radio buttons; for now, try auto-detect
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
				flash('error', gt('Unknown archive format. Archives must either be regular ZIP files, TAR files, Gzipped Tarballs, or Bzipped Tarballs.'));
			} else {

				// Look for stale sessid directories:
				$sessid = session_id();
				if (file_exists(BASE."tmp/extensionuploads/$sessid") && is_dir(BASE."tmp/extensionuploads/$sessid")) expFile::removeDirectory("tmp/extensionuploads/$sessid");
				$return = expFile::makeDirectory("tmp/extensionuploads/$sessid");
				if ($return != SYS_FILES_SUCCESS) {
					switch ($return) {
						case SYS_FILES_FOUNDFILE:
						case SYS_FILES_FOUNDDIR:
							flash('error', gt('Found a file in the directory path when creating the directory to store the files in.'));
							break;
						case SYS_FILES_NOTWRITABLE:
							flash('error', gt('Destination parent is not writable.'));
							break;
						case SYS_FILES_NOTREADABLE:
							flash('error', gt('Destination parent is not readable.'));
							break;
					}
				}

				$dest = BASE."tmp/extensionuploads/$sessid/archive$ext";
                if (is_uploaded_file($_FILES['mod_archive']['tmp_name'])) {
				    move_uploaded_file($_FILES['mod_archive']['tmp_name'],$dest);
                } else {
                    rename($_FILES['mod_archive']['tmp_name'],$dest);
                }

				if ($compression != 'zip') {// If not zip, must be tar
					include_once(BASE.'external/Tar.php');

					$tar = new Archive_Tar($dest,$compression);

					PEAR::setErrorHandling(PEAR_ERROR_PRINT);
					$return = $tar->extract(dirname($dest));
					if (!$return) {
						flash('error',gt('Error extracting TAR archive'));
					} else {
//						header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=tar');
//						self::verify_extension('tar');
					}
				} else { // must be zip
					include_once(BASE.'external/Zip.php');

					$zip = new Archive_Zip($dest);

					PEAR::setErrorHandling(PEAR_ERROR_PRINT);
					if ($zip->extract(array('add_path'=>dirname($dest))) == 0) {
						flash('error',gt('Error extracting ZIP archive: ').$zip->_error_code . ' : ' . $zip->_error_string . '<br />');
					} else {
//						header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=zip');
//						self::verify_extension('zip');
					}
				}
				$sessid = session_id();
				$files = array();
				foreach (expFile::listFlat(BASE.'tmp/extensionuploads/'.$sessid,true,null,array(),BASE.'tmp/extensionuploads/'.$sessid) as $key=>$f) {
					if ($key != '/archive.tar' && $key != '/archive.tar.gz' && $key != '/archive.tar.bz2' && $key != '/archive.zip') {
                        if (empty($_POST['patch']) || !$_POST['patch']) {
                            $key = substr($key,1);
                            if (substr($key,0,7)=='themes/') {
                                $parts = explode('/',$key);
                                $parts[1] = DISPLAY_THEME_REAL;
                                $file = implode('/',$parts);
                                $file = $file;
                            } else {
                                $file = 'themes/'.DISPLAY_THEME_REAL.'/'.str_replace("framework/", "", $key);
                            }
                            $file = str_replace("modules-1", "modules", $file);
                        } else {
                            $file = substr($key,1);
                        }
						$files[] = array(
							'absolute'=>$file,
							'relative'=>$f,
							'canCreate'=>expFile::canCreate(BASE.$file,1),
							'ext'=>substr($f,-3,3)
						);
					}
				}
				assign_to_template(array('relative'=>'tmp/extensionuploads/'.$sessid,'files'=>$files,'patch'=>empty($_POST['patch'])?0:$_POST['patch']));
			}
		}
	}

	public function install_extension_finish() {
        $patch =$_GET['patch']==1;
		$sessid = session_id();
		if (!file_exists(BASE."tmp/extensionuploads/$sessid") || !is_dir(BASE."tmp/extensionuploads/$sessid")) {
			$nofiles = 1;
		} else {
			$success = array();
			foreach (array_keys(expFile::listFlat(BASE."tmp/extensionuploads/$sessid",true,null,array(),BASE."tmp/extensionuploads/$sessid")) as $file) {
				if ($file != '/archive.tar' && $file != '/archive.tar.gz' && $file != 'archive.tar.bz2' && $file != '/archive.zip') {
                    if ($patch) {  // this is a patch/fix extension
                        expFile::makeDirectory(dirname($file));
                        $success[$file] = copy(BASE."tmp/extensionuploads/$sessid".$file,BASE.substr($file,1));
                        if (basename($file) == 'views_c') chmod(BASE.substr($file,1),0777);
                    } else {
                        $newfile = substr($file,1);
                        if (substr($newfile,0,7)=='themes/') {  // this is a theme extension
                            $parts = explode('/',$newfile);
                            $parts[1] = DISPLAY_THEME_REAL;
                            $newfile = implode('/',$parts);
                        } else {  // this is a mod extension
                            $newfile = str_replace("framework/", "", $newfile);
                            $newfile = 'themes/'.DISPLAY_THEME_REAL.'/'.str_replace("modules-1", "modules", $newfile);
                        }
                        expFile::makeDirectory(dirname($newfile));
                        $success[$newfile] = copy(BASE."tmp/extensionuploads/$sessid".$file,BASE.$newfile);
                    }
				}
			}

			$del_return = expFile::removeDirectory(BASE."tmp/extensionuploads/$sessid");  //FIXME shouldn't use echo
//			echo $del_return;
            $tables = self::install_dbtables();
            ksort($tables);
            assign_to_template(array('tables'=>$tables));
			$nofiles = 0;
		}

		assign_to_template(array('nofiles'=>$nofiles,'success'=>$success,'redirect'=>expHistory::getLastNotEditable()));
	}

    public function manage_themes() {
        expHistory::set('manageable', $this->params);
    	$themes = array();
    	if (is_readable(BASE.'themes')) {
    		$dh = opendir(BASE.'themes');
    		while (($file = readdir($dh)) !== false) {
    			if (is_readable(BASE."themes/$file/class.php")) {
    				include_once(BASE."themes/$file/class.php");
    				$theme = new $file();
    				$t = null;
				    $t->user_configured = isset($theme->user_configured) ? $theme->user_configured : '';
    				$t->name = $theme->name();
    				$t->description = $theme->description();
    				$t->author = $theme->author();

				    $t->style_variations = array();
            		$sv = opendir(BASE.'themes/'.$file);
            		while (($s = readdir($sv)) !== false) {
                        if (substr($s,0,4) == "css_") {
                            $t->style_variations[str_replace("css_","",$s)] = str_replace("css_","",$s);
                        }
                    }
                    if(count($t->style_variations)>0){
                        $t->style_variations = array_merge(array('Default'=>'Default'),$t->style_variations);
                    }

    				$t->preview = is_readable(BASE."themes/$file/preview.jpg") ? "themes/$file/preview.jpg" : "themes/" . DISPLAY_THEME . "/noprev.jpg";
				    $t->mobile = is_readable(BASE."themes/$file/mobile/index.php") ? true : false;
    				$themes[$file] = $t;
    			}
    		}
    	}

        assign_to_template(array('themes'=>$themes));
    }
    
    public function theme_switch() {
    	expSettings::change('DISPLAY_THEME_REAL', $this->params['theme']);
	    expSession::set('display_theme',$this->params['theme']);
	    $sv = isset($this->params['sv'])?$this->params['sv']:'';
	    if (strtolower($sv)=='default') {
	       $sv = '';
	    }
	    expSettings::change('THEME_STYLE_REAL',$sv);
	    expSession::set('theme_style',$sv);
	    self::install_dbtables();  // update tables to include any custom definitions in the new theme

        // $message = (MINIFY != 1) ? "Exponent is now minifying Javascript and CSS" : "Exponent is no longer minifying Javascript and CSS" ;
        // flash('message',$message);
	    $message = gt("You have selected the")." '".$this->params['theme']."' ".gt("theme");
	    if ($sv != '') {
		    $message .= ' '.gt('with').' '.$this->params['sv'].' '.gt('style variation');
	    }
	    flash('message',$message);
    	expHistory::returnTo('manageable');
    }	
    
	public function theme_preview() {
		expSession::set('display_theme',$this->params['theme']);
		$sv = isset($this->params['sv'])?$this->params['sv']:'';
		if (strtolower($sv)=='default') {
		   $sv = '';
		}
		expSession::set('theme_style',$sv);
		$message = gt("You are previewing the")." '".$this->params['theme']."' ".gt("theme");
		if ($sv) {
			$message .= ' with '.$sv.' style variation';
		}
		if ($this->params['theme'] != DISPLAY_THEME_REAL || $this->params['sv'] != THEME_STYLE_REAL) {
			flash('notice',$message);
		}
		expTheme::removeSmartyCache();
		expHistory::back();
	}

	public function configure_theme() {
		if (is_readable(BASE."themes/".$this->params['theme']."/class.php")) {
			include_once(BASE."themes/".$this->params['theme']."/class.php");
			$theme = new $this->params['theme']();
			$theme->configureTheme();
		}
	}

	public function update_theme() {
		if (is_readable(BASE."themes/".$this->params['theme']."/class.php")) {
			include_once(BASE."themes/".$this->params['theme']."/class.php");
			$theme = new $this->params['theme']();
			$theme->saveThemeConfig($this->params);
		}
	}

	public function toggle_mobile() {
		if (!expSession::is_set('mobile')) {  // account for FORCE_MOBILE initial state
			expSession::set('mobile',MOBILE);
		}
		expSession::set('mobile',!expSession::get('mobile'));
		expTheme::removeSmartyCache();
		expHistory::back();
	}

    public function configure_site () {
	    expHistory::set('manageable',$this->params);

        // TYPES OF ANTISPAM CONTROLS... CURRENTLY ONLY ReCAPTCHA
        $as_types = array(
            '0'=>'-- '.gt('Please Select an Anti-Spam Control').' --',
            "recaptcha"=>'reCAPTCHA'
        );
        
        //THEMES FOR RECAPTCHA
        $as_themes = array(
            "red"=>gt('DEFAULT RED'),
        	"white"=>gt('White'),
        	"blackglass"=>gt('Black Glass'),
        	"clean"=>gt('Clean (very generic)'),
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
	    $langs = expLang::langList();
//        ksort($langs);

        // smtp protocol
        $protocol = array('ssl'=>'SSL','tls'=>'TLS');

        // attribution
        $attribution = array('firstlast'=>'John Doe','lastfirst'=>'Doe, John','first'=>'John','username'=>'jdoe');
        
        // These funcs need to be moved up in to new subsystems
        
        // Date/Time Format
        $datetime_format = expSettings::dropdownData('datetime_format');

        // Date Format
        $date_format = expSettings::dropdownData('date_format');
        
        // Time Format
        $time_format = expSettings::dropdownData('time_format');
        
        // Start of Week
        $start_of_week = expSettings::dropdownData('start_of_week');

        // File Permissions
        $file_permisions = expSettings::dropdownData('file_permissions');
        
        // File Permissions
        $dir_permissions = expSettings::dropdownData('dir_permissions');

        // Homepage Dropdown
        $section_dropdown = navigationmodule::levelDropDownControlArray(0);

        // Timezone Dropdown
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();
        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if ( ! empty($zone['timezone_id'])
                    AND
                    ! in_array($zone['timezone_id'], $added)
                    AND
                      in_array($zone['timezone_id'], $idents)) {
                    $z = new DateTimeZone($zone['timezone_id']);
                    $c = new DateTime(null, $z);
                    $zone['time'] = $c->format('H:i a');
                    $data[] = $zone;
                    $offset[] = $z->getOffset($c);
                    $added[] = $zone['timezone_id'];
                }
            }
        }

        array_multisort($offset, SORT_ASC, $data);
        $tzoptions = array();
        foreach ($data as $key => $row) {
            $tzoptions[$row['timezone_id']] = self::formatOffset($row['offset'])
                                            . ' ' . $row['timezone_id'];
        }

        assign_to_template(array('as_types'=>$as_types,
                                'as_themes'=>$as_themes,
                                'themes'=>$themes,
                                'langs'=>$langs,
                                'protocol'=>$protocol,
                                'attribution'=>$attribution,
                                'datetime_format'=>$datetime_format,
                                'date_format'=>$date_format,
                                'time_format'=>$time_format,
                                'start_of_week'=>$start_of_week,
                                'timezones'=>$tzoptions,
                                'file_permisions'=>$file_permisions,
                                'dir_permissions'=>$dir_permissions,
                                'section_dropdown'=>$section_dropdown
                                ));
    }

	// now you can use $options;
	private function formatOffset($offset) {
			$hours = $offset / 3600;
			$remainder = $offset % 3600;
			$sign = $hours > 0 ? '+' : '-';
			$hour = (int) abs($hours);
			$minutes = (int) abs($remainder / 60);

			if ($hour == 0 AND $minutes == 0) {
				$sign = ' ';
			}
			return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
					.':'. str_pad($minutes,2, '0');

	}

    public function update_siteconfig () {
        foreach ($this->params['sc'] as $key => $value) {
            expSettings::change($key, addslashes($value));
        }
        
        flash('message', gt("Your Website Configuration has been updated"));
//        expHistory::back();
	    expHistory::returnTo('viewable');
    }

    /**
   	 * Routine to force launching exponent installer
   	 */
   	public static function install_exponent() {
           //FIXME in 2.0.4 we'll add a routine to simply display a flash message with a link to this method
   		// we'll need the not_configured file to exist for install routine to work
   		if (!@file_exists(BASE.'install/not_configured')) {
   			$nc_file = fopen(BASE.'install/not_configured', "w");
   			fclose($nc_file);
   		}
           $page = "";
           if (@file_exists(BASE.'conf/config.php')) {
               $page = "?page=upgrade-1";
           }
   		header('Location: '.URL_FULL.'install/index.php'.$page);
   		exit('Redirecting to the Exponent Install Wizard');
   	}

}

/**
 * This is the base theme class
 *
 * @subpackage Core-Controllers
 * @package Modules
 */
class theme {
	public $user_configured = false;

	function name() { return "theme"; }
	function author() { return ""; }
	function description() { return gt("The theme shell"); }

	/**
	 * Method to Configure theme settings
	 * This generic routine parses the theme's config.php file
	 * and presents the values as text boxes.
	 */
	function configureTheme () {
		if (isset($_GET['sv']) && $_GET['sv'] != '') {
			if (strtolower($_GET['sv'])=='default') {
			   $_GET['sv']='';
			}
			$settings = expSettings::parseFile(BASE."themes/".$_GET['theme']."/config_".$_GET['sv'].".php");
		} else {
			$settings = expSettings::parseFile(BASE."themes/".$_GET['theme']."/config.php");
		}
		$form = new form();
		$form->meta('controller','administration');
		$form->meta('action','update_theme');
		$form->meta('theme',$_GET['theme']);
		$form->meta('sv',isset($_GET['sv'])?$_GET['sv']:'');
		foreach ($settings as $setting=>$key) {
			$form->register($setting,$setting.': ',new textcontrol($key,20));
		}
		$form->register(null,'',new htmlcontrol('<br>'));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		assign_to_template(array('name'=>self::name(),'form_html'=>$form->tohtml()));
	}

	/**
	 * Method to save/update theme settings
	 * This generic routine parses the passed params
	 * and saves them to the theme's config.php file
	 * It attempts to remove non-theme params such as analytics, etc..
	 *
	 * @param $params theme configuration parameters
	 */
	function saveThemeConfig ($params) {
		$theme = $params['theme'];
		unset ($params['theme']);
		$sv = $params['sv'];
		if (strtolower($sv)=='default') {
		   $sv='';
		}
		unset ($params['sv']);
		unset ($params['controller']);
		unset ($params['action']);
        unset ($params['cid']);
        unset ($params['scayt_verLang']);
        unset ($params['slingbar-top']);
        unset ($params['XDEBUG_SESSION']);
		foreach ($params as $key=>$value) {
			if ($key[0] == '_') {
				unset ($params[$key]);
			}
		}
		if ($sv != '') {
			expSettings::saveValues($params, BASE."themes/".$theme."/config_".$sv.".php");
		} else {
			expSettings::saveValues($params, BASE."themes/".$theme."/config.php");
		}
		expHistory::back();
	}

}

?>