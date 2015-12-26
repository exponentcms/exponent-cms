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
 * This is the class expSettings
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */

/** @define "BASE" "../../.." */
class expSettings
{

    public static function initialize()
    {
        /**
         * overides function html entity decode
         *
         * @param $str
         *
         * @return string
         */
        function exponent_unhtmlentities($str)
        {
            $trans = get_html_translation_table(HTML_ENTITIES);
            $trans['&apos;'] = '\'';
            $trans = array_flip($trans);

            $trans['&apos;'] = '\'';
            $trans['&#039;'] = '\'';
            return strtr($str, $trans);
        }

        // include global constants
        @include_once(BASE . "framework/conf/config.php");
        if (!defined('SITE_TITLE')) { // check for upgrade from older file structure
            if (!file_exists(BASE . "framework/conf/config.php") && file_exists(BASE . "conf/config.php")) {
                rename(BASE . "conf/config.php", BASE . "framework/conf/config.php");
                @include_once(BASE . "framework/conf/config.php");
            }
        }

        // include default constants, fill in missing pieces
        if (is_readable(BASE . "framework/conf/extensions")) {
            $dh = opendir(BASE . "framework/conf/extensions");
            while (($file = readdir($dh)) !== false) {
                if (is_readable(BASE . "framework/conf/extensions/$file") && substr(
                        $file,
                        -13,
                        13
                    ) == ".defaults.php"
                ) {
                    @include_once(BASE . "framework/conf/extensions/$file");
                }
            }
        }
    }

    /** exdoc
     * Uses magical regular expressions voodoo to pull the
     * actual define() calls out of a configuration PHP file,
     * and return them in an associative array, for use in
     * viewing and analyzing configs.  Returns an associative
     * array of constant names => values
     *
     * @param string $configname Configuration Profile name
     * @param null   $site_root
     *
     * @return array
     * @node Subsystems:Config
     */
    public static function parse($configname = '', $site_root = null)
    {
        if ($site_root == null) {
            $site_root = BASE;
        }

        if ($configname == '') {
            $file = $site_root . 'framework/conf/config.php';
        } else {
            $file = $site_root . "framework/conf/profiles/$configname.php";
        }
        $options = array();
//		$valid = array();
        if (is_readable($file)) {
            $options = self::parseFile($file);
        }
        // include default constants, fill in missing pieces
        if (is_readable($site_root . 'framework/conf/extensions')) {
            $dh = opendir($site_root . 'framework/conf/extensions');
            while (($file = readdir($dh)) !== false) {
                if (substr($file, -13, 13) == '.defaults.php') {
                    $options = array_merge(
                        self::parseFile($site_root . 'framework/conf/extensions/' . $file),
                        $options
                    );
                }
//				else if (substr($file,-14,14) == '.structure.php') {
//					$tmp = include($site_root.'framework/conf/extensions/'.$file);
//					$valid = array_merge($valid,array_keys($tmp[1]));
//				}
            }
        }

//		$valid = array_flip($valid);

//		foreach ($options as $key=>$value) {
//			if (!isset($valid[$key])) unset($options[$key]);
//		}

        return $options;
    }

    /** exdoc
     * Looks through the source of a given configuration PHP file,
     * and pulls out (by mysterious regular expressions) the define()
     * calls and returns those values.  Returns an associative array
     * of constant names => values
     *
     * @param string $file The full path to the file to parse.
     *
     * @return array
     * @node Subsystems:Config
     */
    public static function parseFile($file)
    {
        $options = array();
        foreach (file($file) as $line) {
            //$line = trim(preg_replace(array("/^.*define\([\"']/","/[^&][#].*$/"),"",$line));
            $line = trim(preg_replace(array("/^.*define\([\"']/", "/[^&][#][@].*$/"), "", $line));
            if ($line != "" && substr($line, 0, 2) != "<?" && substr($line, -2, 2) != "?>") {
                $line = str_replace(array("<?php", "?>", "<?",), "", $line);

                $opts = preg_split("/[\"'],/", $line);

                if (count($opts) == 2) {
                    if (substr($opts[1], 0, 1) == '"' || substr($opts[1], 0, 1) == "'") {
                        $opts[1] = substr($opts[1], 1, -3);
                    } else {
                        $opts[1] = substr($opts[1], 0, -2);
                    }

                    if (substr($opts[0], -5, 5) == "_HTML") {
                        $opts[1] = eval("return " . $opts[1] . ";");
                        /*					$opts[1] = preg_replace('/<[bB][rR]\s?\/?>/',"\r\n",$opts[1]); */
                    }
                    $options[$opts[0]] = str_replace("\'", "'", $opts[1]);
                }
            }
        }
        return $options;
    }

    /**
     * Saves values to config file
     *
     * @param        $values
     * @param string $configname
     */
    public static function saveValues($values, $configname = '')  //FIXME only used with themes and self::change() method
    {
        $profile = null;
        $str = "<?php\n";
        foreach ($values as $directive => $value) {
            $directive = trim(strtoupper($directive));
            if ($directive == 'CURRENTCONFIGNAME') {  // save and strip out the profile name
                $profile = $value;
                continue;
            }
            $str .= "define(\"$directive\",";
            $value = stripslashes($value); // slashes added by POST
            if (substr($directive, -5, 5) == "_HTML") {
                $value = htmlentities($value, ENT_QUOTES, LANG_CHARSET);
//              $value = str_replace(array("\r\n","\r","\n"),"<br />",$value);
                $value = str_replace(array("\r\n", "\r", "\n"), "", $value);
//                $value = str_replace(array('\r\n', '\r', '\n'), "", $value);
                $str .= "exponent_unhtmlentities('$value')";
            } elseif (is_int($value)) {
                $str .= "'" . $value . "'";
            } else {
                if ($directive != 'SESSION_TIMEOUT') {
                    $str .= "'" . str_replace("'", "\'", $value) . "'";  //FIXME is this still necessary since we stripslashes above???
                } //                    $str .= "'".$value."'";
                else {
                    $str .= "'" . str_replace("'", '', $value) . "'";
                }
            }
            $str .= ");\n";
        }

        $str .= '?>';
//		$configname = empty($values['CURRENTCONFIGNAME']) ? '' : $values['CURRENTCONFIGNAME'];
        if ($configname == '') {
            $str .= "\n<?php\ndefine(\"CURRENTCONFIGNAME\",\"$profile\");\n?>"; // add  profile name to end of active profile
        }
        self::writeFile($str, $configname);
    }

    /**
     * Update/Add a single configuration setting to the current/active site configuration
     *
     * @param $var
     * @param $val
     */
    public static function change($var, $val)
    {
        $conf = self::parseFile(BASE . 'framework/conf/config.php');
        $conf[$var] = $val;
        self::saveValues($conf);
    }

    public static function writeFile($str, $configname = '')
    {
        // if ($configname != "") {
        //                 // Wishing to save
        //                 if ((file_exists(BASE."framework/conf/profiles/$configname.php") && expUtil::isReallyWritable(BASE."framework/conf/profiles/$configname.php")) ||
        //                         expUtil::isReallyWritable($BASE."framework/conf/profiles")) {
        //
        //                         $fh = fopen(BASE."framework/conf/profiles/$configname.php","w");
        //                          fwrite($fh,$str);
        //                         fclose($fh);
        //                 } else {
        //                         echo gt('Unable to write profile configuration').'<br />';
        //                 }
        //         }

        //if (isset($values['activate']) || $configname == "") {
        if ($configname == "") {
            $configname = BASE . "framework/conf/config.php";
        }
//		if ((file_exists(BASE."framework/conf/config.php") && expUtil::isReallyWritable(BASE."framework/conf/config.php")) || expUtil::isReallyWritable(BASE."framework/conf")) {
        $conffolder = pathinfo($configname);
        if ((file_exists($configname) && expUtil::isReallyWritable($configname)) || expUtil::isReallyWritable(
                $conffolder['dirname']
            )
        ) {
            $fh = fopen($configname, "w");
            fwrite($fh, $str);
            /*fwrite($fh,"\n<?php\ndefine(\"CURRENTCONFIGNAME\",\"$configname\");\n?>\n");*/
            fclose($fh);
        } else {
            echo gt('Unable to write configuration') . '<br />';
        }
        //}
    }

    /** exdoc
     * Processes the POSTed data from the configuration form
     * object generated by self::configurationForm, and writes
     * a bunch of define() statements to the profiles config file.
     *
     * @param array $values The _POST array to pull configuration data from.
     * @param null  $site_root
     *
     * @node Subsystems:Config
     */
    public static function saveConfiguration($values, $site_root = null)  //FIXME this method is only used in install, and doesn't deal with profiles
    {
        if ($site_root == null) {
            $site_root = BASE;
        }

        if (empty($values['configname']) || $values['configname'] == 'Default') {
            $configname = '';
        } else {
            //		$configname = str_replace(" ","_",$values['configname']);
            $configname = expFile::fixName($values['configname']);
        }

        $original_config = self::parse($configname, $site_root);

        $str = "<?php\n\n";
        foreach ($values['c'] as $directive => $value) {
            $directive = strtoupper($directive);

            // Because we may not have all config options in the POST,
            // we need to unset the ones we do have from the original config.
            unset($original_config[$directive]);

            $str .= "define(\"$directive\",";
            if (substr($directive, -5, 5) == "_HTML") {
                $value = htmlentities(stripslashes($value), ENT_QUOTES, LANG_CHARSET); // slashes added by POST
                $value = str_replace(array("\r\n", "\r", "\n"), "<br />", $value);
                $str .= "exponent_unhtmlentities('$value')";
            } elseif (is_int($value)) {
                $str .= $value;
            } else {
                if ($directive != 'SESSION_TIMEOUT') {
                    $str .= "'" . str_replace("'", "\'", $value) . "'";
                } else {
                    $str .= str_replace("'", '', $value);
                }
            }
            $str .= ");\n";
        }
        foreach ($values['opts'] as $directive => $val) {
            $directive = strtoupper($directive);

            // Because we may not have all config options in the POST,
            // we need to unset the ones we do have from the original config.
            unset($original_config[$directive]);

            $str .= "define(\"$directive\"," . (isset($values['o'][$directive]) ? 1 : 0) . ");\n";
        }
        // Now pick up all of the unspecified values
        // THIS MAY SCREW UP on checkboxes.
        foreach ($original_config as $directive => $value) {
            $str .= "define(\"$directive\",";
            if (substr($directive, -5, 5) == "_HTML") {
                $value = htmlentities(stripslashes($value), ENT_QUOTES, LANG_CHARSET); // slashes added by POST
                $str .= "exponent_unhtmlentities('$value')";
            } else {
                if (is_int($value)) {
                    $str .= $value;
                } else {
                    $str .= "'" . str_replace("'", "\'", $value) . "'";
                }
            }
            $str .= ");\n";
        }
        $str .= "\n?>";

        // if ($configname != "") {
        //  // Wishing to save
        //  if (    (file_exists($site_root."framework/conf/profiles/$configname.php") && expUtil::isReallyWritable($site_root."framework/conf/profiles/$configname.php")) ||
        //      expUtil::isReallyWritable($site_root."framework/conf/profiles")) {
        //
        //      $fh = fopen($site_root."framework/conf/profiles/$configname.php","w");
        //      fwrite($fh,$str);
        //      fclose($fh);
        //  } else {
        //      echo gt('Unable to write profile configuration').'<br />';
        //  }
        // }

//		if (isset($values['activate']) || $configname == "") {
//			if ((file_exists($site_root."framework/conf/config.php") && expUtil::isReallyWritable($site_root."framework/conf/config.php")) ||
//				    expUtil::isReallyWritable($site_root."framework/conf")) {
//				$fh = fopen($site_root."framework/conf/config.php","w");
//				fwrite($fh,$str);
//
        /*				/*fwrite($fh,"\n<?php\ndefine(\"CURRENTCONFIGNAME\",\"$configname\");\n?>\n");*/
//				fclose($fh);
//			} else {
//				echo gt('Unable to write profile configuration').'<br />';
//			}
//		}
        self::writeFile($str);
    }

    /** exdoc
     * This function looks through all of the available configuration
     * extensions, and generates a form object consisting of each
     * extension's form part.  This can then be used to edit the full
     * site configuration.  Returns a form object intended for editing the profile.
     *
     * @param string $configname The name of the configuration profile,
     *                           for filling in default values.
     * @param bool   $database
     *
     * @return \form
     * @node Subsystems:Config
     */
    public static function configurationForm($configname, $database = false)  //FIXME this method is never used
    {
        // $configname = "" for active config
        if (is_readable(BASE . "framework/conf/extensions")) {
            global $user;
            $options = self::parse($configname);

            $form = new form();

            $form->register(null, '', new htmlcontrol('<h3 id="config_top">' . gt('Configuration Options') . '</h3>'));
            $form->register('configname', gt('Profile Name'), new textcontrol($configname));
            $form->register(
                'activate',
                gt('Activate'),
                new checkboxcontrol((!defined('CURRENTCONFIGNAME') || CURRENTCONFIGNAME == $configname))
            );

            $sections = array();

            $dh = opendir(BASE . 'framework/conf/extensions');
            while (($file = readdir($dh)) !== false) {
                if (is_readable(BASE . 'framework/conf/extensions/' . $file) && substr(
                        $file,
                        -14,
                        14
                    ) == '.structure.php'
                ) {
                    $arr = include(BASE . 'framework/conf/extensions/' . $file);
                    // Check to see if the current user is a super admin, and only include database if so
                    if (substr($file, 0, -14) != 'database' || $user->is_admin == 1) {
                        $form->register(
                            null,
                            '',
                            new htmlcontrol('<div id="config_' . count(
                                $sections
                            ) . '" style="font-weight: bold; margin-top: 1.5em; border-top: 1px solid black; border-bottom: 1px solid black; background-color: #ccc; font-size: 12pt;">' . $arr[0] . '</div><a href="#config_top">Top</a>')
                        );
                        $sections[] = '<a href="#config_' . count($sections) . '">' . $arr[0] . '</a>';
                        foreach ($arr[1] as $directive => $info) {

                            if ($info['description'] != '') {
                                $form->register(
                                    null,
                                    '',
                                    new htmlcontrol('<br /><br />' . $info['description'], false)
                                );
                            }
                            if (is_a($info['control'], "checkboxcontrol")) {
                                $form->meta("opts[$directive]", 1);
                                $info['control']->default = $options[$directive];
                                $info['control']->flip = true;
                                $form->register(
                                    "o[$directive]",
                                    '<strong>' . $info['title'] . '</strong>',
                                    $info['control']
                                );
                            } else {
                                if (isset($options[$directive])) {
                                    $info["control"]->default = $options[$directive];
                                }
                                $form->register(
                                    "c[$directive]",
                                    '<strong>' . $info['title'] . '</strong>',
                                    $info['control'],
                                    $info['description']
                                );
                            }
                        }
                        //$form->register(null,'',new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
                    }
                }
            }
//            $form->registerAfter('activate',null,'',new htmlcontrol('<hr size="1" />'.implode('&#160;&#160;|&#160;&#160;')));
            $form->register('submit', '', new buttongroupcontrol(gt('Save'), '', gt('Cancel')));

            return $form;
        }
        return null;
    }

    /** exdoc
     * Takes care of setting the appropriate template variables
     * to be used when viewing a profile or set of profiles.
     * Returns the initializes template.
     *
     * @param template $template The template object to assign to.
     * @param string   $configname The name of the configuration profile.
     *
     * @return \template
     * @node Subsystems:Config
     */
    public static function outputConfigurationTemplate($template, $configname)  //FIXME this method is never used
    {
        if (is_readable(BASE . "framework/conf/extensions")) {
            $categorized = array();
            $options = self::parse($configname);

            $dh = opendir(BASE . "framework/conf/extensions");
            while (($file = readdir($dh)) !== false) {
                if (is_readable(BASE . "framework/conf/extensions/$file") && substr(
                        $file,
                        -14,
                        14
                    ) == ".structure.php"
                ) {
                    $arr = include(BASE . "framework/conf/extensions/$file");
                    $categorized[$arr[0]] = array();
                    foreach ($arr[1] as $directive => $info) {
                        if (is_a($info["control"], "passwordcontrol")) {
                            $info["value"] = '&lt;' . gt('hidden') . '&gt;';
                        } else {
                            if (is_a($info["control"], "checkboxcontrol")) {
                                $info["value"] = (isset($options[$directive]) ? ($options[$directive] ? "yes" : "no") : "no");
                            } else {
                                if (is_a($info["control"], "dropdowncontrol") && isset($options[$directive])) {
                                    $info["value"] = @$info["control"]->items[$options[$directive]];
                                } else {
                                    $info["value"] = (isset($options[$directive]) ? $options[$directive] : "");
                                }
                            }
                        }
                        unset($info["control"]);

                        $categorized[$arr[0]][$directive] = $info;
                    }
                }
            }
            $template->assign("configuration", $categorized);
        }
        return $template;
    }

    /** exdoc
     * Looks through the framework/conf/profiles directory, and finds all of
     * the configuration profiles in existence.  This function also
     * performs some minor name-mangling, to make the Profile Names
     * more user friendly. Returns an array of Profile names.
     *
     * @node Subsystems:Config
     * @return array
     */
    public static function profiles()
    {
        $profiles = array();
        if (is_readable(BASE . "framework/conf/profiles")) {
            $dh = opendir(BASE . "framework/conf/profiles");
            while (($file = readdir($dh)) !== false) {
                if (is_readable(BASE . "framework/conf/profiles/$file") && substr($file, -4, 4) == ".php") {
                    $name = substr($file, 0, -4);
                    $profiles[$name] = str_replace("_", " ", $name);
                }
            }
        }
        return $profiles;
    }

    /** exdoc
     * Creates a configuration profile from the current configuration
     *
     * @param string $profile The name of the Profile to remove.
     *
     * @return string
     *
     * @node Subsystems:Config
     */
    public static function createProfile($profile)
    {
        if (!file_exists(BASE . "framework/conf/profiles")) {
            @mkdir(
                BASE . "framework/conf/profiles",
                DIR_DEFAULT_MODE_STR,
                true
            );
        }
        //FIXME do we need to delete an existing profile first??
        $profile = expFile::fixName($profile);
//        copy(BASE."framework/conf/config.php",BASE."framework/conf/profiles/".$profile.".php");
//        $baseprofile = self::parseFile(BASE . "framework/conf/config.php");
        $baseprofile = self::parse();  // get current configuration plus missing defaults
        unset($baseprofile['CURRENTCONFIGNAME']); // don't save profile name within actual profile
        self::saveValues($baseprofile, BASE . "framework/conf/profiles/" . $profile . ".php");
        return $profile;
    }

    /** exdoc
     * Deletes a configuration profile from the framework/conf/profiles
     * directory.
     *
     * @param string $profile The name of the Profile to remove.
     *
     * @node Subsystems:Config
     */
    public static function deleteProfile($profile)  //FIXME this method is never used
    {
        if (file_exists(BASE . "framework/conf/profiles/$profile.php")) {
            unlink(BASE . "framework/conf/profiles/$profile.php");
        }
    }

    /** exdoc
     * Activates a Configuration Profile.
     *
     * @param string $profile The name of the Profile to activate.
     *
     * @node Subsystems:Config
     */
    public static function activateProfile($profile)
    {
        if (is_readable(BASE . "framework/conf/profiles/$profile.php") && expUtil::isReallyWritable(
                BASE . "framework/conf"
            )
        ) {
            //FIXME do we need to delete current config first??
            copy(BASE . "framework/conf/profiles/$profile.php", BASE . "framework/conf/config.php");
            // tag it with the profile name
            $fh = fopen(BASE . "framework/conf/config.php", "a");
            fwrite(
                $fh,
                "\n<?php\ndefine(\"CURRENTCONFIGNAME\",\"$profile\");\n?>"
            ); // add activated profile name to end of profile
            fclose($fh);
        }
    }

    /** exdoc
     * Parse Drop Down options from a file.
     *
     * @param string $dropdown_name The name of the dropdown type.  The name of the
     *                              file will be retrieved by adding .dropdown as a suffix, and searching the framework/conf/data directory.
     *
     * @return array
     * @node Subsystems:Config
     */
    public static function dropdownData($dropdown_name)
    {
        $array = array();
        if (is_readable(BASE . "framework/conf/data/$dropdown_name.dropdown")) {
            $t = array();
            foreach (file(BASE . "framework/conf/data/$dropdown_name.dropdown") as $l) {
                $l = trim($l);
                if ($l != "" && substr($l, 0, 1) != "#") {
                    $go = count($t);

                    $t[] = trim($l);

                    if ($go) {
                        $array[$t[0]] = gt($t[1]);
                        $t = array();
                    }
                }
            }
        }
        return $array;
    }

}

expSettings::initialize();  // auto-initialize when loaded

?>