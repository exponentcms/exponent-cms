{*
 * Copyright (c) 2007-2009 OIC Group, Inc.
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

{css unique="news-edit" link="`$smarty.const.YUI2_PATH`assets/skins/sam/calendar.css"}

{/css}

<div id="newsedit" class="module news edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
	    {control type=hidden name=id value=$record->id}
        <div id="newsedit-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'Post'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Publish'|gettext}</em></a></li>
                <li><a href="#tab3"><em>{'Files'|gettext}</em></a></li>
                <li><a href="#tab5"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">            
                <div id="tab1">
                    {control type=text name=title label="Title"|gettext value=$record->title}
                	{control type="editor" name="body" label="Body"|gettext value=$record->body}
                	{control type="checkbox" name="is_featured" label="Feature this news post?"|gettext value=1 checked=$record->is_featured}
                	{if $config.enable_ealerts}
                	    {control type="checkbox" name="send_ealerts" label="Send E-Alerts?"|gettext value=1}
                	{/if}
                </div>
                <div id="tab2">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                    {control type="yuidatetimecontrol" name="unpublish" label="Un-publish Date"|gettext edit_text="Do Not Un-Publish" value=$record->unpublish}
                </div>
                <div id="tab3">
                    {control type=files name=images label="Attachable Files"|gettext value=$record->expFile}
                </div>
                <div id="tab5">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading News Item"|gettext}</div>
        {control type=buttongroup submit="Save News Post"|gettext cancel="Cancel"|gettext}
     {/form}
</div>

{script unique="editform" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
       var tabview = new Y.TabView({srcNode:'#newsedit-tabs'});
       tabview.render();
       Y.one('#newsedit-tabs').removeClass('hide');
       Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}