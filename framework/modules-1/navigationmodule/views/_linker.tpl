{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
{* <html>
	<head>
		<meta type="Generator" value="Exponent Content Management System" />
		<link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}style.css" /> 
		<style type="text/css">
		{literal}
			body {
				padding: 5px;
			}
		{/literal}
		</style>
	</head>
	<body> *}
	<b>{'Site Hierarchy'|gettext}</b><hr size="1" />
		<table cellpadding="1" cellspacing="0" border="0" width="100%">
		{foreach from=$sections item=section}
		<tr><td style="padding-left: {math equation="x*20" x=$section->depth}px">
		{if $section->active}
{*			<a href="{$smarty.get.linkbase}&section={$section->id}&section_name={$section->name|escape:url}" class="navlink">{$section->name|escape:"htmlall"}</a>&nbsp; *}
			<a href="javascript:onPageSelect('/{$section->sef_name}')" class="navlink">{$section->name|escape:"htmlall"}</a>&nbsp;
		{else}
			{$section->name}
		{/if}
		</td></tr>
		{/foreach}
		</table>
	{if $haveStandalones}
	<br /><br /><br />
	<b>{'Standalone Pages'|gettext}</b><hr size="1" />
		<table cellpadding="1" cellspacing="0" border="0" width="100%">
		{foreach from=$standalones item=section}
		<tr><td style="padding-left: 20px">
{*		<a href="{$smarty.get.linkbase}&section={$section->id}&section_name={$section->name|escape:url}" class="navlink">{$section->name|escape:"htmlall"}</a>&nbsp; *}
		<a href="javascript:onPageSelect('/{$section->sef_name}')" class="navlink">{$section->name|escape:"htmlall"}</a>&nbsp;
		</td></tr>
		{/foreach}
		</table>
	{/if}
{*	</body>
</html> *}
