<?php
##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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

    /*
        SOME UTILITY FUNCTIONS

        Normally these are wrapped up in Exponent but since this is a standalone file we'll need to define them explicitly
    */

    /*function my__realpath($path)
    {
        $path = str_replace('\\','/',realpath($path));
        if ($path{1} == ':')
        {
            // We can't just check for C:/, because windows users may have the IIS webroot on X: or F:, etc.
            $path = substr($path,2);
        }
        return $path;
    }*/

    /*function expUnserialize($serial_str)
    {
        $out = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str );
        return unserialize($out);
    }*/

    /*function exponent_unhtmlentities( $str )
    {
            $trans = get_html_translation_table(HTML_ENTITIES);
            $trans['&apos;'] = '\'';
            $trans=array_flip($trans);

            $trans['&apos;'] = '\'';
            $trans['&#039;'] = '\'';
            return strtr($str, $trans);
    }*/
    /*
        DEFINE A FEW CONSTANTS
    */

    require_once('../exponent.php');
//    require_once('../framework/conf/config.php');  // now pulled in by exponent.php

    //define('BASE',str_ireplace('/cron','',__realpath(dirname(__FILE__))));
    define('EXP_PATH', BASE);
/** @define "EXP_PATH" ".." */

    //Pull in the mysqli helper class form exponet.
    // Sure, we could have used mysqli directly but we have this nice friendly class so why not use it
//    require_once(EXP_PATH . "framework/core/subsystems/database/mysqli.php");

//TODO Swift 3.x is no longer available, but expMail is already waiting
// $mail - new expMail(); $mail->quickSend();
// Pull in the Swift lib from Exponent's external dir
//    require_once(EXP_PATH . "external/Swift/Swift.php");
//    require_once(EXP_PATH . "external/Swift/Connection/SMTP.php");
//    require_once(EXP_PATH . "external/Swift/Plugin/AntiFlood.php");
//    require_once(EXP_PATH . "external/Swift/Plugin/Decorator.php");

    // instantiate the mysql helper class and connect
//    $db = new mysqli_database();  gobal $db now set by exponent.php above
//    $db->connect(DB_USER, DB_PASS, DB_HOST.':'.DB_PORT, DB_NAME);

?>
