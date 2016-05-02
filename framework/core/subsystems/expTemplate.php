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
* This is the class expTemplate...template support methods
*
 * @package    Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expTemplate {

	/**
	 * Retrieve Module-Independent View File
	 *
	 * Looks in the theme and the /views directory for a .tpl file
	 * corresponding to the passed view.
	 *
	 * @param string $type One of "modules"", "controls"", "forms" or ""
	 * @param string $name The name the object we are requesting a view from
	 * @param string $view The name of the requested view
	 *
	 * @return string The full filepath of the view template
	 */
	public static function getViewFile($type="", $name="", $view="Default") {  //FIXME only called from basetemplate->_construct() NOT controllertemplate
		$viewfilepath = self::resolveFilePaths($type, $name, "tpl", $view); //FIXME only place this method is called, move to this subsystem?
		// Something is really screwed up.
		if ($viewfilepath == false) {
			// Fall back to something that won't error.
            return TEMPLATE_FALLBACK_VIEW;
		}
		//return first match
		return array_shift($viewfilepath);
	}

    /**
     * @deprecated 2.2.0 backward compatibility wrapper
     */
	public static function getModuleViewFile($name, $view, $recurse=true) {  //FIXME Not Used 2.2???
		return self::getViewFile("modules", $name, $view);
	}

    /** exdoc
     * @deprecated 2.2.0 backward compatibility wrapper
     */
    public static function getViewConfigForm($module,$view,$form,$values) {  //FIXME Not Used 2.2???
		$form_file = "";
		$resolved_path = null;
		$resolved_path = self::resolveFilePaths("modules", $module , "form" , $view);
		if (isset($resolved_path) && $resolved_path != '') {
            $tmppath = self::resolveFilePaths("modules", $module , "form" , $view);
			$filepath = array_shift($tmppath);
		} else {
			$filepath = false;
		}

		if ($filepath != false) {
			$form_file = $filepath;
		}

		if ($form == null) $form = new form();
		if ($form_file == "") return $form;

//		$form->register(null,"",new htmlcontrol("<hr size='1' /><b>".gt('Layout Configuration')."</b>"));
        $form->register(null,"",new htmlcontrol("<h2>".gt('Layout Configuration')."</h2>"),true,array('description'=>ucwords($view).' '.gt('View Configuration')));

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
		$fh = fopen($form_file,"r");
		while (($control_data = fgetcsv($fh,65536,"\t")) !== false) {
			$data = array();
			foreach ($control_data as $d) {
				if ($d != "") $data[] = $d;
			}
			if (!isset($values[$data[0]])) $values[$data[0]] = 0;
			if ($data[2] == "checkbox") {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new checkboxcontrol($values[$data[0]]),true,array('description'=>ucwords($view).' '.gt('View Configuration')));
			} else if ($data[2] == 'text') {
				$form->register("_viewconfig[".$data[0]."]",$data[1],new textcontrol($values[$data[0]]),true,array('description'=>ucwords($view).' '.gt('View Configuration')));
			} else {
				$options = array_slice($data,3);
				$form->register("_viewconfig[".$data[0]."]",$data[1],new dropdowncontrol($values[$data[0]],$options),true,array('description'=>ucwords($view).' '.gt('View Configuration')));
			}
		}
        fclose($fh);
        ini_set('auto_detect_line_endings',$line_end);

		$form->register("submit","",new buttongroupcontrol("Save","","Cancel"),true,'base');

		return $form;
	}

    /** exdoc
     * @deprecated 2.2.0 backward compatibility wrapper
     */
	public static function getViewConfigOptions($module,$view) {  //FIXME Not Used 2.2???
		$form_file = "";
		$filepath = array_shift(self::resolveFilePaths("modules", $module, "form", $view));
		if ($filepath != false) {
			$form_file = $filepath;
		}
		if ($form_file == "") return array(); // no form file, no options

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
		$fh = fopen($form_file,"r");
		$options = array();
		while (($control_data = fgetcsv($fh,65536,"\t")) !== false) {
			$data = array();
			foreach ($control_data as $d) {
				if ($d != "") $data[] = $d;
			}
			$options[$data[0]] = $data[1];
		}
        fclose($fh);
        ini_set('auto_detect_line_endings',$line_end);
		return $options;
	}

	//FIXME DEPRECATED: backward compatibility wrapper
	public static function getFormTemplates($type) {  //FIXME Not Used???
	    $forms = array();

	    //Get the forms from the base form directory
	    if (is_dir(BASE.'forms/'.$type)) {
	        if ($dh = opendir(BASE.'forms/'.$type)) {
	             while (false !== ($file = readdir($dh))) {
	                if ( (substr($file,-4,4) == ".tpl") && ($file{0} != '_')) {
	                    $forms[substr($file,0,-4)] = substr($file,0,-4);
	                }
	            }
	        }
	    }
	    //Get the forms from the themes form directory.  If the theme has forms of the same
	    //name as the base form dir, then they will overwrite the ones already  in the array $forms.
	    if (is_dir(THEME_ABSOLUTE.'forms/'.$type)) {
	        if ($dh = opendir(THEME_ABSOLUTE.'forms/'.$type)) {
	             while (false !== ($file = readdir($dh))) {
	                if ( (substr($file,-4,4) == ".tpl") && ($file{0} != '_')) {
	                    $forms[substr($file,0,-4)] = substr($file,0,-4);
	                }
	            }
	        }
	    }

	    return $forms;
	}

	/** exdoc
	 *
	 * Looks through the module's views directory and returns
	 * all non-internal views that are found there.
	 * Returns an array of all standard view names.
	 * This array is unsorted.
	 *
	 * @param string $module The classname of the module to get views for.
	 * @return array
	 * @node Subsystems:Template
     * @deprecated backward compatibility wrapper
	 */
	public static function listModuleViews($module) {  //FIXME only used by container 2.0 edit action
		return self::buildNameList("modules", $module, "tpl", "[!_]*");
	}

    /** exdoc
     * @deprecated previously held view static config variables, e.g., calendar view type
     */
	public static function getViewParams($viewfile) {
		$base = substr($viewfile,0,-4);
		$vparam = null;
		if (is_readable($base.'.config')) {
			$vparam = include($base.'.config');
		}
		return $vparam;
	}

    /** exdoc
     * @state <b>UNDOCUMENTED</b>
     *
     * @node Undocumented
     *
     * @param bool $include_static
     *
     * @return array
     */
    //FIXME we need to also look for custom & jquery & bootstrap controls and NOT assume we only subclass basic controls?
	public static function listControlTypes($include_static = true) {
		$cdh = opendir(BASE."framework/core/forms/controls");
		$list = array();
		while (($ctl = readdir($cdh)) !== false) {
			if (substr($ctl,-4,4) == ".php" && is_readable(BASE."framework/core/forms/controls/$ctl")) {
				if (call_user_func(array(substr($ctl,0,-4),"isSimpleControl"))) {
                    if ($include_static || !call_user_func(array(substr($ctl,0,-4),"isStatic"))) {
                        $list[substr($ctl,0,-4)] = call_user_func(array(substr($ctl,0,-4),"name"));
                    }
				}
			}
		}
		return $list;
	}

    //FIXME we need to also look for custom & jquery & bootstrap controls and NOT assume we only subclass basic controls?
	public static function listSimilarControlTypes($type) {
        if (empty($type)) return array();
        $oldctl = new $type();
		$cdh = opendir(BASE."framework/core/forms/controls");
		$list = array();
		while (($ctl = readdir($cdh)) !== false) {
			if (substr($ctl,-4,4) == ".php" && is_readable(BASE."framework/core/forms/controls/$ctl")) {
				if (call_user_func(array(substr($ctl,0,-4),"getFieldDefinition")) === $oldctl->getFieldDefinition() && call_user_func(array(substr($ctl,0,-4),"isSimpleControl"))) {
					$list[substr($ctl,0,-4)] = call_user_func(array(substr($ctl,0,-4),"name"));
				}
			}
		}
		return $list;
	}

	public static function guessControlType($ddcol, $default_value=null, $colname=null) {
		$control = null;

		if (array_key_exists('FORM_FIELD_TYPE', $ddcol)) {
			new $ddcol['FORM_FIELD_TYPE']($default_value);
		} else {
			if ($ddcol[DB_FIELD_TYPE] == DB_DEF_ID && $colname != 'id') {
                //If the id field is a foreign key reference than we need to try to scaffold
                /*$field_str = array();
                if (stristr($col->Field, '_')) $field_str = split("_id", $col->Field);
                if ( (count($field_str) > 0) && ($db->tableExists($field_str[0])) ) {
                    $foreign_table = $db->describeTable($field_str[0]);
                    $fcolname = "";
                    foreach ($foreign_table as $forcol) {
                        if ($forcol->Field == "title" || $forcol->Field == "name") {
                            $fcolname = $forcol->Field;
                            break;
                        }
                    }

                    if ($fcolname != "") {
                        $foreign_key = $db->selectDropdown($field_str[0],$col->Field, null,$fcolname);
                        eDebug($foreign_key);
                        $control = new dropdowncontrol("", $foreign_key, true);
                    }
                }*/
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_ID && $colname == 'id' && $default_value != null) {
                //$control = new htmlcontrol('<input type="hidden" name="id" value="'.$default_value.'" />');
				return 'editor';
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_INTEGER) {
                //$control = new genericcontrol($default_value);
				return 'text';
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_BOOLEAN) {
                //$control = new genericcontrol($default_value);
				return 'radio';
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {
                //$control = new calendarcontrol($default_value);
                return 'calendar';
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_DECIMAL) {
                //$control = new genericcontrol($default_value);
				return 'text';
			} elseif ($ddcol[DB_FIELD_TYPE] == DB_DEF_STRING) {
                if ($ddcol[DB_FIELD_LEN] > 255) {
                    //$control = new texteditorcontrol($default_value);
                    return 'html';
                } else {
                    //$control = new genericcontrol($default_value);
                    return 'text';
                }
			}
		}
		return 'text';
	}

    /** exdoc
     * This function finds the most appropriate version of a file
     *  - if given wildcards, files -
     * and returns an array with the file's physical location's full path or,
     * if no file was found, false
     *
     * @param string $type    (to be superseded) type of base resource (= directory name)
     * @param string $name    (hopefully in the future type named) Resource identifier (= class name = directory name)
     * @param string $subtype type of the actual file (= file extension = (future) directory name)
     * @param string $subname name of the actual file (= filename name without extension)
     *
     * @return mixed
     * @node Subsystems:expTemplate
     */
    public static function resolveFilePaths($type, $name, $subtype, $subname)
    {
        //FIXME remove old school module code
        //TODO: implement caching
        //TODO: optimization - walk the tree backwards and stop on the first match
        // eDebug($type);
        // eDebug($name);
        // eDebug($subtype);
        // eDebug($subname);
        //once baseclasses are in place, simply lookup the baseclass name of an object
        if ($type == "guess") {
            // new style name processing
            //$type = array_pop(preg_split("*(?=[A-Z])*", $name));

            //TODO: convert everything to the new naming model
            if (stripos($name, "module") != false) {
                $type = "modules";
            } else {
                if (stripos($name, "control") != false) {
                    $type = "controls";
                } else {
                    if (stripos($name, "theme") != false) {
                        $type = "themes";
                    }
                }
            }
        }

        // convert types into paths
        $relpath = '';
        if ($type == "modules" || $type == 'profileextension') {
            $relpath .= "framework/modules-1/";
        } elseif ($type == "Controller" || $type == 'controllers') {
            $relpath .= "framework/views/";
        } elseif ($type == "forms") {
            if ($name == "event/email") {
                $relpath .= "framework/modules/events/views/";
//            } elseif ($name == "forms/calendar") { //TODO  forms/calendar only used by calendarmodule
//                $relpath .= "framework/modules-1/calendarmodule/";
            } else {
                $relpath .= "framework/core/forms/";
            }
        } elseif ($type == "themes" || $type == "Control" || $type == "Theme") {
            $relpath .= "themes/";
        } elseif ($type == "models") {
            $relpath .= "models/";
        } elseif ($type == "controls") {
//			$relpath .= "themes/";
            $relpath .= "external/";
//        } elseif($type == "Control") {
//            $relpath .= "themes/";
        } elseif ($type == "Form") {
            $relpath .= "framework/core/forms/";
        } elseif ($type == "Module") {
            $relpath .= "modules/";
//        } elseif($type == "Theme") {
//            $relpath .= "themes/";
        }

        // for later use for searching in lib/common
        $typepath = $relpath;
//		if ($name != "" && $name != "forms/calendar") {  //TODO  forms/calendar only used by calendarmodule
        if ($name != "" && $name != "event/email" && $name != "forms/calendar") { //TODO  forms/calendar only used by calendarmodule
            $relpath .= $name . "/";
        }

        // for later use for searching in lib/common
        $relpath2 = '';
        if ($subtype == "css") {
            $relpath2 .= "css/";
        } elseif ($subtype == "js") {
            $relpath2 .= "js/";
        } elseif ($subtype == "tpl") {
            if ($type == 'Controller' || $type == 'controllers') {
                //do nothing
            } elseif ($name == "forms/calendar") { //TODO  forms/calendar only used by calendarmodule
                $relpath2 .= "forms/calendar/";
            } elseif ($name == "event/email") {
//				$relpath2 .= "/";
                $relpath2 .= "event/email/";
            } elseif ($type == 'controls' || $type == 'Control') {
                $relpath2 .= 'editors/';
            } elseif ($type == 'profileextension') {
                $relpath2 .= "extensions/";
            } elseif ($type == 'globalviews') {
                $relpath2 .= "framework/core/views/";
            } else {
                $relpath2 .= "views/";
            }
        } elseif ($subtype == "form") {
            $relpath2 .= "views/";
//        } elseif ($subtype == "action") {  //FIXME old school actions were php files
//            $relpath2 .= "actions/";
//            //HACK: workaround for now
//            $subtype = "php";
        }

        $relpath2 .= $subname;
        if ($subtype != "") {
            $relpath2 .= "." . $subtype;
        }

        $relpath .= $relpath2;

        //TODO: handle subthemes
        //TODO: now that glob is used build a syntax for it instead of calling it repeatedly
        //latter override the precursors
        $locations = array(BASE, THEME_ABSOLUTE);
        $checkpaths = array();
        foreach ($locations as $location) {
            $checkpaths[] = $location . $typepath . $relpath2;
            if (strstr($location, THEME_ABSOLUTE) && strstr($relpath, "framework/modules-1")) {
                $checkpaths[] = $location . str_replace("framework/modules-1", "modules", $relpath);
            } else {
                $checkpaths[] = $location . $relpath;
            }
            //eDebug($relpath);
        }
//		eDebug($checkpaths);

        //TODO: handle the - currently unused - case where there is the same file in different $type categories
        $myFiles = array();
        foreach ($checkpaths as $checkpath) {
//		eDebug($checkpath);
            $tempFiles = self::glob2keyedArray(glob($checkpath));
            if ($tempFiles != false) {
                $myFiles = array_merge($myFiles, $tempFiles);
            }
        }
//        eDebug($myFiles);
        if (count($myFiles) != 0) {
            return array_values($myFiles);
        } else {
            //TODO: invent better error handling, maybe an error message channel ?
            //die("The file " . basename($filepath) . " could not be found in the filesystem");
            return false;
        }
    }

    /** exdoc
     * This function is a wrapper around self::resolveFilePaths()
     * and returns a list of the basenames, minus the file extensions - if any
     *
     * @param string $type    (to be superseded) type of base resource (= directory name)
     * @param string $name    (hopefully in the future type named) Resource identifier (= class name = directory name)
     * @param string $subtype type of the actual file (= file extension = (future) directory name)
     * @param string $subname name of the actual file (= filename name without extension)
     *
     * @return array
     * @node Subsystems:expTemplate
     */
    public static function buildNameList($type, $name, $subtype, $subname)
    { //FIXME only used by 1) event module edit action (email forms) & 2) expTemplate::listModuleViews for OS modules
        $nameList = array();
        $fileList = self::resolveFilePaths($type, $name, $subtype, $subname);
        if ($fileList != false) {
            foreach ($fileList as $file) {
                // self::resolveFilePaths() might also return directories
                if (basename($file) != "") {
                    // just to make sure: do we have an extension ?
                    // relying on there is only one dot in the filename
                    $extension = strstr(basename($file), ".");
                    $nameList[basename($file, $extension)] = basename($file, $extension);
                } else {
                    // don't know where this might be needed, but...
                    $nameList[] = array_pop(explode("/", $file));
                }
            }
        }
        return $nameList;
    }

    /**
     * helper function for building assoc array of pathnames
     *
     * @param $workArray
     *
     * @return array
     */
    public static function glob2keyedArray($workArray)
    {
        $temp = array();
        if (is_array($workArray)) {
            foreach ($workArray as $myWorkFile) {
                $temp[basename($myWorkFile)] = $myWorkFile;
            }
        }
        return $temp;
    }

    public static function get_common_template($view, $loc, $controllername='') {
        $controller = new stdClass();
        $controller->baseclassname = empty($controllername) ? 'common' : $controllername;
        $controller->loc = $loc;

        $view = urldecode($view);  // parse a non-SEFURL name

        $themenewuipath = BASE . 'themes/' . DISPLAY_THEME . '/modules/common/views/' . $controllername . '/' . $view . '.newui.tpl';
        $themepath = BASE . 'themes/' . DISPLAY_THEME . '/modules/common/views/' . $controllername . '/' . $view . '.tpl';
        $basenewuipath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.newui.tpl';
        $basepath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.tpl';

        if (bs(true)) {
            $basebstrap3path = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.bootstrap3.tpl';
            $basebstrappath = BASE . 'framework/modules/common/views/' . $controllername . '/' . $view . '.bootstrap.tpl';
            if (file_exists($themepath)) {
                return new controllertemplate($controller, $themepath);
            } elseif (bs3(true) && file_exists($basebstrap3path)) {
                return new controllertemplate($controller, $basebstrap3path);
            } elseif (file_exists($basebstrappath)) {
                return new controllertemplate($controller, $basebstrappath);
//            } elseif(NEWUI && file_exists($basenewuipath)) {  //FIXME is this the correct sequence spot?
//                return new controllertemplate($controller,$basenewuipath);
            } elseif (file_exists($basepath)) {
                return new controllertemplate($controller, $basepath);
            } else {
                return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/blank.tpl');
            }
        } else {
            if (newui() && file_exists($themenewuipath)) {
                return new controllertemplate($controller, $themenewuipath);
            } elseif (file_exists($themepath)) {
                return new controllertemplate($controller,$themepath);
            } elseif (newui() && file_exists($basenewuipath)) {
                return new controllertemplate($controller,$basenewuipath);
            } elseif(file_exists($basepath)) {
                return new controllertemplate($controller,$basepath);
            } else {
                return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/blank.tpl');
            }
        }
    }

    /**
     * Return entire list of all controller configuration views available
     *
     * @param $controller
     * @param $loc
     * @return array
     */
    public static function get_config_templates($controller, $loc) {
    //    global $db;

        // set paths we will search in for the view
        $commonpaths = array(
            BASE.'framework/modules/common/views/configure',
            BASE.'themes/'.DISPLAY_THEME.'/modules/common/views/configure',
        );

        $modpaths = array(
            $controller->viewpath.'/configure',
    	    BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/configure'
        );

        // get the common configuration files
        $common_views = self::find_config_views($commonpaths, $controller->remove_configs);
        foreach ($common_views as $key=>$value) {
            $common_views[$key]['name'] = gt($value['name']);
        }
        $moduleconfig = array();
        if (!empty($common_views['module_style'])) $moduleconfig['module_style'] = $common_views['module_style'];
        unset($common_views['module_style']);
        if (!empty($common_views['module'])) $moduleconfig['module'] = $common_views['module'];
        unset($common_views['module']);

        // get the config views for the module
        $module_views = self::find_config_views($modpaths);
        foreach ($module_views as $key=>$value) {
            $module_views[$key]['name'] = gt($value['name']);
        }

        // look for a config form for this module's current view
    //    $controller->loc->mod = expModules::getControllerClassName($controller->loc->mod);
        //check to see if hcview was passed along, indicating a hard-coded module
    //    if (!empty($controller->params['hcview'])) {
    //        $viewname = $controller->params['hcview'];
    //    } else {
    //        $viewname = $db->selectValue('container', 'view', "internal='".serialize($controller->loc)."'");
    //    }
    //    $viewconfig = $viewname.'.config';
    //    foreach ($modpaths as $path) {
    //        if (file_exists($path.'/'.$viewconfig)) {
    //            $fileparts = explode('_', $viewname);
    //            if ($fileparts[0]=='show'||$fileparts[0]=='showall') array_shift($fileparts);
    //            $module_views[$viewname]['name'] = ucwords(implode(' ', $fileparts)).' '.gt('View Configuration');
    //            $module_views[$viewname]['file'] =$path.'/'.$viewconfig;
    //        }
    //    }

        // sort the views highest to lowest by filename
        // we are reverse sorting now so our array merge
        // will overwrite property..we will run array_reverse
        // when we're finished to get them back in the right order
        krsort($common_views);
        krsort($module_views);

        if (!empty($moduleconfig)) $common_views = array_merge($common_views, $moduleconfig);
        $views = array_merge($common_views, $module_views);
        $views = array_reverse($views);

        return $views;
    }

    /**
     * Return list of controller configuration views
     * @param array $paths
     * @param array $excludes
     * @return array
     */
    public static function find_config_views($paths=array(), $excludes=array()) {
        $views = array();
        foreach ($paths as $path) {
            if (is_readable($path)) {
                $dh = opendir($path);
                while (($file = readdir($dh)) !== false) {
                    if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl' && substr($file, -14) != '.bootstrap.tpl' && substr($file, -15) != '.bootstrap3.tpl' && substr($file, -10) != '.newui.tpl') {
                        $filename = substr($file, 0, -4);
                        if (!in_array($filename, $excludes)) {
                            $fileparts = explode('_', $filename);
                            $views[$filename]['name'] = ucwords(implode(' ', $fileparts));
                            $views[$filename]['file'] = $path.'/'.$file;
                            if ((bs(true)) && file_exists($path.'/'.$filename.'.bootstrap.tpl')) {
                                $views[$filename]['file'] = $path . '/' . $filename . '.bootstrap.tpl';
                            }
                            if (bs3(true) && file_exists($path.'/'.$filename.'.bootstrap3.tpl')) {
                                $views[$filename]['file'] = $path.'/'.$filename.'.bootstrap3.tpl';
                            }
                            if (newui() && file_exists($path.'/'.$filename.'.newui.tpl')) {
                               $views[$filename]['file'] = $path.'/'.$filename.'.newui.tpl';
                           }
                        }
                    }
                }
            }
        }

        return $views;
    }

    public static function get_template_for_action($controller, $action, $loc=null) {
        $action = urldecode($action);  // parse a non-SEFURL name

        // set paths we will search in for the view
        $newuithemepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.newui.tpl'; //FIXME should there be a theme newui variation?
        $themepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$action.'.tpl';
        $basenewuipath = $controller->viewpath.'/'.$action.'.newui.tpl';
        $basepath = $controller->viewpath.'/'.$action.'.tpl';

        // the root action will be used if we don't find a view for this action and it is a derivative of
        // action.  i.e. showall_by_tags would use the showall.tpl view if we do not have a view named
        // showall_by_tags.tpl
        $root_action = explode('_', $action);
        $rootnewuithemepath = BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath.'/'.$root_action[0].'.newui.tpl'; //FIXME should there be a theme newui variation?
        $rootthemepath = BASE . 'themes/' . DISPLAY_THEME . '/modules/' . $controller->relative_viewpath . '/' . $root_action[0] . '.tpl';
        $rootnewuipath = $controller->viewpath.'/'.$root_action[0].'.newui.tpl';
        $rootbasepath = $controller->viewpath . '/' . $root_action[0] . '.tpl';

        if (bs(true)) {
            $basebstrap3path = $controller->viewpath . '/' . $action . '.bootstrap3.tpl';
            $basebstrappath = $controller->viewpath . '/' . $action . '.bootstrap.tpl';
            $rootbstrap3path = $controller->viewpath . '/' . $root_action[0] . '.bootstrap3.tpl';
            $rootbstrappath = $controller->viewpath . '/' . $root_action[0] . '.bootstrap.tpl';
            if (file_exists($themepath)) {
                return new controllertemplate($controller, $themepath);
            } elseif (bs3(true) && file_exists($basebstrap3path)) {
                return new controllertemplate($controller, $basebstrap3path);
            } elseif (file_exists($basebstrappath)) {
                return new controllertemplate($controller, $basebstrappath);
            } elseif (file_exists($basepath)) {
                return new controllertemplate($controller, $basepath);
            } elseif ($root_action[0] != $action) {
                if (file_exists($rootthemepath)) {
                    return new controllertemplate($controller, $rootthemepath);
                } elseif (bs3(true) && file_exists($rootbstrap3path)) {
                    return new controllertemplate($controller, $rootbstrap3path);
                } elseif (file_exists($rootbstrappath)) {
                    return new controllertemplate($controller, $rootbstrappath);
                } elseif (file_exists($rootbasepath)) {
                    return new controllertemplate($controller, $rootbasepath);
                }
            }
        } else {
            if (newui() && file_exists($newuithemepath)) {
                return new controllertemplate($controller, $newuithemepath);
            } elseif (file_exists($themepath)) {
                return new controllertemplate($controller, $themepath);
            } elseif (newui() && file_exists($basenewuipath)) {
                return new controllertemplate($controller, $basenewuipath);
            } elseif (file_exists($basepath)) {
                return new controllertemplate($controller, $basepath);
            } elseif ($root_action[0] != $action) {
                if (newui() && file_exists($rootnewuithemepath)) {
                    return new controllertemplate($controller, $rootnewuithemepath);
                } elseif (file_exists($rootthemepath)) {
                    return new controllertemplate($controller, $rootthemepath);
                } elseif (newui() && file_exists($rootnewuipath)) {
                    return new controllertemplate($controller, $rootnewuipath);
                } elseif (file_exists($rootbasepath)) {
                    return new controllertemplate($controller, $rootbasepath);
                }
            }
        }

        // if we get here it means there were no views for the this action to be found.
        // we will check to see if we have a scaffolded version or else just grab a blank template.
        if (bs3(true) && file_exists(BASE . 'framework/modules/common/views/scaffold/' . $action . '.bootstrap3.tpl')) {
            return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/' . $action . '.bootstrap3.tpl');
        } elseif (bs2() && file_exists(BASE . 'framework/modules/common/views/scaffold/' . $action . '.bootstrap.tpl')) {
            return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/' . $action . '.bootstrap.tpl');
        } elseif (newui() && file_exists(BASE . 'framework/modules/common/views/scaffold/' . $action . '.newui.tpl')) {
            return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/' . $action . '.newui.tpl');
        } elseif (file_exists(BASE . 'framework/modules/common/views/scaffold/' . $action . '.tpl')) {
            return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/' . $action . '.tpl');
        } else {
            return new controllertemplate($controller, BASE . 'framework/modules/common/views/scaffold/blank.tpl');
        }
    }

    /**
     * Return the best match template file for the framework, including custom views
     *  If we receive a pathname, we'll only look there, otherwise we'll run the 'framework' flow
     *  allowing for custom/theme views
     *
     * @param $ctl
     * @param $view
     * @return mixed|string
     */
    public static function find_template($ctl, $view) {
        if (strpos($view, '$') !== false) return $view;  // we don't mess with variables

        $controller = expModules::getController($ctl);

        $include_file = str_replace(array('\'', '"'), '', $view);  // remove quotes

        // store/strip template file type
        $fileparts = explode('.', $include_file);
        if (count($fileparts) > 1) {
            $type = array_pop($fileparts);
        } else $type = '.tpl';
        $include_file = implode($fileparts);

        // store/strip path and file type
        $fileparts = explode('/', $include_file);
        if (count($fileparts) > 1) {
            $is_path = true;
            $fname = array_pop($fileparts);
            $fpath = implode($fileparts);
        } else {
            $is_path = false;
            $fname = $include_file;
            $fpath = '';
        }

        //FIXME we assume the file is only a filename and NOT a path?
        $path = substr(str_replace(PATH_RELATIVE, '', $controller->asset_path), 0, -7) . 'views/' . $controller . '/';  // strip relative path for links coming from templates

        $themepath = THEME_RELATIVE . str_replace('framework/', '', $path);
        $themepath = str_replace(PATH_RELATIVE, '', $themepath);

        // see if there's an framework appropriate template variation
        //FIXME we need to check for custom views and add full path for system views if coming from custom view
        if (file_exists(BASE . $themepath . $include_file . '.' . $type)) {
            $include_file = BASE . $themepath . $include_file . '.' . $type;
        } elseif (bs(true)) {
            if (file_exists(BASE . $path . $include_file . '.bootstrap.' . $type)) {
                $include_file = BASE . $path . $include_file . '.bootstrap.' . $type;
            } elseif (bs3(true) && file_exists(BASE . $path . $include_file . '.bootstrap3.' . $type)) {
                $include_file = BASE . $path . $include_file . '.bootstrap3.' . $type;
            } else {
                $include_file = BASE . $path . $include_file . '.' . $type;
            }
        } else {
            if (newui()) {
                if (file_exists(BASE . $path . $include_file . '.newui.' . $type)) {
                    $include_file = BASE . $path . $include_file . '.newui.' . $type;
                } else {
                    $include_file = BASE . $path . $include_file . '.' . $type;
                }
            } else {
                $include_file = BASE . $path . $include_file . '.' . $type;
            }
        }

        return $include_file;
    }

    /**
     * Return list of controller display views available
     * @param $ctl
     * @param $action
     * @param $human_readable
     * @return array
     */
    public static function get_action_views($ctl, $action, $human_readable) {
        // setup the controller
        $controller = expModules::getController($ctl);

        // set path information
        $paths = array(
            $controller->viewpath,
            BASE.'themes/'.DISPLAY_THEME.'/modules/'.$controller->relative_viewpath,
        );

        $views = array();
        foreach ($paths as $path) {
            if (is_readable($path)) {
                $dh = opendir($path);
                while (($file = readdir($dh)) !== false) {
                    if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl' && substr($file, -14) != '.bootstrap.tpl' && substr($file, -15) != '.bootstrap3.tpl' && substr($file, -10) != '.newui.tpl') {
                        $filename = substr($file, 0, -4);
                        $fileparts = explode('_', $filename);
                        if ($fileparts[0] == $action) {
                            if (count($fileparts) == 1) {
                                $views[$filename] = 'Default';
                            } else {
                                array_shift($fileparts); //shift the action name off the array of words
                                $views[$filename] = ucwords(implode(' ', $fileparts));
                            }
                        }
                    }
                }
            }
        }

        // Language-ize the views names
        foreach ($views as $key=>$value) {
            $views[$key] = gt($value);
        }

        return $views;
    }

    /**
     * Return list of attached file display views available
     * @return array
     */
    public static function get_filedisplay_views() {
        $paths = array(
            BASE.'framework/modules/common/views/file/',
            BASE.'themes/'.DISPLAY_THEME.'modules/common/views/file/',
        );

        $views = array();
        foreach ($paths as $path) {
            if (is_readable($path)) {
                $dh = opendir($path);
                while (($file = readdir($dh)) !== false) {
                    if (is_readable($path.'/'.$file) && substr($file, -4) == '.tpl' && substr($file, -14) != '.bootstrap.tpl' && substr($file, -15) != '.bootstrap3.tpl' && substr($file, -10) != '.newui.tpl') {
                        $filename = substr($file, 0, -4);
                        $views[$filename] = gt($filename);
                    }
                }
            }
        }

        return $views;
    }

}

?>