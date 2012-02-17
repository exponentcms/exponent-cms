<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

/** exdoc
 * This is the expTheme 1.0 compatibility layer
 */

function exponent_theme_remove_css() {
	return expTheme::removeCss();
}

function exponent_theme_remove_smarty_cache() {
	return expTheme::removeSmartyCache();
}

function exponent_theme_headerInfo($config) {
    expTheme::head($config);
}

function headerInfo($config) {
	return expTheme::headerInfo($config);
}

function exponent_theme_advertiseRSS() {
	expTheme::advertiseRSS();
}

function exponent_theme_footerInfo($params = array()) {
    expTheme::foot($params);
}

function footerInfo($params) {
	expTheme::footerInfo($params);
}

function exponent_theme_sourceSelectorInfo() {
	return;
}

function exponent_theme_showSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
	expTheme::showSectionalModule($module,$view,$title,$prefix, $pickable, $hide_menu);
}

function exponent_theme_showTopSectionalModule($module,$view,$title,$prefix = null, $pickable = false, $hide_menu=false) {
	expTheme::showTopSectionalModule($module,$view,$title,$prefix, $pickable, $hide_menu);
}

function exponent_theme_showModule($module,$view="Default",$title="",$source=null,$pickable=false,$section=null,$hide_menu=false,$params=array()) {
	expTheme::showModule($module,$view,$title,$source,$pickable,$section,$hide_menu,$params);
}

function exponent_theme_inAction() {
    return expTheme::inAction();
}

function exponent_theme_reRoutActionTo($theme = "") {
    return expTheme::reRoutActionTo($theme);
}

function exponent_theme_main() {
	expTheme::main();
}

function exponent_theme_runAction() {
	expTheme::runAction();
}

function exponent_theme_showAction($module, $action, $src="", $params="") {
	expTheme::showAction($module, $action, $src, $params);
}

function exponent_theme_goDefaultSection() {
	expTheme::goDefaultSection();
}

function exponent_theme_mainContainer() {
	expTheme::mainContainer();
}

function exponent_theme_getSubthemes($include_default = true,$theme = DISPLAY_THEME) {
    return expTheme::getSubthemes($include_default,$theme);
}

function exponent_theme_getPrinterFriendlyTheme() {
    return expTheme::getPrinterFriendlyTheme();
}

function exponent_theme_getTheme() {
     return expTheme::getTheme();
}

function exponent_theme_loadActionMaps() {
   return expTheme::loadActionMaps();
}

function exponent_theme_satisfyThemeRequirements() {
	expTheme::satisfyThemeRequirements();
}

?>