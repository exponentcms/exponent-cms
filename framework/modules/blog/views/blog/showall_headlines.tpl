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

<div class="module blog showall-headlines">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{/if}
    {rss_link}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h2>{/if}
    {permissions}
		<div clas="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <ul>
    {foreach from=$page->records item=record name="blogs"}
        {if $smarty.foreach.blogs.iteration <= $config.headcount}
        <li class="item">
            <a href="{link action=show title=$record->sef_url}" title="{$record->body|summarize:"html":"para"}">{$record->title}</a>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {if $myloc != $record->location_data}
                            {if $permissions.manage == 1}
                                {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$record}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$record}
                    {/if}
                </div>
            {/permissions}
        </li>
        {/if}
    {/foreach}
    </ul> 
</div>
