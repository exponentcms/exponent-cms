{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    {if $top->external == 'N;'}
        {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure
          || $container->permissions.manage || $container->permissions.edit || $container->permissions.delete || $container->permissions.configure)}
            {$mainNeedsClosing=1}

            {css unique="admin-container" link="`$asset_path`css/container-newui.css"}

            {/css}
            {script yui3mods="1" unique="container-chrome" src="`$smarty.const.JS_RELATIVE`exp-container.js"}

            {/script}
            <div id="cont{$top->id}" class="exp-container-module-wrapper">
        {/if}
        {*if $hasParent == 0 && ($permissions.edit || $permissions.create || $permissions.delete || $permissions.order_module || $permissions.manage)*}
        {if empty($container->hasParent) && ($permissions.configure || $container->permissions.configure)}
        {** top level container module **}
            <div class="exp-skin">
            <div class="dropdown exp-container-chrome exp-container-chrome-container">
                <a class="exp-trigger" data-toggle="dropdown" href="#">{'Container'|gettext}<i class="fa fa-caret-down fa-fw"></i></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                    <li role="presentation" class="dropdown-header">{if $top->scope == 'top-sectional'}{'Top'|gettext}{else}{$top->scope|gettext}{/if}</li>
                    <li class="divider"></li>
                    {if $user->isAdmin()}
                        <li><a href="{link controller=users action=userperms mod=container}"><i class="fa fa-user fa-fw"></i> {"User Permissions"|gettext}</a></li>
                        <li><a href="{link controller=users action=groupperms mod=container}"><i class="fa fa-group fa-fw"></i> {"Group Permissions"|gettext}</a></li>
                    {/if}
                    {foreach $containers as $container}
                        {if !empty($container->external)}
                            {$external = $container->external}
                            {break}
                        {/if}
                    {/foreach}
                    {capture name=rerank}{ddrerank module=container model=container where="external='`$top->internal`'" label="Modules"|gettext}{/capture}
                    {if $smarty.capture.rerank != ""}<li>{$smarty.capture.rerank}</li>{/if}
                    {if ($permissions.configure || $container->permissions.configure)}
                   		<li><a href="{link module=container src=$module->info['source'] hcview=$top->view}" class="config-mod"><i class="fa fa-gears fa-fw"></i> {"Configure Settings"|gettext}</a></li>
                   	{/if}
                    {if $smarty.const.HELP_ACTIVE}<li>{help text="Get Help with"|gettext|cat:" "|cat:("Containers"|gettext)}</li>{/if}
                </ul>
            </div>
            </div>
        {/if}
    {/if}
	{if $permissions.create && empty($hidebox)}
		<a class="addmodule" href="{link action=edit rerank=1 rank=1}"><span class="addtext">{"Add Module"|gettext}</span></a>
	{/if}
{/permissions}

{viewfile module=$singlemodule view=$singleview var=viewfile}

{foreach key=key name=c from=$containers item=container}
    {$i=$smarty.foreach.c.iteration}
	{if $smarty.const.SELECTOR == 1}
		{include file=$viewfile}
	{else}
		<div name="mod_{$container->id}" id="mod_{$container->id}"></div>
		{permissions}
            {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure
                 || $container->permissions.manage || $container->permissions.edit || $container->permissions.delete || $container->permissions.configure)}
                
                {* repeating css and JS calls in case they only have module management, and are not admins *}
                {css unique="admin-container" link="`$asset_path`css/admin-container.css"}

                {/css}
            	{script yui3mods="1" unique="container-chrome" src="`$smarty.const.JS_RELATIVE`exp-container.js"}

            	{/script}
				<div id="module{$container->id}" class="exp-container-module-wrapper"{if !empty($container->hasParent)} style="border: 1px dashed darkgray; padding: 0.25em;"{/if}>
                    {if $i == $containers|@count}
                        {$last=true}
                    {else}
                        {$last=false}
                    {/if}
                    <div class="exp-skin">
                        <div class="dropdown exp-container-chrome exp-container-chrome-module">
                            <a class="exp-trigger" data-toggle="dropdown" href="#">{$container->info.module|gettext} <i class="fa fa-caret-down fa-fw"></i></a>
                            {nocache}{getchromemenu module=$container rank=$i+1 rerank=$rerank last=$last}{/nocache}
                        </div>
                    </div>

            {/if}
        {/permissions}

                    {$container->output}

        {permissions}
                {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure
                     || $container->permissions.manage || $container->permissions.edit || $container->permissions.delete || $container->permissions.configure)}
                </div>
            {/if}
		{/permissions}

		{permissions}
			{if $permissions.create && $hidebox == 0}
				<a class="addmodule" href="{link action=edit rerank=1 rank=$smarty.foreach.c.iteration+1}"><span class="addtext">{"Add Module"|gettext}</span></a>
			{/if}
		{/permissions}
	{/if}
{/foreach}

{permissions}
    {if $mainNeedsClosing==1}
		</div>
	{/if}
{/permissions}
