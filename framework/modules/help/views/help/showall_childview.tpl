{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="module help showall childview">
    <h2>{'Additional Help Topics'|gettext}</h2>
    <dl>
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=doc name=docs}
        <div class="item">
            <dt>
                <h3>
                    <a href={link controller=help action=show version=$doc->help_version->version title=$doc->sef_url} title="{$doc->body|summarize:"html":"para"}">{$doc->title}</a>
                </h3>
            </dt>
            
            <dd>
            {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $doc->poster == $user->id)}
                    {if $myloc != $doc->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$doc->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
                    {icon action=edit record=$doc}
                    {icon action=copy record=$doc}
                {/if}
                {if $permissions.delete || ($permissions.create && $doc->poster == $user->id)}
                    {icon action=delete record=$doc}
                {/if}
            </div>
            {/permissions}
            
            <div class="bodycopy">
                {*{$doc->summary}*}
                {*{$doc->body|summarize:"html":"paralinks"}*}
                {$doc->body|summarize:"html":"parahtml"}
            </div>
            
        </div>
    {/foreach}
    </dl>
</div>
