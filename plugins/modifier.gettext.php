<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 * @param $str
 * @return string
 */
 
function smarty_modifier_gettext($str) {
	return expLang::gettext($str);
}