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

<div id="editfile" class="module filedownload edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New File Download'|gettext}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        <div id="editfile-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Publish'|gettext}</em></a></li>
                <li><a href="#tab3"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'File Download'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title}

                    <div id="alt-control" class="alt-control">
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
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
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
                <div id="tab2">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                </div>
                <div id="tab3">
                     <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                    {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                    {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex|default:1 value=1 description='Should this page be indexed by search engines?'|gettext}
                    {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow|default:1 value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading File Download Item"|gettext}</div>
        {control type=buttongroup submit="Save File"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editfile-tabs'});
		Y.one('#editfile-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}

{script unique="file-type" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
    var radioSwitchers = Y.all('#alt-control input[type="radio"]');
    radioSwitchers.on('click',function(e){
        Y.all(".alt-item").setStyle('display','none');
        var curdiv = Y.one("#" + e.target.get('value') + "-div");
        curdiv.setStyle('display','block');
    });

    radioSwitchers.each(function(node,k){
        if(node.get('checked')==true){
            node.simulate('click');
        }
    });
});
{/literal}
{/script}