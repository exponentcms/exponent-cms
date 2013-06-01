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

    {if ($record->prev || $record->next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Top Only")}
        <div class="module-actions">
            {clear}
            <hr>
            <span style="float:left">
                {if $record->prev}
                    <a class="nav" href="{link action=show title=$record->prev->sef_url}" rel="{$record->prev->sef_url}" title="{$record->prev->body|summarize:"html":"para"}">
                        {icon img='page_prev.png' title='Previous Item'|gettext}
                        {$record->prev->title}
                    </a>
                {/if}
            </span>
            <span style="float:right">
                {if $record->next}
                    <a class="nav" href="{link action=show title=$record->next->sef_url}" rel="{$record->next->sef_url}" title="{$record->next->body|summarize:"html":"para"}">
                        {$record->next->title}
                        {icon img='page_next.png' title='Next Item'|gettext}
                    </a>
                {/if}
            </span>
            {clear}
            <hr>
        </div>
    {/if}
    <div class="item">
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$record->publish_date|format_date:"%b"}</span>
                <span class="day">{$record->publish_date|format_date:"%e"}</span>
                <span class="year">{$record->publish_date|format_date:"%Y"}</span>
            </p>
        {/if}
        <h1>{$record->title}</h1>
        {printer_friendly_link view='show'}{export_pdf_link view='show' prepend='&#160;&#160;|&#160;&#160;'}
        {subscribe_link prepend='<br/>'}
        {$myloc=serialize($__loc)}
        <div class="post-info">
            <span class="attribution">
                {if $record->private}<strong>({'Draft'|gettext})</strong>{/if}
                {if $record->publish_date > $smarty.now}
                    <strong>{'Will be'|gettext}&#160;
                {/if}
                {$prepend = ''}
                {if !$config.displayauthor}
                    <span class="label posted">{'Posted by'|gettext}</span>
                    <a href="{link action=showall_by_author author=$record->poster|username}" title='{"View all posts by"|gettext} {attribution user_id=$record->poster}'>{attribution user_id=$record->poster}</a>
                    {$prepend = '&#160;&#160;|&#160;&#160;'}
                {/if}
                {if $config.usecategories}
                    {'in'|gettext} <a href="{link action=showall src=$record->src cat=$record->expCat[0]->id}" title='{"View all posts filed under"|gettext} {$item->expCat[0]->title}'>{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</a>
                {/if}
                {if !$config.datetag}
                    {'on'|gettext} <span class="date">{$record->publish_date|format_date}</span>
                {/if}
                {if $record->publish_date > $smarty.now}
                    </strong>&#160;
                {/if}
            </span>
            {comments_count record=$record prepend=$prepend}
            {tags_assigned record=$record prepend='&#160;&#160;|&#160;&#160;'}
        </div>
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
        <div class="bodycopy">
            {if $config.ffloat != "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
            {/if}
            {$record->body}
            {if !$config.displayauthor}
                {$record->poster|signature}
            {/if}
            {if $config.ffloat == "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
            {/if}
        </div>
        {clear}
    </div>
    {if ($record->prev || $record->next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Bottom Only")}
        <div class="module-actions">
            {clear}
            <hr>
            <span style="float:left">
                {if $record->prev}
                    <a class="nav" href="{link action=show title=$record->prev->sef_url}" rel="{$record->prev->sef_url}" title="{$record->prev->body|summarize:"html":"para"}">
                        {icon img='page_prev.png' title='Previous Item'|gettext}
                        {$record->prev->title}
                    </a>
                {/if}
            </span>
            <span style="float:right">
                {if $record->next}
                    <a class="nav" href="{link action=show title=$record->next->sef_url}" rel="{$record->next->sef_url}" title="{$record->next->body|summarize:"html":"para"}">
                        {$record->next->title}
                        {icon img='page_next.png' title='Next Item'|gettext}
                    </a>
                {/if}
            </span>
            {clear}
            <hr>
        </div>
    {/if}
    {comments record=$record title="Comments"|gettext}
