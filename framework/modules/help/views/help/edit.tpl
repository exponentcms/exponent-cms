{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

<div id="edithelp" class="module help edit yui3-skin-sam hide">
    
    {if $record->id != ""}<h1>Editing {$record->title}</h1>{else}<h1>New Help Document</h1>{/if}
    
    {form action=update record=$record}
        {control type=hidden name=id value=$record->id}
        <div id="helpedit" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>General</em></a></li>
                <li><a href="#tab2"><em>Actions and Views</em></a></li>
                <li><a href="#tab3"><em>Configuration</em></a></li>
                <li><a href="#tab4"><em>Videos</em></a></li>
                <li><a href="#tab5"><em>Additional Info</em></a></li>
                <li><a href="#tab6"><em>SEO</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                <h2>General Information</h2>
                {control type=text name=title label="Title" value=$record->title}
	            {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                {control type="dropdown" name="help_version_id" label="Version" frommodel="help_version" key=id display=version order=version dir=DESC value=$record->help_version_id}
                {control type=textarea name=summary label="Summary" value=$record->summary}
                {control type=html name=body label="General Information" value=$record->body}
				{control type="dropdown" name="section" label="Help Section" items=$sections value=$cursec}
            </div>
            <div id="tab2">
                 <h2>Actions and Views</h2>
                 {control type=html name=actions_views label="Actions and Views" value=$record->actions_views}
            </div>
            <div id="tab3">
                 <h2>Configuration</h2>
                 {control type=html name=configuration label="Configurations" value=$record->configuration}
            </div>
            <div id="tab4">
                <h2>YouTube Video Code</h2>
                {control type=textarea cols=80 rows=20 name=youtube_vid_code label="YouTube Video (Embed) Code" value=$record->youtube_vid_code}
            </div>
            <div id="tab5">
                 <h2>Additional Information</h2>
                 {control type=html name=additional label="Additional Info (displays in side column)" value=$record->additional}
            </div>
            <div id="tab6">
                 <h2>SEO Settings</h2>
                {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title}
                {control type="textarea" name="meta_keywords" label="Meta Description" rows=5 cols=35 value=$record->meta_description}
                {control type="textarea" name="meta_description" label="Meta Keywords" rows=5 cols=35 value=$record->meta_keywords}
            </div>
            </div>
        </div>
        {control type=buttongroup submit="Save Help Doc" cancel="Cancel"}
    {/form}     
</div>
<div class="loadingdiv">{"Loading Edit Form"|gettext}</div>

{script unique="editform" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('helpedit');
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#helpedit'});
	    tabview.render();
		Y.one('#edithelp').removeClass('hide');
		Y.one('.loadingdiv').remove();
//        Y.one('#edithelp').removeClass('hide').next().remove();
    });
{/literal}
{/script}
