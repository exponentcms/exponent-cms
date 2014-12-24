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

<div id="editblog" class="module blog edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$model_name}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=revision_id value=$record->revision_id}
        {if !empty($record->current_revision_id)}
            {control type=hidden name=current_revision_id value=$record->current_revision_id}
        {/if}
        <div id="editblog-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Publish'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    <h2>{'Blog Entry'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title focus=1}
                    {control type=html name=body label="Post Content"|gettext value=$record->body}
                    {control type="checkbox" name="private" label="Save as draft/private"|gettext value=1 checked=$record->private}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$model_name`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                    {if $config.enable_ealerts}
                   	    {control type="checkbox" name="send_ealerts" label="Send E-Alert?"|gettext value=1}
                   	{/if}
                    {if $config.enable_auto_status}
                   	    {control type="checkbox" name="send_status" label="Post as Facebook Status?"|gettext value=1}
                   	{/if}
                    {if $config.enable_auto_tweet}
                   	    {control type="checkbox" name="send_tweet" label="Post as a Tweet?"|gettext value=1}
                   	{/if}
                    {if !$config.usescomments || !$config.hidecomments}
                        {if $config.disable_item_comments}
                            {control type="checkbox" name="disable_comments" label="Disable Comments to this Item?"|gettext value=1 checked=$record->disable_comments}
                        {/if}
                    {/if}
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                </div>
                {if $config.filedisplay}
                    <div id="tab3" role="tabpanel" class="tab-pane fade">
                        {control type="files" name="files" label="Files"|gettext value=$record->expFile folder=$config.upload_folder}
                    </div>
                {/if}
                <div id="tab4" role="tabpanel" class="tab-pane fade">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                    {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                    {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                    {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Blog Item"|gettext}</div>
        {control type=buttongroup submit="Save Blog Post"|gettext cancel="Cancel"|gettext}
    {/form}
    {selectobjects table=$record->tablename where="id=`$record->id`" orderby='revision_id DESC' item=revisions}
    {if count($revisions) > 1}
        {toggle unique='text-edit' label='Revisons'|gettext collapsed=true}
            {foreach from=$revisions item=revision name=revision}
                {$class = ''}
                {if $revision->revision_id == $record->revision_id}{$class = 'current-revision revision'}{else}{$class = 'revision'}{/if}
                {if !empty($revision->editor)}{$editor = $revision->editor}{else}{$editor = $revision->poster}{/if}
                {$label = 'Revision'|gettext|cat:(' #'|cat:($revision->revision_id|cat:(' '|cat:('from'|gettext|cat:(' '|cat:($revision->edited_at|format_date:$smarty.const.DISPLAY_DATETIME_FORMAT|cat:(' '|cat:('by'|gettext|cat:(' '|cat:($editor|username))))))))))}
                {if $revision->revision_id == $record->revision_id}{$label = 'Editing'|gettext|cat:(' '|cat:$label)}{/if}
                {if !$revision->approved && $smarty.const.ENABLE_WORKFLOW}{$class = 'unapproved '|cat:$class}{/if}
                {$label = $label|cat:(' - '|cat:$revision->title)}
                {group label=$label class=$class}
                    {if $revision->revision_id != $record->revision_id}
                    <a class="revision" href="{link action=edit id=$revision->id revision_id=$revision->revision_id}" title="{'Click to Restore this revision'|gettext}">
                    {else}
                    <span title="{'Editing this revision'|gettext}">
                    {/if}
                        {$revision->body|summarize:"html":"parahtml"}
                    {if $revision->revision_id != $record->revision_id}
                    </a>
                    {else}
                    </span>
                    {/if}
                {/group}
            {/foreach}
        {/toggle}
    {/if}
</div>

{*{script unique="blogtabs" yui3mods=1}*}
{*{literal}*}
    {*EXPONENT.YUI3_CONFIG.modules.exptabs = {*}
        {*fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',*}
        {*requires: ['history','tabview','event-custom']*}
    {*};*}

	{*YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {*}
        {*Y.expTabs({srcNode: '#editblog-tabs'});*}
		{*Y.one('#editblog-tabs').removeClass('hide');*}
		{*Y.one('.loadingdiv').remove();*}
    {*});*}
{*{/literal}*}
{*{/script}*}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}