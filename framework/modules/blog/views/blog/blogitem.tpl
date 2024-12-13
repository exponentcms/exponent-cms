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

    {if ($record->prev || $record->next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Top Only")}
        <div class="module-actions">
            {clear}
            <hr>
            <span style="float:left">
                {if $record->prev}
                    <a class="blognav" href="{link action=show title=$record->prev->sef_url}" rel="{$record->prev->sef_url}" title="{$record->prev->body|summarize:"html":"para"}">
                        {icon img='page_prev.png' live=1 title='Previous Item'|gettext}
                        {$record->prev->title}
                    </a>
                {/if}
            </span>
            <span class="loader"></span>
            <span style="float:right">
                {if $record->next}
                    <a class="blognav" href="{link action=show title=$record->next->sef_url}" rel="{$record->next->sef_url}" title="{$record->next->body|summarize:"html":"para"}">
                        {$record->next->title}
                        {icon img='page_next.png' live=1 title='Next Item'|gettext}
                    </a>
                {/if}
            </span>
            {clear}
            <hr>
        </div>
    {/if}
    <div class="item{if !$record->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}">
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$record->publish_date|format_date:"%b"}</span>
                <span class="day">{$record->publish_date|format_date:"%e"}</span>
                <span class="year">{$record->publish_date|format_date:"%Y"}</span>
            </p>
        {/if}
        <{$config.heading_level|default:'h1'} class="heading">{$record->title}</{$config.heading_level|default:'h1'}>
        <div class="item-actions">
            {printer_friendly_link view='show'}{export_pdf_link view='show' prepend='&#160;&#160;|&#160;&#160;'|not_bs}
            {subscribe_link prepend='<br/>'}
        </div>
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
                    {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
                {/if}
                {if !empty($config.usecategories)}
                    {'in'|gettext} <a href="{link action=showall cat=$record->expCat[0]->sef_url src=$record->src}" title='{"View all posts filed under"|gettext} {$record->expCat[0]->title}'>{if $record->expCat[0]->title!= ""}{$record->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</a>
                {/if}
                {if !$config.datetag}
                    {'on'|gettext} <span class="date">{$record->publish_date|format_date}</span>
                {/if}
                {if $record->publish_date > $smarty.now}
                    </strong>&#160;
                {/if},
            </span>
            {comments_count record=$record show=1 prepend=$prepend}
            {$prepend = '&#160;&#160;|&#160;&#160;'|not_bs}
            {tags_assigned record=$record prepend=','|cat:$prepend}
        </div>
        {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                    {if $record->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$record->revision_id}">{$record->revision_id}</span>{/if}
                    {if $myloc != $record->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
                    {icon action=edit record=$record}
                {/if}
                {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                    {icon action=delete record=$record}
                {/if}
                {if !$record->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $record->poster == $user->id))}
                    {icon action=approve record=$record}
                {/if}
            </div>
        {/permissions}
        <div class="bodycopy">
            {if $config.ffloat != "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
            {/if}
            {$record->body}
            {if $config.displayauthor}
                {$record->poster|signature}
            {/if}
            {if $config.ffloat == "Below"}
                {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
            {/if}
        </div>
        {if !empty($config.enable_facebook_like)}
            <div class="fb-like" data-href="{link action=show title=$record->sef_url}" data-width="{$config.fblwidth}" data-layout="{$config.fblayout|default:'standard'}" data-action="{$config.fbverb|default:'like'}" data-size="{$config.fblsize|default:'small'}" data-share="true"></div>
        {/if}
        {if $config.enable_tweet}
            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{$record->title}" data-url="{link action=show title=$record->sef_url}"{if $config.twsize} data-size="{$config.twsize}"{/if} data-show-count="false">{'Tweet'|gettext}</a>
        {/if}
        {clear}
    </div>
    {if ($record->prev || $record->next) && ($config.pagelinks == "Top and Bottom" || $config.pagelinks == "Bottom Only")}
        <div class="module-actions">
            {clear}
            <hr>
            <span style="float:left">
                {if $record->prev}
                    <a class="blognav" href="{link action=show title=$record->prev->sef_url}" rel="{$record->prev->sef_url}" title="{$record->prev->body|summarize:"html":"para"}">
                        {icon img='page_prev.png' live=1 title='Previous Item'|gettext}
                        {$record->prev->title}
                    </a>
                {/if}
            </span>
            <span class="loader"></span>
            <span style="float:right">
                {if $record->next}
                    <a class="blognav" href="{link action=show title=$record->next->sef_url}" rel="{$record->next->sef_url}" title="{$record->next->body|summarize:"html":"para"}">
                        {$record->next->title}
                        {icon img='page_next.png' live=1 title='Next Item'|gettext}
                    </a>
                {/if}
            </span>
            {clear}
            <hr>
        </div>
    {/if}
    {comments record=$record title="Comments"|gettext}
