<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

if (!defined("EXPONENT")) exit("");

global $router;
$container = $db->selectObject('container', 'id='.intval($_REQUEST['id']));
//eDebug($container);
$module_loc = unserialize($container->internal);
$clipboard_object->module = $module_loc->mod;
$clipboard_object->source = $module_loc->src;
$clipboard_object->internal = $module_loc->int;
$clipboard_object->title = $container->title;
$clipboard_object->view = $container->view;
$clipboard_object->copied_from = $db->selectValue('section', 'name', 'id='.exponent_sessions_get('last_section'));
$clipboard_object->section_id = exponent_sessions_get('last_section');
$clipboard_object->operation = $_REQUEST['op'];
//$clipboard_object->description = $db->selectValue('locationref', 'description', 'module="'.$clipboard_object->module.'" AND source="'.$clipboard_object->source.'"');
//$clipboard_object->refcount = $db->selectValue('locationref', 'refcount', 'module="'.$clipboard_object->module.'" AND source="'.$clipboard_object->source.'"');
$clipboard_object->description = $db->selectValue('sectionref', 'description', 'module="'.$clipboard_object->module.'" AND source="'.$clipboard_object->source.'"');
$clipboard_object->refcount = $db->selectValue('sectionref', 'refcount', 'module="'.$clipboard_object->module.'" AND source="'.$clipboard_object->source.'"');
//eDebug($clipboard_object);
$db->insertObject($clipboard_object, 'clipboard');
flash('message', 'Module copied to clipboard');
exponent_flow_redirect();

?>
