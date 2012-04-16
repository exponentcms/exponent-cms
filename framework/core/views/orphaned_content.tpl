{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>{$smarty.const.SITE_TITLE} -- {'Archived Content'|gettext}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.LANG_CHARSET}" />
		<link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}style.css" />
		<link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}editor.css" />
		<meta name="Generator" value="Exponent Content Management System" />
	</head>
	
	<body style="margin: 0px; padding: 0px;">
	<table cellspacing="0" cellpadding="5" width="100%" border="0">
		<tr>
			<td width="70%">
				<strong>{'Archived Content Selector'|gettext}</strong>
			</td>
			<td width="30%" align="right">
				[ <a class="mngmntlink" href="{$smarty.const.PATH_RELATIVE}source_selector.php">{'Live Content'|gettext}</a> ]
			</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="5" width="100%" border="0">
		<tr>
			<td colspan="2" style="background-color: #999; color: #fff; border-bottom: 1px solid #000; padding-bottom: .5em;">
				<em>{'Use this page to choose content from a module that has been removed from the site, but not deleted.'|gettext}</em>
			</td>
		</tr>
	</table>
	<table cellspacing="0" cellpadding="5" width="100%" border="0" height="80%">
		<tr>
			<td valign="top" style="padding: 5px;">
			{$modules_output}
			</td>
			
			<td width="80%" valign="top" style="border-left: 1px dashed #666;">
			{if $error == ''}{$main_output}
			{elseif $error == 'needmodule'}{'Please select a module from the left'|gettext}
			{elseif $error == 'nomodule'}<em>{'No archived modules were found.'|gettext}</em>
			{/if}
			</td>
		</tr>
	</table>
	</body>
</html>