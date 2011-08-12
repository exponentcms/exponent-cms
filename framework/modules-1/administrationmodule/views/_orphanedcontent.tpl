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
<div class="administrationmodule orphanedcontent">
	<h1>{'Recycle Bin'|gettext}</h1>
	{foreach from=$modules key=class item=data}
		<div style="padding-left: 5px; border-top: 1px dashed #DDD; margin-top: 1em;">
			{$data.name}
			{foreach from=$data.modules key=src item=output}
			<div style="margin-left: 10px;" class="container_editbox">
				<div class="container_editheader" align="right">
					<a href="{link action=orphanedcontent_delete mod=$class delsrc=$src}">{'Delete Content'|gettext}</a>
				</div>
				<div class="container_box">
					<div style="width: 100%">
					{$output}
					</div>
				</div>
			</div>
			{/foreach}
		</div>
	{foreachelse}
		<i>{'You have nothing in your Recycle Bin.'|gettext}</i>
	{/foreach}

	{if $have_bad_orphans == 1}
	<br /><br />
	<b><i>{'Archived Content for Uninstalled Modules was found'|gettext}</i></b>
	{/if}
</div>
