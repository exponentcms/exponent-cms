{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div id="editgallery" class="module imagegallery edit hide exp-skin-tabview">
    
    {if $record->id}<h1>Editing {$record->title}</h1>{else}<h1>New {$modelname}</h1>{/if}
        
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                <li><a href="#tab3"><em>{"Image"|gettext}</em></a></li>
                <li><a href="#tab4"><em>{"SEO"|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                {control type=text name=title label="Title" value=$record->title}
                {control type=html name=body label="Description" value=$record->body}
                {control type="text" name="link" label="Link a Slideshow Slide" value=$record->link}
                {control type="text" name="alt" label="Alt Tag (overwrites alt supplied in file manager)" value=$record->alt}
            </div>
            <div id="tab3">
                {control type="files" name="files" label="Files" value=$record->expFile limit=1}
            </div>
            <div id="tab4">
                 <h2>SEO Settings</h2>
                {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title}
                {control type="textarea" name="meta_description" label="Meta Description" rows=5 cols=35 value=$record->meta_description}
                {control type="textarea" name="meta_keywords" label="Meta Keywords" rows=5 cols=35 value=$record->meta_keywords}
            </div>
            </div>
        </div>
        {control type=buttongroup submit="Save Photo" cancel="Cancel"}
    {/form}   
    
</div>
<div class="loadingdiv">{"Loading Edit Form"|gettext}</div>

{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
        var YAHOO=Y.YUI2;

        var tabView = new YAHOO.widget.TabView('demo');
        Y.one('#editgallery').removeClass('hide').next().remove();
    });
{/literal}
{/script}
