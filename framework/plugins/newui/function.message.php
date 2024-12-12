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

if (!function_exists('smarty_function_message')) {
    /**
     * Smarty {message} function plugin
     *
     * Type:     function<br>
     * Name:     message<br>
     * Purpose:  create message styled text
     *
     * (default) - green
     * 'error' - red
     * 'notice' - yellow
     * 'info' - blue
     *
     * @param         $params
     * @param \Smarty $smarty
     * @return bool
     *
     * @package    Smarty-Plugins
     * @subpackage Function
     */
    function smarty_function_message($params, &$smarty)
    {
        $text = empty($params['text']) ? '&#160;' : $params['text'];
        $class = empty($params['class']) ? '' : $params['class'];
        if ($class == 'error' || $class == 'danger') {
            $class = 'danger';
        } elseif ($class == 'info') {
            $class = 'info';
        } elseif ($class == 'notice' || $class == 'warning') {
            $class = 'warning';
        } else {
            $class = 'success';
        }
        $centered = empty($params['center']) ? '' : ' style="text-align:center"';
        echo '<div class="alert alert-', $class, $centered, '" role="alert">', $text, '</div>';
    }
}

?>
