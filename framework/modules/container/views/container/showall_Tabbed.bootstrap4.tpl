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

{css unique="container" link="`$asset_path`css/container.css"}

{/css}

{uniqueid assign=tabs}

<div class="containermodule tabbed"{permissions}{if $hasParent != 0} style="border: 1px dashed darkgray;"{/if}{/permissions}>
    {viewfile module=$singlemodule view=$singleview var=viewfile}
    <div id="{$tabs}" class="">
        <ul class="nav nav-tabs" role="tablist">
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
                    <li role="presentation" class="nav-item"><a href="#tab{$smarty.section.contain.index}-{$tabs}" class="nav-link active" role="tab" data-toggle="tab"><em>{$tabtitle}</em></a></li>
                {elseif $container != null}
                    <li role="presentation" class="nav-item"><a href="#tab{$smarty.section.contain.index}-{$tabs}" class="nav-link" role="tab" data-toggle="tab"><em>{$tabtitle}</em></a></li>
                {else}
                    {permissions}
                        {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure)}
                            <li class="nav-item" role="presentation"><a href="#tab{$smarty.section.contain.index}-{$tabs}" class="nav-link" role="tab" data-toggle="tab"><em>{$tabtitle}</em></a></li>
                        {/if}
                    {/permissions}
                {/if}
            {/section}
            {permissions}
                {if ($permissions.manage || $permissions.edit || $permissions.delete || $permissions.create || $permissions.configure)}
                    <li role="presentation" class="nav-item">
                    {if $smarty.section.contain.total != 0}
                        <a href="#tab{$smarty.section.contain.index}-{$tabs}" class="nav-link" role="tab" data-toggle="tab"><em>({'Add New'|gettext})</em></a></li>
                    {else}
                        <a href="#tab{$smarty.section.contain.index}-{$tabs}" class="nav-link active" role="tab" data-toggle="tab"><em>({'Add New'|gettext})</em></a></li>
                    {/if}
                {/if}
            {/permissions}
        </ul>
        <div class="tab-content">
            {section name=contain loop=$numcontainers start=1}
                {$container=$containers[$smarty.section.contain.index]}
                {$rank=$smarty.section.contain.index}
                {$menurank=$rank+1}
                {$index=$smarty.section.contain.index}
                {if $container != null}
                    <div id="tab{$smarty.section.contain.index}-{$tabs}" role="tabpanel" class="tab-pane fade{if $smarty.section.contain.first} show active{/if}">
                        {$container=$containers.$index}
                        {$i=$menurank}
                        {$rerank=0}
                        {include file=$viewfile}
                    </div>
                {else}
                    {permissions}
                        {if $permissions.create && $hidebox == 0}
                            <div id="tab{$smarty.section.contain.index}-{$tabs}" role="tabpanel" class="tab-pane fade{if $smarty.section.contain.first} show active{/if}">
                                <a class="exp-addmodule-link" href="{link action=edit rerank=0 rank=$rank+1}"><i class="fas fa-plus"></i> {"Add Module"|gettext}</a>
                            </div>
                        {/if}
                    {/permissions}
                {/if}
            {/section}
            {permissions}
                {if $permissions.create && $hidebox == 0}
                    <div id="tab{$smarty.section.contain.index}-{$tabs}" role="tabpanel" class="tab-pane fade{if $smarty.section.contain.first || $smarty.section.contain.total == 0} show active{/if}">
                        <a class="exp-addmodule-link" href="{link action=edit rerank=0 rank=$rank+1}"><i class="fas fa-plus"></i> {"Add Module"|gettext}</a>
                    </div>
                {/if}
            {/permissions}
        </div>
    </div>
    {loading}
</div>
