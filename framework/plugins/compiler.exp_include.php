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
 * @subpackage Compiler
 */

/**
 * Smarty {exp_include} compiler plugin
 * Author: Liu Song - loosen.copen@gmail.com
 * File: compiler.include_if_exists.php
 * Type: compiler
 * Name: include_if_exists
 * Version: 1.0.0
 * Source: http://code.google.com/p/smartyplugin-include-if-exists/downloads/list
 * Purpose: Similar with "include" function, but only include the
 *    template file when it exists. Otherwise, a default file passed
 *    by parameter "else" will be included.
 * NOTE! debugging only works if view template is NOT already compiled
 *  templates are only re-compiled when the template is updated
 *  or the compiled file does not exist
 * Updates
 *    Version 2.0:
 *       Updated to work with Smarty v3 and added Exponent framework template detection/selection
 * Example:
 *    1    {exp_include file="foo" assign="foo"}
 *    2    {exp_include file="foo" else="default"}
 */

/**
 * Smarty {exp_include} compiler plugin
 * Type:     compiler<br>
 * Name:     exp_include<br>
 * Purpose:  includes appropriate file based on 'framework' with fallback
 *
 * @param $_params
 * @param $compiler
 *
 * @return string
 */
function smarty_compiler_exp_include($_params, &$compiler)
{
    $arg_list = array();

    if (!isset($_params['file'])) {
        if (count($_params) == 1) {
            $_params['file'] = $_params[0]; // single parm is filename
        } else {
            trigger_error(
                "missing 'file' attribute in exp_include tag in " . __FILE__ . " on line " . __LINE__,
                E_COMPILE_WARNING
            );
            return;
        }
    }

    foreach ($_params as $arg_name => $arg_value) {
        // look for specific arguments: file, else, or assign
        if ($arg_name == 'file') {
            $include_file = str_replace(array('\'', '"'), '', $arg_value);
            if (strpos($include_file, '$') === false) {  // we don't want to process smarty variables, just pass them through
                if (strpos($include_file, '/') === false) { // we don't want to process paths, just pass them through
                    // store/strip template file type
                    $fileparts = explode('.', $include_file);
                    if (count($fileparts) > 1) {
                        $type = array_pop($fileparts);
                    } else {
                        $type = '.tpl';
                    }
                    $include_file = implode($fileparts);

                    //                // store/strip path and file type
                    //                $fileparts = explode('/', $include_file);
                    //                if (count($fileparts) > 1) {
                    //                    $is_path = true;
                    //                    $fname = array_pop($fileparts);
                    //                    $fpath = implode($fileparts);
                    //                } else {
                    //                    $is_path = false;
                    //                    $fname = $include_file;
                    //                    $fpath = '';
                    //                }

                    //FIXME we assume the file is only a filename and NOT a path?
                    if (PATH_RELATIVE != '/') {
                        $path = str_replace(PATH_RELATIVE, '', $compiler->tpl_vars['asset_path']->value);
                    } else {
                        $path = $compiler->tpl_vars['asset_path']->value;
                    }
                    $path = substr(
                            $path,
                            0,
                            -7
                        ) . 'views/' . $compiler->tpl_vars['controller']->value . '/'; // strip relative path for links coming from templates

                    $themepath = THEME_RELATIVE . str_replace('framework/', '', $path);
                    if (PATH_RELATIVE != '/') $themepath = str_replace(PATH_RELATIVE, '', $themepath);

                    // see if there's an framework appropriate template variation
                    //FIXME we need to check for custom views and add full path for system views if coming from custom view
                    if (file_exists(BASE . $themepath . $include_file . '.' . $type)) {
                        $include_file = BASE . $themepath . $include_file . '.' . $type;  // theme custom view gets priority
                    } elseif (bs(true)) {
                        $tmp_include = $include_file;
                        $bs_file_found = false;
                        if (file_exists(BASE . $path . $include_file . '.bootstrap.' . $type)) {
                            $include_file = BASE . $path . $include_file . '.bootstrap.' . $type;  // bootstrap3 falls back to bootstrap
                            $bs_file_found = true;
                        }
                        if (bs3(true) && file_exists(
                                BASE . $path . $tmp_include . '.bootstrap3.' . $type
                            )
                        ) {
                            $include_file = BASE . $path . $tmp_include . '.bootstrap3.' . $type;
                            $bs_file_found = true;
                        } elseif (!$bs_file_found) {
                            $include_file = BASE . $path . $include_file . '.' . $type;  // fall back to plain
                        }
                    } else {
                        if (newui()) {
                            if (file_exists(BASE . $path . $include_file . '.newui.' . $type)) {
                                $include_file = BASE . $path . $include_file . '.newui.' . $type;
                            } else {
                                $include_file = BASE . $path . $include_file . '.' . $type;  // newui falls back to plain
                            }
                        } else {
                            $include_file = BASE . $path . $include_file . '.' . $type;
                        }
                    }
                }
                $include_file = '"' . $include_file . '"'; // add quotes for string
            }
            continue;
        } elseif ($arg_name == 'else') {
            // the fallback view
            $include_file_else = $arg_value;
            // tack on a default file type if one is missing
            $fileparts = explode('.', $include_file_else);
            if (count($fileparts) == 1) {
                $include_file_else .= '.tpl';
            }
            continue;
        } elseif ($arg_name == 'assign') {
            // assign the output to a variable instead of displaying
            $assign_var = $arg_value;
            continue;
        }

        // all other arguments are (additional) variables to pass to template
        if (is_bool($arg_value)) {
            $arg_value = $arg_value ? 'true' : 'false';
        }
        $arg_list[] = "'$arg_name' => $arg_value";
    }

    // output compiler code; php code in the compiled file
    if ($include_file_else) {
        $output = "\n<?php \$_include_file = (\$_smarty_tpl->templateExists({$include_file})) ? {$include_file} : {$include_file_else};\n";
    } else {
        $output = "\n<?php if(\$_smarty_tpl->templateExists({$include_file})) {\n";
    }

    if (isset($assign_var)) { // capture output for var assignment
        $output .= "ob_start();\n";
    }

    $output .= "\$_smarty_tpl_vars = \$_smarty_tpl->tpl_vars;\n";

    if ($include_file_else) {
        $output .= "echo \$_smarty_tpl->getSubTemplate(\$_include_file, \$_smarty_tpl->cache_id, \$_smarty_tpl->compile_id, 0, null, array(" . implode(
//        $output .= "echo \$_smarty_tpl->_subTemplateRender(\$_include_file, \$_smarty_tpl->cache_id, \$_smarty_tpl->compile_id, 0, null, array(" . implode( //fixme for v3.1.28+
                ',',
                (array)$arg_list
            ) . "), 0);\n";
    } else {
        $output .= "echo \$_smarty_tpl->getSubTemplate({$include_file}, \$_smarty_tpl->cache_id, \$_smarty_tpl->compile_id, 0, null, array(" . implode(
//        $output .= "echo \$_smarty_tpl->_subTemplateRender({$include_file}, \$_smarty_tpl->cache_id, \$_smarty_tpl->compile_id, 0, null, array(" . implode( //fixme for v3.1.28+
                ',',
                (array)$arg_list
            ) . "), 0);\n";
    }
    $output .= "\$_smarty_tpl->tpl_vars = \$_smarty_tpl_vars;\n" .
        "unset(\$_smarty_tpl_vars);\n";

    if (isset($assign_var)) { // clean up capture output for var assignment
        $output .= "\$_smarty_tpl->assign(" . $assign_var . ", ob_get_contents()); ob_end_clean();\n";
    }

    if ($include_file_else) {
        $output .= "unset(\$_include_file); ?>\n";
    } else {
        $output .= "} ?>\n";
    }

    return $output;
}

?>
