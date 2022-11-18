{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

<div id="editnews" class="module news edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$model_name}</h1>{/if}
    {form action=update}
	    {control type=hidden name=id value=$record->id}
        {control type=hidden name=revision_id value=$record->revision_id}
        {if !empty($record->current_revision_id)}
            {control type=hidden name=current_revision_id value=$record->current_revision_id}
        {/if}
        <div id="editnews-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-toggle="tab"><em>{'Post'|gettext}</em></a></li>
                <li role="presentation" class="nav-item"><a href="#tab2" class="nav-link" role="tab" data-toggle="tab"><em>{'Publish'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li role="presentation" class="nav-item"><a href="#tab3" class="nav-link" role="tab" data-toggle="tab"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li role="presentation" class="nav-item"><a href="#tab4" class="nav-link" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
                {if !$config.disable_facebook_meta}
                    <li role="presentation" class="nav-item"><a href="#tab5" class="nav-link" role="tab" data-toggle="tab"><em>{'Facebook'|gettext}</em></a></li>
                {/if}
                {if !$config.disable_twitter_meta}
                    <li role="presentation" class="nav-item"><a href="#tab6" class="nav-link" role="tab" data-toggle="tab"><em>{'Twitter'|gettext}</em></a></li>
                {/if}
            </ul>
            <div class="tab-content yui3-skin-sam">
                <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                    <h2>{'News Entry'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title focus=1}
                	{control type="editor" name="body" label="Body"|gettext value=$record->body}
                	{control type="checkbox" name="is_featured" label="Feature this News Post?"|gettext value=1 checked=$record->is_featured}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
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
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
                    <h2>{'Publishing Date'|gettext}</h2>
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                    {control type="yuidatetimecontrol" name="unpublish" label="Un-Publish Date"|gettext edit_text="Never Un-Publish" value=$record->unpublish}
                </div>
                {if $config.filedisplay}
                    <div id="tab3" role="tabpanel" class="tab-pane fade">
                        <h2>{'Attach Files'|gettext}</h2>
                        {control type=files name=images label="Attachable Files"|gettext value=$record->expFile folder=$config.upload_folder}
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
                {if !$config.disable_facebook_meta}
                    <div id="tab5" role="tabpanel" class="tab-pane fade">
                        <h2>{'Facebook Meta'|gettext}</h2>
                        <blockquote>
                            {'Also used by Twitter, Pinterest, etc...'|gettext}
                        </blockquote>
                        {control type="text" name="fb[title]" label="Meta Title"|gettext value=$record->meta_fb.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="fb[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_fb.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="fb[url]" label="Meta URL"|gettext value=$record->meta_fb.url description='Canonical URL for social media if different than Canonical URL'|gettext}
                        {control type="files" name="fbimage" subtype=fbimage label="Meta Image"|gettext value=$record->meta_fb folder=$config.upload_folder limit=1 description='Image for social media (1200px x 630px or 600px x 315px, but larger than 200px x 200px)'|gettext}
                    </div>
                {/if}
                {if !$config.disable_twitter_meta}
                    <div id="tab6" role="tabpanel" class="tab-pane fade">
                        <h2>{'Twitter Meta'|gettext}</h2>
                        {control type="text" name="tw[title]" label="Meta Title"|gettext value=$record->meta_tw.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="tw[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_tw.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="tw[site]" label="Twitter Account"|gettext value=$record->meta_tw.twsite description='Must include @'|gettext}
                        {control type="files" name="twimage" subtype=twimage label="Meta Image"|gettext value=$record->meta_tw folder=$config.upload_folder limit=1 description='Image for social media (120px x 120px minimum)'|gettext}
                    </div>
                {/if}
            </div>
        </div>
        {loading title="Loading News Item"|gettext}
        {control type=buttongroup submit="Save News Post"|gettext cancel="Cancel"|gettext}
    {/form}
    {if $smarty.const.ENABLE_WORKFLOW}
        {selectobjects table=$record->tablename where="id=`$record->id`" orderby='revision_id DESC' item=revisions}
        {if count($revisions) > 1}
            {toggle unique='text-edit' label='Revisions'|gettext collapsed=true}
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
                            {$revision->body|summarize:"html":"paraxlinks"}
                        {if $revision->revision_id != $record->revision_id}
                        </a>
                        {else}
                        </span>
                        {/if}
                    {/group}
                {/foreach}
            {/toggle}
        {/if}
    {/if}
</div>
