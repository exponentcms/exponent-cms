{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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
                <div class="exp-skin">
                    <div class="dropdown exp-container-chrome exp-container-chrome-module">
                        <a id="dropdownMenu{$container->id}" class="dropdown-toggle exp-trigger" data-bs-toggle="dropdown" href="#">{if $container->is_private}<i class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-unlock2-fill{else}fas fa-unlock fa-fw{/if}" title="{'Private Module'|gettext}"></i> {/if}{$container->info.module|gettext}</a>
                        {nocache}
                            {getchromemenu module=$container rank=$i+1 rerank=$rerank last=$last}
                        {/nocache}
                    </div>
                </div>
		{/if}
	{/permissions}
	<div class="{module_style style=$container->config.mstyle}"> {* module styling output *}
	{$container->output}
	</div>
	{permissions}
		{if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $container->permissions.manage)}
			</div>
		{/if}
	{/permissions}
{else}
	{permissions}
		{if $permissions.create && $hidebox == 0}
            <a class="exp-addmodule-link" href="{link action=edit rank=$i+1}"><i class="fas fa-plus"></i> {"Add Module"|gettext}</a>
		{/if}
	{/permissions}
{/if}
