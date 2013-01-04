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

<div id="editblog" class="module blog edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="editblog-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Publish'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li><a href="#tab3"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li><a href="#tab4"><em>{'SEO'|gettext}</em></a></li>
            </ul>
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'Blog Entry'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title}
                    {control type=html name=body label="Body Content"|gettext value=$record->body}
                    {control type="checkbox" name="private" label="Save as draft/private"|gettext value=1 checked=$record->private}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.enable_ealerts}
                   	    {control type="checkbox" name="send_ealerts" label="Send E-Alert?"|gettext value=1}
                   	{/if}
                    {if !$config.usescomments || !$config.hidecomments}
                        {if $config.disable_item_comments}
                            {control type="checkbox" name="disable_comments" label="Disable Comments to this Item?"|gettext value=1 checked=$record->disable_comments}
                        {/if}
                    {/if}
                </div>
                <div id="tab2">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                </div>
                {if $config.filedisplay}
                    <div id="tab3">
                        {control type="files" name="files" label="Files"|gettext value=$record->expFile}
                    </div>
                {/if}
                <div id="tab4">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Blog Item"|gettext}</div>
        {control type=buttongroup submit="Save Blog Post"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="blogtabs" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editblog-tabs'});
		Y.one('#editblog-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
