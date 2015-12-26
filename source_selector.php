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

define('SCRIPT_EXP_RELATIVE','');
define('SCRIPT_FILENAME','source_selector.php');

/* exdoc
 * Define Source_Selector constant as 1, since we are not selecting orphaned content.
 * @node General
 */
define('SOURCE_SELECTOR',1);

// Initialize the Exponent Framework
include_once('exponent.php');

$level = 99;
if (expSession::is_set('uilevel')) {
    $level = expSession::get('uilevel');
}
expSession::set("uilevel", UILEVEL_PREVIEW);

$section = $router->getSection();
$sectionObj = $router->getSectionObj($section);
if (bs3()) {
    expCSS::pushToHead(array(
        'unique'=>"container-newui",
        'lesscss'=>PATH_RELATIVE."framework/modules/container/assets/less/container-newui.less"
    ));
}
// Call the real selector script.  It will use the value of SOURCE_SELECTOR to determine what it needs to do.
include_once('selector.php');

expSession::set("uilevel", $level);

?>