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

<div id="newedit" class="module news edit yui3-skin-sam hide">
    {if $record->id != ""}<h1>Editing {$record->title}</h1>{else}<h1>Create News Post</h1>{/if}

    {form action=update}
	    {control type=hidden name=id value=$record->id}
        <div id="newedfrm" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>Post</em></a></li>
                <li><a href="#tab2"><em>Publish</em></a></li>
                <li><a href="#tab3"><em>Files</em></a></li>
                <li><a href="#tab5"><em>SEO</em></a></li>
            </ul>            
            <div class="yui-content">            
                <div id="tab1">
                    {control type=text name=title label="Title" value=$record->title}
                	{control type="editor" name="body" label="Body" value=$record->body}                	
                	{control type="checkbox" name="is_featured" label="Feature this news post?" value=1 checked=$record->is_featured}
                	{if $config.enable_ealerts}
                	    {control type="checkbox" name="send_ealerts" label="Send E-Alerts?" value=1}
                	{/if}
                </div>
                <div id="tab2">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date" edit_text="Publish Immediately" value=$record->publish}
                    {control type="yuidatetimecontrol" name="unpublish" label="Un-publish Date" edit_text="Do Not Un-Publish" value=$record->unpublish}
                </div>          
                <div id="tab3">
                    {control type=files name=images label="Attachable Files" value=$record->expFile}
                </div>
                <div id="tab5">
                    <h2>SEO Settings</h2>
                    {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords" rows=5 cols=35 value=$record->meta_keywords}
                    {control type="textarea" name="meta_description" label="Meta Description" rows=5 cols=35 value=$record->meta_description}
                </div>
            </div>
        </div>
        {control type=buttongroup submit="Save News Post" cancel="Cancel"}
     {/form}
</div>
<div class="loadingdiv">{"Loading Edit Form"|gettext}</div>

{script unique="newed" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('newedfrm');
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
       var tabview = new Y.TabView({srcNode:'#newedfrm'});
       tabview.render();
       Y.one('#newedit').removeClass('hide');
       Y.one('.loadingdiv').remove();
//       Y.one('#newedit').removeClass('hide').next().remove();
    });
{/literal}
{/script}

