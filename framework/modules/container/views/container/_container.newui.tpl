{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
			<div id="module{$container->id}" class="exp-container-module-wrapper exp-skin">
				<div class="exp-container-chrome exp-container-chrome-module">
                    <a id="dropdownMenu{$container->id}" class="exp-trigger" data-toggle="dropdown" href="#">{$container->info.module|gettext} <i class="fa fa-caret-down fa-fw"></i></a>
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
            <a class="exp-addmodule-link" href="{link action=edit rank=$i+1}"><i class="fa fa-plus"></i> {"Add Module"|gettext}</a>
		{/if}
	{/permissions}
{/if}
