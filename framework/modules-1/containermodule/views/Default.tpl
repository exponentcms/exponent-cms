{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Phillip Ball (this file anyways :)
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
 
{permissions}
	{if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1 || $permissions.order_modules == 1
      || $container->permissions.administrate == 1 || $container->permissions.edit_module == 1 || $container->permissions.delete_module == 1 || $container->permissions.order_modules == 1)}
        {css unique="container-chrome" link=$smarty.const.PATH_RELATIVE|cat:'framework/modules/container/assets/css/admin-container.css'}

        {/css}
    	{script yui3mods="1" unique="container-chrome" src="`$smarty.const.PATH_RELATIVE`framework/core/assets/js/exp-container.js"}

    	{/script}
		<div id="cont{$top->id}" class="exp-container-module-wrapper">
	{/if}
{/permissions}

{permissions}
    {*if $hasParent == 0 && ($permissions.edit_module || $permissions.add_module || $permissions.delete_module || $permissions.order_module || $permissions.administrate)*}
    {if $hasParent == 0 && ($user->isAdmin())}
	{** top level container module **}
		<div class="container-chrome">
			<a href="#" class="trigger" title="Container">{'Container'|gettext}</a>
			<ul class="container-menu">
			    {if $user->isAdmin()}
    				<li><a href="{link _common=1 action=userperms}" class="user">{"User Permissions"|gettext}</a></li>
    				<li><a href="{link _common=1 action=groupperms}" class="group">{"Group Permissions"|gettext}</a></li>
			    {/if}
				<li>{help text="Help with Containers"|gettext}</li>
			</ul>
		</div>
	{/if}
	{if $permissions.add_module == 1 && $hidebox == 0}
		<a class="addmodule" href="{link action=edit rerank=1 rank=0}"><span class="addtext">{"Add Module"|gettext}</span></a>
	{/if}
{/permissions}

{viewfile module=$singlemodule view=$singleview var=viewfile}

{foreach key=key name=c from=$containers item=container}
	{assign var=i value=$smarty.foreach.c.iteration}
	{if $smarty.const.SELECTOR == 1}
		{include file=$viewfile}
	{else}
		<a name="mod_{$container->id}"></a> 
		{permissions}
            {if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1 || $permissions.order_modules == 1
                 || $container->permissions.administrate == 1 || $container->permissions.edit_module == 1 || $container->permissions.delete_module == 1 || $container->permissions.order_modules == 1)}
                
                {* repeating css and JS calls in case they only have module management, and aren not admins *}
                {css unique="container-chrome" link=$smarty.const.PATH_RELATIVE|cat:'framework/modules/container/assets/css/admin-container.css'}

                {/css}
            	{script yui3mods="1" unique="container-chrome" src="`$smarty.const.PATH_RELATIVE`framework/core/assets/js/exp-container.js"}

            	{/script}

				<div id="module{$container->id}" class="exp-container-module-wrapper">
				{if $i == $containers|@count}
					{assign var=last value=true}
				{else}
					{assign var=last value=false}
				{/if}
				<div class="container-chrome module-chrome">
					<a href="#" class="trigger" title="{$container->info.module}">{$container->info.module}</a>
					{getchromemenu module=$container rank=$i rerank=$rerank last=$last}
				</div>
			{/if}
		{/permissions}
		
		{$container->output}

		{permissions}
            {if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1 || $permissions.order_modules == 1
                 || $container->permissions.administrate == 1 || $container->permissions.edit_module == 1 || $container->permissions.delete_module == 1 || $container->permissions.order_modules == 1)}
				</div>
			{/if}
		{/permissions}

		{permissions}
			{if $permissions.add_module == 1 && $hidebox == 0}
				<a class="addmodule" href="{link action=edit rerank=1 rank=$smarty.foreach.c.iteration}"><span class="addtext">{"Add Module"|gettext}</span></a>
			{/if}
		{/permissions}
	{/if}
{/foreach}

{permissions}
    {if ($permissions.administrate == 1 || $permissions.edit_module == 1 || $permissions.delete_module == 1 || $permissions.add_module == 1 || $permissions.order_modules == 1
         || $container->permissions.administrate == 1 || $container->permissions.edit_module == 1 || $container->permissions.delete_module == 1 || $container->permissions.order_modules == 1)}
		{clear}
		</div>
	{/if}
{/permissions}