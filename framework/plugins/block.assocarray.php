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
 * Smarty plugin
 *
 * @package    Smarty-Plugins
 * @subpackage Block
 */

/**
 * Smarty {assocarray} block plugin
 * Type:     block<br>
 * Name:     assocarray<br>
 * Purpose:  Set up a associative array
 * Version:  1.1
 * Author:    boots
 * @deprecated
 *
 * @param         $params
 * @param         $content
 * @param \Smarty $smarty
 * @param         $repeat
 *
 *           Purpose:  make assignments from within a template with a simple syntax
 *           supporting multiple assignments and allowing for simple
 *           assignments as well as arrays and keyed arrays.
 *           See:      http://www.phpinsider.com/smarty-forum/viewtopic.php?t=64
 *           Example:
 *           {assocarray}
 *              test: "test"
 *              test2: 10
 *              test3: "this is a test"
 *              test4: ["test1", "test2", "test3"]
 *              test5: [
 *                  key1: $smarty.const.PATH_RELATIVE
 *                  key2: "value2"
 *              ]
 *              test6: [
 *                  key1: "value1"
 *                  key2: [
 *                      subkey1: $config.passed_value  //$config.passed_value is set to 'value'
 *                      subkey2: "subvalue2"
 *                  ]
 *              ]
 *           {/assocarray}
 *
 *           creates the following smarty assignments:
 *           $test  [= "test"]
 *           $test2  [= 10]
 *           $test3  [= "this is a test"]
 *           $test4  [= array("test1", "test2", "test3")]
 *           $test5  [= array('key1'=>PATH_RELATIVE, 'key2'=>"value2")]
 *           $test6  [= array('key1'=>"value1", 'key2'=>array('subkey1'=>"value", 'subkey2'=>"subvalue2"))]
 */
function smarty_block_assocarray($params, $content, &$smarty, &$repeat)
{
    if (!empty($content)) {
        $src = $content;

        // pre-tokenize strings
        $src_c = preg_match_all('/(".*")/', $src, $token_str);
        $src = preg_replace('/".*"/', ' STRING! ', $src);
        // "fix" array delimiters
        $src = str_replace('[', ' [ ', $src);
        $src = str_replace(']', ' ] ', $src);
        // split on whitespace
        $src = preg_split('/\s+/', $src);

        $msg = '';
        $stack = array();

        // take each token in turn...
        $level = 0;
        $items = 0;
        foreach ($src as $_token) {
            $token = trim($_token);
            $last_char = substr($token, strlen($token) - 1, 1);
            $first_char = substr($token, 0, 1);
            $stack[] = $items;
            switch ($last_char) {
                case '[':
                    // array start
                    $msg .= "array(";
                    ++$level;
                    $stack[] = $items;
                    $items = 0;
                    break;
                case ']':
                    // array end
                    $msg .= ")";
                    $items = array_pop($stack);
                    ++$items;
                    --$level;
                    break;
                case ':':
                    if ($level == 0) {
                        if ($items > 0) {
                            $msg .= ';';
                        }
                        $msg .= "$" . substr($token, 0, strlen($token) - 1) . "=";
                    } else {
                        if ($items > 0) {
                            $msg .= ',';
                        }
                        $msg .= '"' . substr($token, 0, strlen($token) - 1) . '"=>';
                    }
                    // assignment
                    ++$items;
                    break;
                case '!':
                    // pre tokenized type
                    switch ($token) {
                        case 'STRING!':
                            $msg .= array_shift($token_str[1]);
                            break;
                    }
                    break;
                default:
                    if ($first_char == '$') {
                        if (strpos($token, '$smarty.const.') !== false) {
                            $msg .= substr($token, 14);
                        } else {
                            $msg .= "'".$smarty->getTemplateVars(substr($token, 1))."'";
                        }
                    } else {
                        $msg .= $token;
                    }
                    break;
            }
        }
        $msg .= ';';
        $cnt = preg_match_all('/(\$(\w+)\s*=\s*(.*?;))/', $msg, $list);  //FIXME we discard this result?
        $cnt = count($list[1]);
        if ($cnt > 0) {
            for ($i = 0; $i < $cnt; $i++) {
                $var = $list[2][$i];
                if (!empty($var)) {
                    eval ($list[1][$i]);
                    $smarty->assign($var, $$var);
                }
            }
        }

    }
}

?>