{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

{permissions level=$smarty.const.UILEVEL_NORMAL}
<div class="administrationmodule default">
	<h1>{$moduletitle}</h1>
	{include file="`$smarty.const.BASE`modules/common/views/_permission_icons.tpl"}

	<div class="category-box">
	{foreach name=cat from=$menu key=cat item=items}
		{assign var=perm_name value=$check_permissions[$cat]}
		{if $permissions[$perm_name] == 1}
		<div class="category">
			<h4 style="background-image: url({$items.icon})">{$cat}</h4>
			<ul class="task">
			{foreach name=links from=$items item=info key=name}
				{if $name != 'icon'}
					<li><a href="{link module=$info.module action=$info.action}">{$info.title}</a></li>
				{/if}
			{/foreach}
			</ul>
		</div>
		{/if}
	{/foreach}
	</div>
</div>
{/permissions}
