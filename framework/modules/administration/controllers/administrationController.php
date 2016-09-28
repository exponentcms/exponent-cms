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

/**
 * @subpackage Controllers
 * @package Modules
 */
/** @define "BASE" "../../../.." */

class administrationController extends expController {
    public $basemodel_name = 'expRecord';
    protected $manage_permissions = array(
        'change'=>'Change Settings',
	    'clear'=>'Clear Caches',  //FIXME this requires a logged in user to perform?
	    "fix"=>"Fix Database",
	    "install"=>"Installation",
        'mass'=>'Mass Mailing',
        'save'=>'Save Settings',
	    "theme"=>"Manage Themes",
	    'test_smtp'=>'Test SMTP Server Settings',
	    'toggle'=>'Toggle Settings',
//        'update'=>'Update Settings',
    );

    static function displayname() { return gt("Administration Controls"); }
    static function description() { return gt("This is the Administration Module"); }

    static function hasSources() {
        return false;
    }

	public function install_tables() {
		$tables = expDatabase::install_dbtables();
		ksort($tables);
        assign_to_template(array(
            'status'=>$tables
        ));
	}

    public function delete_unused_columns() {
   		$tables = expDatabase::install_dbtables(true);
   		ksort($tables);
        assign_to_template(array(
            'status'=>$tables
        ));
   	}

    public function manage_unused_tables() {
        global $db;

        expHistory::set('manageable', $this->params);
        $unused_tables = array();
        $used_tables = array();
        $tables = $db->getTables();
        //eDebug($tables);

		// first the core and 1.0 definitions
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
            BASE.'themes/'.DISPLAY_THEME_REAL.'/modules',  // we only want to do this for the set theme, NOT the preview theme
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
            $basename = strtolower(str_replace($db->prefix, '', $table));
            if (!in_array($basename, $used_tables) && !stristr($basename, 'forms')) {
                $unused_tables[$basename] = new stdClass();
                $unused_tables[$basename]->name = $table;
                $unused_tables[$basename]->rows = $db->countObjects($basename);
            }
        }

        assign_to_template(array(
            'unused_tables'=>$unused_tables
        ));
    }

    public function delete_unused_tables() {
        global $db;

        $count = 0;
        foreach($this->params['tables'] as $del=>$table) {
            $basename = str_replace($db->prefix, '', $table);
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
	    assign_to_template(array(
            'before'=>$before,
            'after'=>$after
        ));
	}

//	public function fix_sessions() {
//	    global $db;
//
////		$test = $db->sql('CHECK TABLE '.$db->prefix.'sessionticket');
//		$fix = $db->sql('REPAIR TABLE '.$db->prefix.'sessionticket');
//		flash('message', gt('Sessions Table was Repaired'));
//		expHistory::back();
//	}

	public function fix_database() {
	    global $db;

	// upgrade sectionref's that have lost their originals
//        $no_origs = array();
//		$sectionrefs = $db->selectObjects('sectionref',"is_original=0");
//		if (count($sectionrefs)) {
//			print_r(gt("Found").": ".count($sectionrefs)." ".gt("copies (not originals)")."<br>");
//            foreach ($sectionrefs as $sectionref) {
//                if ($db->selectObject('sectionref',"module='".$sectionref->module."' AND source='".$sectionref->source."' AND is_original='1'") == null) {
//                // There is no original for this sectionref so change it to the original
//                    $sectionref->is_original = 1;
//                    $db->updateObject($sectionref,"sectionref");
//                    $no_origs[] = gt("Fixed").": ".$sectionref->module." - ".$sectionref->source;
//                }
//            }
//		}
//        assign_to_template(array(
//            'no_origs'=>$no_origs,
//        ));

	// upgrade sectionref's that point to missing sections (pages)
		$sectionrefs = $db->selectObjects('sectionref',"refcount!=0");
		$no_sections = array();
		foreach ($sectionrefs as $sectionref) {
			if ($db->selectObject('section',"id='".$sectionref->section."'") == null) {
			// There is no section/page for sectionref so change the refcount
				$sectionref->refcount = 0;
				$db->updateObject($sectionref,"sectionref");
                $no_sections[] = gt("Fixed").": ".$sectionref->module." - ".$sectionref->source;
			}
		}
        assign_to_template(array(
            'no_sections'=>$no_sections,
        ));

	 // delete sectionref's that have empty sources since they are dead
		 $sectionrefs = $db->selectObjects('sectionref','source=""');
         $no_assigns = array();
		 if ($sectionrefs != null) {
             $no_assigns[] = gt("Removing").": ".count($sectionrefs)." ".gt("empty sectionrefs (no source)");
			 $db->delete('sectionref','source=""');
		 }
        assign_to_template(array(
            'no_assigns'=>$no_assigns,
        ));

	// add missing sectionrefs based on existing containers (fixes aggregation problem)
		$containers = $db->selectObjects('container',1);
        $missing_sectionrefs = array();
		foreach ($containers as $container) {
			$iloc = expUnserialize($container->internal);
			if ($db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."'") == null) {
			// There is no sectionref for this container.  Populate sectionref
                if ($container->external != "N;") {
                    $newSecRef = new stdClass();
                    $newSecRef->module   = $iloc->mod;
                    $newSecRef->source   = $iloc->src;
                    $newSecRef->internal = '';
                    $newSecRef->refcount = 1;
//                    $newSecRef->is_original = 1;
					$eloc = expUnserialize($container->external);
                    $section = $db->selectObject('sectionref',"module='container' AND source='".$eloc->src."'");
					if (!empty($section)) {
						$newSecRef->section = $section->id;
						$db->insertObject($newSecRef,"sectionref");
						$missing_sectionrefs[] = gt("Missing sectionref for container replaced").": ".$iloc->mod." - ".$iloc->src." - PageID #".$section->id;
					} else {
                        $db->delete('container','id="'.$container->id.'"');
                        $missing_sectionrefs[] = gt("Cant' find the container page for container").": ".$iloc->mod." - ".$iloc->src.' - '.gt('deleted');
					}
				}
			}
		}
        assign_to_template(array(
            'missing_sectionrefs'=>$missing_sectionrefs,
        ));
	}

    public function fix_tables() {
        $renamed = expDatabase::fix_table_names();
        assign_to_template(array(
            'tables'=>$renamed,
        ));
   	}

    public function install_ecommerce_tables() {
        global $db;

        $eql = BASE . "install/samples/ecommerce.eql";
        if (file_exists($eql)) {
            $errors = array();
            expFile::restoreDatabase($eql,$errors);
        } else {
            $errors = array(gt('The file does not exist'));
        }
        if (DEVELOPMENT && count($errors)) {
            $msg = gt('Errors were encountered importing the e-Commerce data.').'<ul>';
            foreach ($errors as $e) $msg .= '<li>'.$e.'</li>';
            $msg .= '</ul>';
            flash('error',$msg);
        } else {
            flash('message',gt('e-Commerce data was added to your database.'));
        }
        expHistory::back();
   	}

    public function toolbar() {
//        global $user;

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
                        if (empty($menu[substr($file,0,-4)])) unset($menu[substr($file,0,-4)]);
				    }
			    }
		    }
		}

        // sort the top level menus alphabetically by filename
		ksort($menu);
		$sorted = array();
		foreach($menu as $m) $sorted[] = $m;

        // slingbar position
        if (isset($_COOKIE['slingbar-top'])){
            $top = $_COOKIE['slingbar-top'];
        } else {
            $top = SLINGBAR_TOP;
        }

		assign_to_template(array(
            'menu'=>(bs3()) ? $sorted : json_encode($sorted),
            "top"=>$top
        ));
    }

//    public function index() {
//        redirect_to(array('controller'=>'administration', 'action'=>'toolbar'));
////        $this->toolbar();
//    }

    public function update_SetSlingbarPosition() {
        setcookie('slingbar-top', $this->params['top']);
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
        assign_to_template(array(
            'langs'=>$langs,
            'missing'=>$num_missing,
            "count"=>count($cur_lang),
            'untrans'=>$num_untrans
        ));
   	}

    public function update_language() {
        expSettings::change('LANGUAGE', $this->params['newlang']);
        flash('message',gt('Display Language changed to').": ".$this->params['newlang']);
        redirect_to(array('controller'=>'administration', 'action'=>'manage_lang'));
//        $this->manage_lang();
   	}

    public function manage_lang_await() {
        global $cur_lang;

        $awaiting_trans = array();
        foreach ($cur_lang as $key => $value) {
            if ($key == $value) {
                $awaiting_trans[$key] = stripslashes($value);
            }
        }
        assign_to_template(array(
            'await'=>$awaiting_trans
        ));
   	}

    public function save_newlangfile() {
		$result = expLang::createNewLangFile($this->params['newlang']);
        flash($result['type'],$result['message']);
        if ($result['type'] != 'error') {
            expSettings::change('LANGUAGE', $this->params['newlang']);
            expLang::createNewLangInfoFile($this->params['newlang'],$this->params['newauthor'],$this->params['newcharset'],$this->params['newlocale']);
            flash('message',gt('Display Language changed to').": ".$this->params['newlang']);
        }
        redirect_to(array('controller'=>'administration', 'action'=>'manage_lang'));
//        $this->manage_lang();
   	}

	public function test_smtp() {
		$smtp = new expMail();
		$smtp->test();
	}

    public function toggle_minify() {
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Minification settings.'));
        } else {
            $newvalue = (MINIFY == 1) ? 0 : 1;
            expSettings::change('MINIFY', $newvalue);
            $message = ($newvalue) ? gt("Exponent is now minifying Javascript and CSS") : gt(
                "Exponent is no longer minifying Javascript and CSS"
            );
            flash('message', $message);
        }
    	expHistory::back();
    }

	public function toggle_dev() {
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Error Reporting settings.'));
        } else {
            $newvalue = (DEVELOPMENT == 1) ? 0 : 1;
            expSettings::change('DEVELOPMENT', $newvalue);
//            expTheme::removeCss();
            expCSS::updateCoreCss();  // go ahead and rebuild the core .css files
            $message = ($newvalue) ? gt("Exponent is now in 'Development' mode") : gt(
                "Exponent is no longer in 'Development' mode"
            );
            flash('message', $message);
        }
		expHistory::back();
	}

    public function toggle_log() {
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Logging settings.'));
        } else {
            $newvalue = (LOGGER == 1) ? 0 : 1;
            expSettings::change('LOGGER', $newvalue);
        }
  		expHistory::back();
  	}

	public function toggle_maintenance() {
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Maintenance settings.'));
        } else {
            $newvalue = (MAINTENANCE_MODE == 1) ? 0 : 1;
            expSettings::change('MAINTENANCE_MODE', $newvalue);
            MAINTENANCE_MODE == 1 ? flash('message', gt("Exponent is no longer in 'Maintenance' mode")) : "";
        }
		expHistory::back();
	}

    public function toggle_workflow() {
        global $db;

        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Site Configuration settings.'));
        } else {
            $newvalue = (ENABLE_WORKFLOW == 1) ? 0 : 1;
            expSettings::change('ENABLE_WORKFLOW', $newvalue);
            $models = expModules::initializeModels();
            if ($newvalue) {
                // workflow is now turned on, initialize by approving all items
                expDatabase::install_dbtables(true, $newvalue);  // force a strict table update to add workflow columns
                foreach ($models as $modelname => $modelpath) {
                    $model = new $modelname();
                    if ($model->supports_revisions) {
                        $db->columnUpdate($model->tablename, 'approved', 1, 'approved=0');
                    }
                }
            } else {
                // workflow is now turned off, remove older revisions
                foreach ($models as $modelname => $modelpath) {
                    $model = new $modelname();
                    if ($model->supports_revisions) {
                        $items = $model->find();
                        foreach ($items as $item) {
                            $db->trim_revisions($model->tablename, $item->id, 1, $newvalue);
                        }
                    }
                }
                expDatabase::install_dbtables(
                    true,
                    $newvalue
                );  // force a strict table update to remove workflow columns
            }
            $message = ($newvalue) ? gt("Exponent 'Workflow' is now ON") : gt("Exponent 'Workflow' is now OFF");
            flash('message', $message);
        }
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
			expSession::set("uilevel",UILEVEL_PREVIEW);
		}
		$message = ($level == UILEVEL_PREVIEW) ? gt("Exponent is no longer in 'Preview' mode") : gt("Exponent is now in 'Preview' mode") ;
		flash('message',$message);
		expHistory::back();
	}

    public function manage_version() {
        expSession::un_set('update-check');  // reset the already checked flag
        if (!expVersion::checkVersion(true)) {
            flash('message', gt('Your version of Exponent CMS is current.'));
        }
   		expHistory::back();
   	}

	public function clear_smarty_cache() {
		expTheme::clearSmartyCache();
        expSession::clearAllUsersSessionCache();
    }

	public function clear_css_cache() {
		expTheme::removeCss();
        expSession::set('force_less_compile', 1);
        expCSS::updateCoreCss();  // go ahead and rebuild the core .css files
		flash('message',gt("CSS/Minify Cache has been cleared"));
		expHistory::back();
	}

	public function clear_image_cache() {
		expFile::removeFilesInDirectory(BASE.'tmp/pixidou');
		if (file_exists(BASE.'tmp/img_cache'))
            expFile::removeFilesInDirectory(BASE.'tmp/img_cache');
		flash('message',gt("Image/Pixidou Cache has been cleared"));
		expHistory::back();
	}

	public function clear_rss_cache() {
		expFile::removeFilesInDirectory(BASE.'tmp/rsscache');
		flash('message',gt("RSS/Podcast/Twitter Cache has been cleared"));
		expHistory::back();
	}

	public static function clear_all_caches() {
		expTheme::removeSmartyCache();
        expSession::clearAllUsersSessionCache();  // clear the session cache for true 'clear all'
        expHistory::flush();  // clear the history
        expSession::un_set('framework');
        expSession::un_set('display_theme');
        expSession::un_set('theme_style');
		expTheme::removeCss();
        expSession::set('force_less_compile', 1);
        expCSS::updateCoreCss();  // go ahead and rebuild the core .css files
		expFile::removeFilesInDirectory(BASE.'tmp/pixidou');
		if (file_exists(BASE.'tmp/img_cache'))
            expFile::removeFilesInDirectory(BASE.'tmp/img_cache');
        if (file_exists(BASE.'tmp/elfinder'))
              expFile::removeFilesInDirectory(BASE.'tmp/elfinder');
		if (file_exists(BASE.'tmp/extensionuploads'))
            expFile::removeFilesInDirectory(BASE.'tmp/extensionuploads');
		expFile::removeFilesInDirectory(BASE.'tmp/rsscache');
		flash('message',gt("All the System Caches have been cleared"));
		expHistory::back();
	}

	public function install_extension() {

		$modsurl =array(
			'themes'=>'http://www.exponentcms.org/rss/feed/title/exponentcms-themes',
			'fixes'=>'http://www.exponentcms.org/rss/feed/title/exponentcms-fixes',
			'mods'=>'http://www.exponentcms.org/rss/feed/title/exponentcms-mods'
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
                    $rssObject->length = $enclosure->get_length();
				}
		        $items[$type][] = $rssObject;
		    }
		}

//		$form = new form();
//        $form->meta('module','administration');
//        $form->meta('action','install_extension_confirm');
//		$form->register(null,'',new htmlcontrol(expCore::maxUploadSizeMessage()));
//		$form->register('mod_archive','Extension Archive',new uploadcontrol());
//        $form->register('patch',gt('Patch Exponent CMS or Install Theme?'),new checkboxcontrol(false,false),null,null,gt('All extensions are normally placed within the CURRENT theme (folder)'));
//        $form->register('submit','',new buttongroupcontrol(gt('Upload Extension')));

		assign_to_template(array(
            'themes'=>$items['themes'],
            'fixes'=>$items['fixes'],
            'mods'=>$items['mods'],
//            'form_html'=>$form->toHTML()
        ));
	}

	public function install_extension_confirm() {
        if (!empty($this->params['files'])) {
            foreach ($this->params['files'] as $title=>$url) {
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
						flash('error',gt('Error extracting ZIP archive').': '.$zip->_error_code . ' : ' . $zip->_error_string . '<br />');
					} else {
//						header('Location: ' . URL_FULL . 'index.php?module=administrationmodule&action=verify_extension&type=zip');
//						self::verify_extension('zip');
					}
				}
				$sessid = session_id();
				$files = array();
				foreach (expFile::listFlat(BASE.'tmp/extensionuploads/'.$sessid,true,null,array(),BASE.'tmp/extensionuploads/'.$sessid) as $key=>$f) {
					if ($key != '/archive.tar' && $key != '/archive.tar.gz' && $key != '/archive.tar.bz2' && $key != '/archive.zip') {
                        if (empty($this->params['patch']) || !$this->params['patch']) {
                            $key = substr($key,1);
                            if (substr($key,0,7)=='themes/') {
                                $parts = explode('/',$key);
                                $parts[1] = DISPLAY_THEME_REAL;
                                $file = implode('/',$parts);
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
				assign_to_template(array(
                    'relative'=>'tmp/extensionuploads/'.$sessid,
                    'files'=>$files,
                    'patch'=>empty($this->params['patch'])?0:$this->params['patch']
                ));
			}
		}
	}

	public function install_extension_finish() {
        $patch =$this->params['patch']==1;
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
            $tables = expDatabase::install_dbtables();
            ksort($tables);
            assign_to_template(array(
                'tables'=>$tables
            ));
			$nofiles = 0;
		}

		assign_to_template(array(
            'nofiles'=>$nofiles,
            'success'=>$success,
            'redirect'=>expHistory::getLastNotEditable()
        ));
	}

    public function mass_mail() {
        // nothing we need to do except display view
    }

    public function mass_mail_out() {
        global $user;

        $emaillist = array();
        if (!empty($this->params['allusers'])) {
            foreach (user::getAllUsers() as $u) {
                $emaillist[$u->email] = user::getUserAttribution($u->id);
            }
        } else {
            if(!empty($this->params['group_list'])) {
                foreach (listbuildercontrol::parseData($this->params,'grouplist') as $group_id) {
                   $grpusers = group::getUsersInGroup($group_id);
                   foreach ($grpusers as $u) {
                       $emaillist[$u->email] = user::getUserAttribution($u->id);
                   }
                }
            }
            if(!empty($this->params['user_list'])) {
                foreach (listbuildercontrol::parseData($this->params,'user_list') as $user_id) {
                    $u = user::getUserById($user_id);
                    $emaillist[$u->email] = user::getUserAttribution($u->id);
                }
            }
            if(!empty($this->params['address_list'])) {
                foreach (listbuildercontrol::parseData($this->params,'address_list') as $email) {
                    $emaillist[] = $email;
                }
            }
        }

        //This is an easy way to remove duplicates
        $emaillist = array_flip(array_flip($emaillist));
        $emaillist = array_map('trim', $emaillist);

        if (empty($emaillist)) {
            $post     = empty($_POST) ? array() : expString::sanitize($_POST);
            expValidator::failAndReturnToForm(gt('No Mailing Recipients Selected!'), $post);
        }
        if (empty($this->params['subject']) && empty($this->params['body']) && empty($_FILES['attach']['size'])) {
            $post     = empty($_POST) ? array() : expString::sanitize($_POST);
            expValidator::failAndReturnToForm(gt('Nothing to Send!'), $post);
        }

        $emailText = $this->params['body'];
		$emailText = trim(strip_tags(str_replace(array("<br />","<br>","br/>"),"\n",$emailText)));
		$emailHtml = $this->params['body'];

        $from = $user->email;
		if (empty($from)) {
			$from = trim(SMTP_FROMADDRESS);
		}
        $from_name = $user->firstname." ".$user->lastname." (".$user->username.")";
		if (empty($from_name)) {
			$from_name = trim(ORGANIZATION_NAME);
		}
        $subject = $this->params['subject'];
		if (empty($subject)) {
            $subject = gt('Email from') . ' ' . trim(ORGANIZATION_NAME);
		}
//        $headers = array(
//            "MIME-Version" => "1.0",
//            "Content-type" => "text/html; charset=" . LANG_CHARSET
//        );

        if (count($emaillist)) {
			$mail = new expMail();
            if (!empty($_FILES['attach']['size'])) {
                $dir = 'tmp';
                $filename = expFile::fixName(time() . '_' . $_FILES['attach']['name']);
                $dest = $dir . '/' . $filename;
                //Check to see if the directory exists.  If not, create the directory structure.
                if (!file_exists(BASE . $dir)) expFile::makeDirectory($dir);
                // Move the temporary uploaded file into the destination directory, and change the name.
                $file = expFile::moveUploadedFile($_FILES['attach']['tmp_name'], BASE . $dest);
//                $finfo = finfo_open(FILEINFO_MIME_TYPE);
//                $relpath = str_replace(PATH_RELATIVE, '', BASE);
//                $ftype = finfo_file($finfo, BASE.$dest);
//                finfo_close($finfo);
                if (!empty($file)) $mail->attach_file_on_disk(BASE . $file, expFile::getMimeType(BASE . $file));
            }
            if ($this->params['batchsend']) {
                $mail->quickBatchSend(array(
//                    	'headers'=>$headers,
                        'html_message'=>$emailHtml,
                        "text_message"=>$emailText,
                        'to'=>$emaillist,
                        'from'=>array(trim($from) => $from_name),
                        'subject'=>$subject,
                ));
            } else {
                $mail->quickSend(array(
//                    	'headers'=>$headers,
                        'html_message'=>$emailHtml,
                        "text_message"=>$emailText,
                        'to'=>$emaillist,
                        'from'=>array(trim($from)=>$from_name),
                        'subject'=>$subject,
                ));
            }
            if (!empty($file)) unlink(BASE . $file);  // delete temp file attachment
            flash('message',gt('Mass Email was sent'));
            expHistory::back();
        }
    }

    /**
     * feature to run upgrade scripts outside of installation
     *
     */
    public function install_upgrades() {
        //display the upgrade scripts
        if (is_readable(BASE.'install/upgrades')) {
            $i = 0;
            if (is_readable(BASE.'install/include/upgradescript.php')) include(BASE.'install/include/upgradescript.php');

            // first build a list of valid upgrade scripts
            $oldscripts = array(
                'install_tables.php',
                'convert_db_trim.php',
                'remove_exp1_faqmodule.php',
                'remove_locationref.php',
                'upgrade_attachableitem_tables.php',
            );
            $ext_dirs = array(
               BASE . 'install/upgrades',
                THEME_ABSOLUTE . 'modules/upgrades'
            );
            foreach ($ext_dirs as $dir) {
                if (is_readable($dir)) {
                    $dh = opendir($dir);
                    while (($file = readdir($dh)) !== false) {
                        if (is_readable($dir . '/' . $file) && is_file($dir . '/' . $file) && substr($file, -4, 4) == '.php'  && !in_array($file,$oldscripts)) {
                            include_once($dir . '/' . $file);
                            $classname     = substr($file, 0, -4);
                            /**
                             * Stores the upgradescript object
                             * @var \upgradescript $upgradescripts
                             * @name $upgradescripts
                             */
                             $upgradescripts[] = new $classname;
                        }
                    }
                }
            }
            //  next sort the list by priority
            usort($upgradescripts, array('upgradescript','prioritize'));

            //  next run through the list
            $db_version = expVersion::dbVersion();
            $upgrade_scripts = array();
            foreach ($upgradescripts as $upgradescript) {
                if ($upgradescript->checkVersion($db_version) && $upgradescript->needed()) {
                    $upgradescript->classname = get_class($upgradescript);
                    $upgrade_scripts[] = $upgradescript;
                    $i++;
                }
            }
        }
        assign_to_template(array(
            'scripts'=>$upgrade_scripts,
        ));
    }

    /**
     * run selected upgrade scripts outside of installation
     *
     */
    public function install_upgrades_run() {

        $tables = expDatabase::install_dbtables();
        ksort($tables);

        // locate the upgrade scripts
        $upgrade_dir = BASE.'install/upgrades';
        if (is_readable($upgrade_dir)) {
            $i = 0;
            if (is_readable(BASE.'install/include/upgradescript.php')) include(BASE.'install/include/upgradescript.php');
            $dh = opendir($upgrade_dir);

            // first build a list of valid upgrade scripts
            $oldscripts = array(
                'install_tables.php',
                'convert_db_trim.php',
                'remove_exp1_faqmodule.php',
                'remove_locationref.php',
                'upgrade_attachableitem_tables.php',
            );
            while (($file = readdir($dh)) !== false) {
                if (is_readable($upgrade_dir . '/' . $file) && is_file($upgrade_dir . '/' . $file) && substr($file, -4, 4) == '.php'  && !in_array($file,$oldscripts)) {
                    include_once($upgrade_dir . '/' . $file);
                    $classname     = substr($file, 0, -4);
                    /**
                     * Stores the upgradescript object
                     * @var \upgradescript $upgradescripts
                     * @name $upgradescripts
                     */
                    $upgradescripts[] = new $classname;
                }
            }
            //  next sort the list by priority
            usort($upgradescripts, array('upgradescript','prioritize'));

            //  next run through the list
            $db_version = expVersion::dbVersion();
            $upgrade_scripts = array();
            foreach ($upgradescripts as $upgradescript) {
                if ($upgradescript->checkVersion($db_version) && $upgradescript->needed()) {
                    if (!empty($this->params[get_class($upgradescript)])) {
                        $upgradescript->results = $upgradescript->upgrade();
                    }
                    $upgradescript->classname = get_class($upgradescript);
                    $upgrade_scripts[] = $upgradescript;
                    $i++;
                }
            }
        }
        assign_to_template(array(
            'scripts'=>$upgrade_scripts,
            'tables'=>$tables,
        ));
    }

    public function manage_themes() {
        expHistory::set('manageable', $this->params);

        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change the theme.'));
        }

    	$themes = array();
    	if (is_readable(BASE.'themes')) {
    		$dh = opendir(BASE.'themes');
    		while (($file = readdir($dh)) !== false) {
    			if ($file != '.' && $file != '..' && is_dir(BASE."themes/$file") && is_readable(BASE."themes/$file/class.php")) {
    				include_once(BASE."themes/$file/class.php");
    				$theme = new $file();
    				$t = new stdClass();
				    $t->user_configured = isset($theme->user_configured) ? $theme->user_configured : '';
                    $t->stock_theme = isset($theme->stock_theme) ? $theme->stock_theme : '';
    				$t->name = $theme->name();
    				$t->description = $theme->description();
    				$t->author = $theme->author();

				    $t->style_variations = array();
            		$sv = opendir(BASE.'themes/'.$file);
            		while (($s = readdir($sv)) !== false) {
                        if (substr($s,0,4) == "css_") {
                            $t->style_variations[str_replace("css_","",$s)] = str_replace("css_","",$s);
                        } elseif (substr($s,0,5) == "less_") {
                            $t->style_variations[str_replace("less_","",$s)] = str_replace("less_","",$s);
                        }
                    }
                    if(count($t->style_variations)>0){
                        $t->style_variations = array_merge(array('Default'=>'Default'),$t->style_variations);
                    }

    				$t->preview = is_readable(BASE."themes/$file/preview.jpg") ? PATH_RELATIVE . "themes/$file/preview.jpg" : YUI2_RELATIVE . "yui2-skin-sam-editor/assets/skins/sam/blankimage.png";
				    $t->mobile = is_readable(BASE."themes/$file/mobile/index.php") ? true : false;
    				$themes[$file] = $t;
    			}
    		}
    	}

        assign_to_template(array(
            'themes'=>$themes
        ));
    }

    public function theme_switch() {
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change the theme.'));
        }
    	expSettings::change('DISPLAY_THEME_REAL', $this->params['theme']);
	    expSession::set('display_theme',$this->params['theme']);
	    $sv = isset($this->params['sv'])?$this->params['sv']:'';
	    if (strtolower($sv)=='default') {
	       $sv = '';
	    }
	    expSettings::change('THEME_STYLE_REAL',$sv);
	    expSession::set('theme_style',$sv);
	    expDatabase::install_dbtables();  // update tables to include any custom definitions in the new theme

        // $message = (MINIFY != 1) ? "Exponent is now minifying Javascript and CSS" : "Exponent is no longer minifying Javascript and CSS" ;
        // flash('message',$message);
	    $message = gt("You have selected the")." '".$this->params['theme']."' ".gt("theme");
	    if ($sv != '') {
		    $message .= ' '.gt('with').' '.$this->params['sv'].' '.gt('style variation');
	    }
	    flash('message',$message);
//        expSession::un_set('framework');
        expSession::set('force_less_compile', 1);
//        expTheme::removeSmartyCache();
        expSession::clearAllUsersSessionCache();
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
//        expSession::un_set('framework');
        expSession::set('force_less_compile', 1);
//		expTheme::removeSmartyCache();
        expSession::clearAllUsersSessionCache();
		expHistory::back();
	}

	public function configure_theme() {
		if (is_readable(BASE."themes/".$this->params['theme']."/class.php")) {
			include_once(BASE."themes/".$this->params['theme']."/class.php");
            $themeclass = $this->params['theme'];
			$theme = new $themeclass($this->params);
			$theme->configureTheme();
		}
	}

	public function update_theme() {
		if (is_readable(BASE."themes/".$this->params['theme']."/class.php")) {
			include_once(BASE."themes/".$this->params['theme']."/class.php");
            $themeclass = $this->params['theme'];
			$theme = new $themeclass();
			$theme->saveThemeConfig($this->params);
            expSession::set('force_less_compile', 1);
            expTheme::removeSmartyCache();
            expSession::clearAllUsersSessionCache();
		}
	}

    public function export_theme() {
        include_once(BASE.'external/Tar.php');

        $themeclass = $this->params['theme'];
        $fname = tempnam(BASE.'/tmp','exporter_files_');
        $tar = new Archive_Tar($fname,'gz');
        $tar->createModify(BASE.'themes/'.$themeclass,'themes/',BASE.'themes/');

        $filename = preg_replace('/[^A-Za-z0-9_.-]/','-',$themeclass.'.tar.gz');

        ob_end_clean();
        // This code was lifted from phpMyAdmin, but this is Open Source, right?

        // 'application/octet-stream' is the registered IANA type but
        //        MSIE and Opera seems to prefer 'application/octetstream'
        $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octetstream' : 'application/octet-stream';

        header('Content-Type: ' . $mime_type);
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        // IE need specific headers
        if (EXPONENT_USER_BROWSER == 'IE') {
            header('Content-Disposition: inline; filename="' . $filename . '"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
        }

        $fh = fopen($fname,'rb');
        while (!feof($fh)) {
            echo fread($fh,8192);
        }
        fclose($fh);
        unlink($fname);

        exit(''); // Exit, since we are exporting.
    }

	public function togglemobile() {
		if (!expSession::is_set('mobile')) {  // account for FORCE_MOBILE initial state
			expSession::set('mobile',MOBILE);
		}
		expSession::set('mobile',!expSession::get('mobile'));
        expSession::set('force_less_compile', 1);
		expTheme::removeSmartyCache();
		expHistory::back();
	}

    public function configure_site () {
	    expHistory::set('manageable',$this->params);

        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Site Configuration settings.'));
        }

        // TYPES OF ANTISPAM CONTROLS... CURRENTLY ONLY ReCAPTCHA
        $as_types = array(
            '0'=>'-- '.gt('Please Select an Anti-Spam Control').' --',
            "recaptcha"=>'reCAPTCHA'
        );

        //THEMES FOR RECAPTCHA
        $as_themes = array(
            "light"=>gt('Light (Default)'),
        	"dark"=>gt('Dark'),
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

        // Available elFinder Themes
        $elf_themes = array(''=>gt('Default/OSX'));
        if (is_readable(BASE.'external/elFinder/themes')) {
        	$theme_dh = opendir(BASE.'external/elFinder/themes');
        	while (($theme_file = readdir($theme_dh)) !== false) {
        		if ($theme_file != '..' && is_readable(BASE.'external/elFinder/themes/'.$theme_file.'/css/theme.css')) {
                    $elf_themes['/themes/' . $theme_file] = ucwords($theme_file);
        		}
        	}
        }
        uasort($elf_themes,'strnatcmp');

        // Available Languages
	    $langs = expLang::langList();
//        ksort($langs);

        // smtp protocol
        $protocol = array(
            'ssl'=>'SSL',
            'tls'=>'TLS'
        );

        // Currency Format
        $currency = expSettings::dropdownData('currency');

        // attribution
//        $attribution = array(
//            'firstlast'=>'John Doe',
//            'lastfirst'=>'Doe, John',
//            'first'=>'John',
//            'last'=>'Doe',
//            'username'=>'jdoe'
//        );
        $attribution = expSettings::dropdownData('attribution');

        // These funcs need to be moved up in to new subsystems

        // Date/Time Format
        $datetime_format = expSettings::dropdownData('datetime_format');

        // Date Format
        $date_format = expSettings::dropdownData('date_format');

        // Time Format
        $time_format = expSettings::dropdownData('time_format');

        // Start of Week
//        $start_of_week = glist(expSettings::dropdownData('start_of_week'));
        $daysofweek = event::dayNames();
        $start_of_week = $daysofweek['long'];

        // File Permissions
        $file_permisions = glist(expSettings::dropdownData('file_permissions'));

        // File Permissions
        $dir_permissions = glist(expSettings::dropdownData('dir_permissions'));

        // Homepage Dropdown
        $section_dropdown = section::levelDropdownControlArray(0, 0, array(), false, 'view', true);

        // Timezone Dropdown
        $list = DateTimeZone::listAbbreviations();
        $idents = DateTimeZone::listIdentifiers();
        $data = $offset = $added = array();
        foreach ($list as $abbr => $info) {
            foreach ($info as $zone) {
                if (!empty($zone['timezone_id']) AND !in_array($zone['timezone_id'],$added) AND in_array($zone['timezone_id'],$idents)) {
                    try{
                        $z = new DateTimeZone($zone['timezone_id']);
                        $c = new DateTime(null, $z);
                        $zone['time'] = $c->format('H:i a');
                        $data[] = $zone;
                        $offset[] = $z->getOffset($c);
                        $added[] = $zone['timezone_id'];
                    } catch(Exception $e) {
                        flash('error', $e->getMessage());
                    }
                }
            }
        }

        array_multisort($offset, SORT_ASC, $data);
        $tzoptions = array();
        foreach ($data as $row) {
            $tzoptions[$row['timezone_id']] = self::formatOffset($row['offset'])
                                            . ' ' . $row['timezone_id'];
        }

        $expcat = new expCat();
        $cats = $expcat->find('all','module="file"');
        $folders = array();
        $folders[] = 'Root Folder';
        foreach ($cats as $cat) {
            $folders[$cat->id] = $cat->title;
        }

        // profiles
        $profiles = expSettings::profiles();
        if (empty($profiles)) {
            $profiles = array('' => '(default)');
        }

        assign_to_template(array(
            'as_types'=>$as_types,
            'as_themes'=>$as_themes,
            'themes'=>$themes,
            'elf_themes'=>$elf_themes,
            'langs'=>$langs,
            'protocol'=>$protocol,
            'currency'=>$currency,
            'attribution'=>$attribution,
            'datetime_format'=>$datetime_format,
            'date_format'=>$date_format,
            'time_format'=>$time_format,
            'start_of_week'=>$start_of_week,
            'timezones'=>$tzoptions,
            'file_permisions'=>$file_permisions,
            'dir_permissions'=>$dir_permissions,
            'section_dropdown'=>$section_dropdown,
            'folders'=>$folders,
            'profiles'=>$profiles
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
        if (!expUtil::isReallyWritable(BASE.'framework/conf/config.php')) {  // we can't write to the config.php file
            flash('error',gt('The file /framework/conf/config.php is NOT Writeable. You will be unable to change Site Configuration settings.'));
        } else {
            $this->params['sc']['MAINTENANCE_RETURN_TIME'] = yuicalendarcontrol::parseData('MAINTENANCE_RETURN_TIME', $this->params['sc']);
            foreach ($this->params['sc'] as $key => $value) {
//            expSettings::change($key, addslashes($value));
                expSettings::change($key, $value);
            }

            flash('message', gt("Your Website Configuration has been updated"));
        }
        setcookie('slingbar-top', $this->params['sc']['SLINGBAR_TOP']);
//        expHistory::back();
	    expHistory::returnTo('viewable');
    }

    public function change_profile() {
        if (empty($this->params['profile'])) return;
        expSession::un_set('display_theme');
        expSession::un_set('theme_style');
//        expSession::un_set('framework');
        expSession::set('force_less_compile', 1);
        expTheme::removeSmartyCache();
        expSession::clearAllUsersSessionCache();
        expSettings::activateProfile($this->params['profile']);
        flash('message', gt("New Configuration Profile Loaded") . ' (' . $this->params['profile'] . ')');
        redirect_to(array('controller'=>'administration', 'action'=>'configure_site'));
    }

    public function save_profile() {
        if (empty($this->params['profile'])) return;
        $profile = expSettings::createProfile($this->params['profile']);
        flash('message', gt("Configuration Profile Saved") . ' (' . $this->params['profile'] . ')');
//        redirect_to(array('controller'=>'administration', 'action'=>'configure_site'));
        redirect_to(array('controller'=>'administration', 'action'=>'change_profile', 'profile'=>$profile));
    }

    /**
   	 * Routine to force launching exponent installer
   	 */
   	public static function install_exponent() {
   		// we'll need the not_configured file to exist for install routine to work
//   		if (!@file_exists(BASE.'install/not_configured')) {
//   			$nc_file = fopen(BASE.'install/not_configured', "w");
//   			fclose($nc_file);
//   		}
//        $page = "";
//        if (@file_exists(BASE.'framework/conf/config.php')) {
//            $page = "?page=upgrade-1";
//        }
        if (@file_exists(BASE.'install/index.php')) {
            header('Location: ' . URL_FULL . 'install/index.php');
            exit('Redirecting to the Exponent Install Wizard');
        } else {
            flash('error', gt("Installation files were not found"));
        }
   	}

}

/**
 * This is the base theme class
 *
 * @subpackage Controllers
 * @package Modules
 */
class theme {
	public $user_configured = false;
    public $stock_theme = false;

	function name() { return "theme"; }
	function author() { return ""; }
	function description() { return gt("The theme shell"); }

    /**
     * @param array $params
     */
    function __construct($params = array()) {
        $this->params = $params;
    }

	/**
	 * Method to Configure theme settings
	 * This generic routine parses the theme's config.php file
	 * and presents the values as text boxes.
	 */
	function configureTheme () {
		if (isset($this->params['sv']) && $this->params['sv'] != '') {
			if (strtolower($this->params['sv'])=='default') {
                $this->params['sv']='';
			}
			$settings = expSettings::parseFile(BASE."themes/".$this->params['theme']."/config_".$this->params['sv'].".php");
		} else {
			$settings = expSettings::parseFile(BASE."themes/".$this->params['theme']."/config.php");
		}
		$form = new form();
		$form->meta('controller','administration');
		$form->meta('action','update_theme');
		$form->meta('theme',$this->params['theme']);
		$form->meta('sv',isset($this->params['sv'])?$this->params['sv']:'');
		foreach ($settings as $setting=>$key) {
			$form->register($setting,$setting.': ',new textcontrol($key,20));
		}
		$form->register(null,'',new htmlcontrol('<br>'));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		assign_to_template(array(
            'name'=>$this->name().(!empty($this->params['sv'])?' '.$this->params['sv']:''),
            'form_html'=>$form->toHTML()
        ));
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
		unset (
            $params['sv'],
            $params['controller'],
            $params['action'],
            $params['cid'],
            $params['scayt_verLang'],
            $params['slingbar-top'],
            $params['XDEBUG_SESSION']
        );
		foreach ($params as $key=>$value) {
			if ($key[0] == '_') {
				unset ($params[$key]);
			}
		}
		if (!empty($sv)) {
			expSettings::saveValues($params, BASE."themes/".$theme."/config_".$sv.".php");
		} else {
			expSettings::saveValues($params, BASE."themes/".$theme."/config.php");
		}
		expHistory::back();
	}

}

?>