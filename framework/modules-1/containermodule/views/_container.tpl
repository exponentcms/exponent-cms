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

{if $container != null}
    <a name="mod_{$container->id}"></a>
	{permissions}
		{if ($permissions.manage == 1 || $permissions.edit == 1 || $permissions.delete == 1 || $permissions.create == 1 || $container->permissions.manage == 1)}
			<div id="module{$container->id}" class="exp-container-module-wrapper">
				<div class="container-chrome module-chrome">
					<a href="#" class="trigger" title="{$container->info.module|gettext}">{$container->info.module|gettext}</a>
					{getchromemenu module=$container rank=$i rerank=$rerank last=$last}
				</div>
		{/if}
	{/permissions}
	{$container->output}
	{permissions}
		{if ($permissions.manage == 1 || $permissions.edit == 1 || $permissions.delete == 1 || $permissions.create == 1 || $container->permissions.manage == 1)}
			</div>
		{/if}
	{/permissions}
{else}
	{permissions}
		{if $permissions.create == 1 && $hidebox == 0}
			<a class="addmodule" href="{link action=edit rank=$i}"><span class="addtext">{'Add Module'|gettext}</span></a>
		{/if}
	{/permissions}
{/if}