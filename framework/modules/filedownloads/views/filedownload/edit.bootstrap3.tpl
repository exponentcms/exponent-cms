{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div id="editfile" class="module filedownload edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New File Download'|gettext}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        <div id="editfile-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Publish'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
                {if !$config.disable_facebook_meta}
                    <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{'Facebook'|gettext}</em></a></li>
                {/if}
                {if !$config.disable_twitter_meta}
                    <li role="presentation"><a href="#tab5" role="tab" data-toggle="tab"><em>{'Twitter'|gettext}</em></a></li>
                {/if}
            </ul>
            <div class="tab-content yui3-skin-sam">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    <h2>{'File Download'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title focus=1}

                    <div id="alt-control-file" class="alt-control">
                        <div class="control"><label class="label">{'Type of Media'|gettext}</label></div>
                        <div class="alt-body">
                            {control type=radiogroup columns=2 name="file_type" items="Uploaded File,External File"|gettxtlist values="file,ext_file" default=$record->file_type|default:"file"}
                            <div id="file-div" class="alt-item" style="display:none;">
                                {control id="downloadable" type="files" name="downloadable" label="Files for Download"|gettext subtype=downloadable value=$record->expFile description='First file is the primary download.'|gettext}
                            </div>
                            <div id="ext_file-div" class="alt-item" style="display:none;">
                                {control type=url name=ext_file label="External File URL"|gettext value=$record->ext_file size=100 description='A download link on another server used instead of Files above.'|gettext}
                            </div>
                        </div>
                    </div>

                    {control id="preview" type="files" name="preview" label="Preview Image to display"|gettext subtype=preview accept="image/*" value=$record->expFile limit=1}
                    {control type=html name=body label="Description"|gettext value=$record->body}
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
                    {if $config.disable_item_comments}
                   	    {control type="checkbox" name="disable_comments" label="Disable Comments to this Item?"|gettext value=1 checked=$record->disable_comments}
                   	{/if}
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                </div>
                <div id="tab3" role="tabpanel" class="tab-pane fade">
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
                    <div id="tab4" role="tabpanel" class="tab-pane fade">
                        <h2>{'Facebook Meta'|gettext}</h2>
                        <blockquote>
                            {'Also used for Twitter, Pinterest, etc...'|gettext}
                        </blockquote>
                        {control type="text" name="fb[title]" label="Meta Title"|gettext value=$record->meta_fb.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="fb[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_fb.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="fb[url]" label="Meta URL"|gettext value=$record->meta_fb.url description='Canonical URL for social media if different than Canonical URL'|gettext}
                        {control type="files" name="fbimage" subtype=fbimage label="Meta Image"|gettext value=$record->meta_fb folder=$config.upload_folder limit=1 description='Image for social media (1200px x 630px or 600px x 315px, but larger than 200px x 200px)'|gettext}
                    </div>
                {/if}
                {if !$config.disable_twitter_meta}
                    <div id="tab5" role="tabpanel" class="tab-pane fade">
                        <h2>{'Twitter Meta'|gettext}</h2>
                        {control type="text" name="tw[title]" label="Meta Title"|gettext value=$record->meta_tw.title size=88 description='Override the item title for social media'|gettext}
                        {control type="textarea" name="tw[description]" label="Meta Description"|gettext rows=5 cols=35 size=200 value=$record->meta_tw.description description='Override the item summary for social media'|gettext}
                        {control type="text" name="tw[site]" label="Twitter Account"|gettext value=$record->meta_tw.twsite description='Must include @'|gettext}
                        {control type="files" name="twimage" subtype=twimage label="Meta Image"|gettext value=$record->meta_tw folder=$config.upload_folder limit=1 description='Image for social media (120px x 120px minimum)'|gettext}
                    </div>
                {/if}
            </div>
        </div>
        {loading title="Loading File Download Item"|gettext}
        {control type=buttongroup submit="Save File"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}

{script unique="file-type" jquery=1}
{literal}
$(document).ready(function(){
    var radioSwitcher_file = $('#alt-control-file input[type="radio"]');
    radioSwitcher_file.on('click', function(e){
        $("#alt-control-file .alt-item").css('display', 'none');
        var curdiv = $("#" + e.target.value + "-div");
        curdiv.css('display', 'block');
    });

    radioSwitcher_file.each(function(k, node){
        if(node.checked == true){
            $(node).trigger('click');
        }
    });
});
{/literal}
{/script}
