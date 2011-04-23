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
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_caption}</div>
{$form_html}
{if $smarty.const.CURRENTCONFIGNAME == $configname}
	[ {$_TR.activate} ]
	[ {$_TR.delete} ]
{else}
	{if $canactivate == 1}
	[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_activate configname=$configname}">{$_TR.activate}</a> ]
	{elseif $configname != ""}
		<i>{$_TR.cannot_activate}</i><br />
	{/if}
	{if $candelete == 1}
		[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_delete configname=$configname}">{$_TR.delete}</a> ]
	{elseif $configname != ""}
		<i>{$_TR.cannot_delete}</i><br />
	{/if}
{/if}
{if $canedit == 1}
	[ <a class="mngmntlink administration_mngmntlink" href="{link action=config_configuresite configname=$configname}">{$_TR.edit}</a> ]
{elseif $configname != ""}
	<i>{$_TR.cannot_edit}</i>
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