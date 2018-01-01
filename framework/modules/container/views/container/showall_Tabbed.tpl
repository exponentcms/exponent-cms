{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="container" link="`$asset_path`css/container.css"}

{/css}

{uniqueid assign=tabs}

<div class="containermodule tabbed"{permissions}{if $hasParent != 0} style="border: 1px dashed darkgray;"{/if}{/permissions}>
    {viewfile module=$singlemodule view=$singleview var=viewfile}
    <div id="{$tabs}" class="yui-navset">
        <ul class="yui-nav">
            {foreach from=$containers item=container key=tabnum name=contain}
                {$numcontainers=$tabnum+1}
            {/foreach}
            {section name=contain loop=$numcontainers start=1}
                {$container=$containers[$smarty.section.contain.index]}
                {*{$containereditmode=0}*}
                {if $container == null}
                    {$tabtitle="(empty)"|gettext}
                {elseif $container->title == ""}
                    {$tabtitle=' '}
                {else}
                    {$tabtitle=$container->title}
                {/if}
                {if $smarty.section.contain.first}
                    <li class="selected"><a href="#tab{$smarty.section.contain.index}-{$tabs}"><em>{$tabtitle}</em></a></li>
                {elseif $container != null}
                    <li><a href="#tab{$smarty.section.contain.index}-{$tabs}"><em>{$tabtitle}</em></a></li>
                {else}
                    {permissions}
                        {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure)}
                            <li><a href="#tab{$smarty.section.contain.index}-{$tabs}"><em>{$tabtitle}</em></a></li>
                        {/if}
                    {/permissions}
                {/if}
            {/section}
            {permissions}
                {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure)}
                    {if $smarty.section.contain.total != 0}
                        <li>
                    {else}
                        <li class="selected">
                    {/if}
                    <a href="#tab{$smarty.section.contain.index}-{$tabs}"><em>({'Add New'|gettext})</em></a></li>
                {/if}
            {/permissions}
        </ul>
        <div class="yui-content">
            {section name=contain loop=$numcontainers start=1}
                {$container=$containers[$smarty.section.contain.index]}
                {$rank=$smarty.section.contain.index}
                {$menurank=$rank+1}
                {$index=$smarty.section.contain.index}
                {if $container != null}
                    <div id="tab{$smarty.section.contain.index}-{$tabs}"{if !$smarty.section.contain.first}{/if}>
                        {$container=$containers.$index}
                        {$i=$menurank}
                        {$rerank=0}
                        {include file=$viewfile}
                    </div>
                {else}
                    {permissions}
                        {if $permissions.create && $hidebox == 0}
                            <div id="tab{$smarty.section.contain.index}-{$tabs}">
                                <a class="addmodule" href="{link action=edit rerank=0 rank=$rank+1}"><span class="addtext">{'Add Module'|gettext}</span></a>
                            </div>
                        {/if}
                    {/permissions}
                {/if}
            {/section}
            {permissions}
                {if $permissions.create && $hidebox == 0}
                    <div id="tab{$smarty.section.contain.index}-{$tabs}">
                        <a class="addmodule" href="{link action=edit rerank=0 rank=$rank+1}"><span class="addtext">{'Add Module'|gettext}</span></a>
                    </div>
                {/if}
            {/permissions}
        </div>
    </div>
    {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>

{script unique="`$tabs`" jquery="jqueryui"}
{literal}
    $('#{/literal}{$tabs}{literal}').tabs().next().remove();
{/literal}
{/script}
