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
<div class="form_title">{'Configure Exponent'|gettext}</div>
<div class="form_header">{'This form lets you determine site-wide behavior.  Be especially careful when dealing with database settings, as you can quite easily lock yourself out of the site by switching databases.'|gettext}</div>
{$form_html}
{if $smarty.const.CURRENTCONFIGNAME == $configname}
	[ {'Activate'|gettext} ]
	[ {'Delete'|gettext} ]
{else}
	{if $canactivate == 1}
	[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_activate configname=$configname}">{'Activate'|gettext}</a> ]
	{elseif $configname != ""}
		<i>{'(You cannot activate this profile - the active configuration file is unwritable.)'|gettext}</i><br />
	{/if}
	{if $candelete == 1}
		[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_delete configname=$configname}">{'Delete'|gettext}</a> ]
	{elseif $configname != ""}
		<i>{'(You cannot delete this profile - the profile configuration file is unwritable.)'|gettext}</i><br />
	{/if}
{/if}
{if $canedit == 1}
	[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_configuresite configname=$configname}">{'Edit'|gettext}</a> ]
{elseif $configname != ""}
	<i>{'(You cannot edit or delete this profile - the profile\'s configuration file is unwritable.)'|gettext}</i>
{/if}
<table cellpadding="4" cellspacing="0" border="0" width="">
{foreach from=$configuration key=category item=opts}
	<tr><td colspan="2"><hr size='1' /><h3>{$category}</h3></td></tr>
	{foreach from=$opts key=directive item=option}
	<tr>
		<td>{$option.title}</td>
		<td>{$option.value}</td>
	</tr>
	{/foreach}
{/foreach}
</table>