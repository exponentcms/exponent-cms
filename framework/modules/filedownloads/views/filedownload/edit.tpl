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

<div id="editfile" class="module filedownload edit hide exp-skin-tabview">
    
    {if $record->id != ""}<h1>Editing {$record->title}</h1>{else}<h1>New File Download</h1>{/if}
    
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>General</em></a></li>
				{if $config.usestags}<li><a href="#tab2"><em>Tags</em></a></li>{/if}
                <li><a href="#tab3"><em>Files</em></a></li>
                <li><a href="#tab4"><em>SEO</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                {control type=text name=title label="Title" value=$record->title}
                {control type=html name=body label="Body Content" value=$record->body}
            </div>
			{if $config.usestags}
            <div id="tab2">
                {foreach from=$record->expTag item=tag name=tags}
                    {if $smarty.foreach.tags.first == false}
                        {assign var=tags value="`$tags`,`$tag->title`"}
                    {else}
                        {assign var=tags value=$tag->title}
                    {/if}                    
                {/foreach}
                {control type="textarea" name="tags" label="Tags (comma separated)" rows=3 cols=35 value=$tags}
            </div>
			{/if}
            <div id="tab3">
                {control id="downloadable" type="files" name="downloadable" label="File for Download" subtype=downloadable value=$record->expFile}
                {control id="preview" type="files" name="preview" label="Preview Image to display with above 'File for Download'" subtype=preview value=$record->expFile}
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
        {control type=buttongroup submit="Save File" cancel="Cancel"}
    {/form}   
    
</div>
<div class="loadingdiv">Loading File Download Form</div>

{script unique="editform" yui3mods=1}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
        var YAHOO=Y.YUI2;

        var tabView = new YAHOO.widget.TabView('demo');
        Y.one('#editfile').removeClass('hide').next().remove();
    });
{/literal}
{/script}
