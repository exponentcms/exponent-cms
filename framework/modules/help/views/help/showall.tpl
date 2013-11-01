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

<div class="module help showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        {if $permissions.create}
            {icon class=add action=edit text="Add a Help Doc"|gettext}{br}
        {/if}
        {if $permissions.manage}
            {icon action=manage version=$current_version->id text="Manage Help Docs for version"|gettext|cat:" `$current_version->version`"}{br}
            {icon class=manage action=manage_versions text="Manage Help Versions"|gettext}{br}
            {*{if $rank == 1}*}
            {if $rank}
	            {ddrerank items=$page->records only="help_version_id=`$current_version->id`" model="help" label="Help Docs"|gettext}
		    {/if}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <dl>
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=doc name=docs}
        <div class="item">
            <dt>
                <h2>
                    <a href={link controller=help action=show version=$doc->help_version->version title=$doc->sef_url} title="{$doc->body|summarize:"html":"para"}">{$doc->title}</a>
                </h2>
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
