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

{css unique="links" link="`$asset_path`css/links.css"}

{/css}

<div class="module links showall-quicklinks">
    {if $moduletitle && !$config.hidemoduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1 || $permissions.edit == 1}
				{icon class=add action=create text="Add a new link"|gettext}
			{/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$items model="links" label="Links"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {assign var=myloc value=serialize($__loc)}
    {if $config.usecategories}
        {foreach from=$cats key=catid item=cat}
           {if $catid != 0}
              <div class="itemtitle"><h3>{$cat->name}</h3></div>
           {/if}
            <ul>
           {foreach name=links from=$cat->records item=item}
                <li{if $smarty.foreach.links.last} class="item last"{/if}>
                    <div class="link">
                        <a href="{$item->url}" {if $item->new_window == 1} target="_blank"{/if} title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                    </div>
                    {permissions}
                        <div class="item-actions">
                            {if $myloc != $item->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
                            {if $permissions.edit == 1}
                                {icon action=edit record=$item}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action=delete record=$item}
                            {/if}
                        </div>
                    {/permissions}
                    {edebug var=$item}                </li>
           {foreachelse}
              {if ($catid != 0) }
                  <div ><i>{'No Links'|gettext}</i></div>
              {/if}
           {/foreach}
            </ul>
        {/foreach}
    {else}
        <ul>
            {foreach name=items from=$items item=item name=links}
                <li{if $smarty.foreach.links.last} class="item last"{/if}>
                    <a class="link" {if $item->new_window}target="_blank"{/if} href="{$item->url}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
                    {permissions}
                        <div class="item-actions">
                            {if $myloc != $item->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
                            {if $permissions.edit == 1}
                                {icon action=edit record=$item}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon action=delete record=$item}
                            {/if}
                        </div>
                    {/permissions}
                </li>
            {/foreach}
        </ul>
    {/if}
</div>
