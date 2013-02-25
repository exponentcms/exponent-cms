<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
/** @define "BASE" "../../.." */

/**
 * Wraps the OS modules template system in use, to provide a uniform and consistent
 * interface to templates.
 *
 * @package    Subsystems-Forms
 * @subpackage Template
 */
//TODO: prepare this class for multiple template systems
class template extends basetemplate {

    var $module = '';

    function __construct($module, $view = null, $loc = null, $caching = false, $type = null) {
        $type = !isset($type) ? 'modules' : $type;

        //parent::__construct("modules", $module, $view);
        parent::__construct($type, $module, $view);

        $this->viewparams = expTemplate::getViewParams($this->viewfile);

        if ($loc == null) {
            $loc = expCore::makeLocation($module);
        }

        $this->tpl->assign("__loc", $loc);
        $this->tpl->assign("__name", $module);

        // View Config
        global $db;
        $container_key = serialize($loc);
        $cache = expSession::getCacheValue('containers');
        if (isset($cache[$container_key])) {
            $container = $cache[$container_key];
        } else {
            $container = $db->selectObject("container", "internal='" . $container_key . "'");
            $cache[$container_key] = $container;
        }
        $this->viewconfig = ($container && isset($container->view_data) && $container->view_data != "" ? unserialize($container->view_data) : array());
        $this->tpl->assign("__viewconfig", $this->viewconfig);
    }

}

?>
