{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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
    <div name="mod_{$container->id}" id="mod_{$container->id}"></div>
	{permissions}
		{if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $container->permissions.manage)}
			<div id="module{$container->id}" class="exp-container-module-wrapper">
				<div class="container-chrome module-chrome">
					<a href="#" class="trigger" title="{$container->info.module|gettext}">{$container->info.module|gettext}</a>
                    {nocache}{getchromemenu module=$container rank=$i+1 rerank=$rerank last=$last}{/nocache}
				</div>
		{/if}
	{/permissions}
	{$container->output}
	{permissions}
		{if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $container->permissions.manage)}
			</div>
		{/if}
	{/permissions}
{else}
	{permissions}
		{if $permissions.create && $hidebox == 0}
			<a class="addmodule" href="{link action=edit rank=$i+1}"><span class="addtext">{'Add Module'|gettext}</span></a>
		{/if}
	{/permissions}
{/if}
