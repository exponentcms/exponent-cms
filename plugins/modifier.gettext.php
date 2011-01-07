<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
 
function smarty_modifier_gettext($str) {
	return expLang::gettext($str);
}