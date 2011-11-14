{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class=add action=edit title="Add a Help Doc"|gettext text="Add Help Doc"|gettext}{br}
        {/if}
        {if $permissions.manage == 1}
            {icon action=manage version=$current_version->id title="Manage Help Docs"|gettext text="Manage Help Docs for version"|gettext|cat:" `$current_version->version`"}{br}
            {icon class=manage action=manage_versions title="Manage Help Versions"|gettext text="Manage Help Versions"|gettext}{br}
		    {if ($rank == 1)}
	            {ddrerank items=$page->records only="help_version_id=$current_version->id" model="help" label="Help Docs"|gettext}
		    {/if}
        {/if}
    {/permissions}
    
    <dl>
    {foreach from=$page->records item=doc name=docs}
        <div class="item">
            <dt>
                <h2>
                    <a href={link controller=help action=show version=$doc->help_version->version title=$doc->sef_url}>{$doc->title}</a>
                </h2>
            </dt>
            
            <dd>
            {permissions}
            <div class="item-actions">
                {if $permissions.edit == 1}
                    {icon action=edit record=$doc}
                {/if}
                {if $permissions.delete == 1}
                    {icon action=delete record=$doc}
                {/if}
            </div>
            {/permissions}
            
            <div class="bodycopy">
                {$doc->summary}
            </div>
            
        </div>
    {/foreach}
    </dl>
</div>
