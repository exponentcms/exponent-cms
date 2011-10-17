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

<div id="editblog" class="module blog edit yui3-skin-sam hide">
    
    {if $record->id != ""}<h1>Editing {$record->title}</h1>{else}<h1>New {$modelname}</h1>{/if}
    
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>General</em></a></li>
                <li><a href="#tab2"><em>SEO</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                {control type=text name=title label="Title" value=$record->title}
                {control type=html name=body label="Body Content" value=$record->body}
                {*control type="checkbox" name="private" label="Save as draft" value=1 checked=$record->private*}
                {control type="files" name="files" label="Files" value=$record->expFile}
                {foreach from=$record->expTag item=tag name=tags}
                    {if $smarty.foreach.tags.first == false}
                        {assign var=tags value="`$tags`,`$tag->title`"}
                    {else}
                        {assign var=tags value=$tag->title}
                    {/if}                    
                {/foreach}
                {control type="textarea" name="expTag" label="Tags (comma separated)" value=$tags}
            </div>
            <div id="tab2">
                <h2>SEO Settings</h2>
                {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title}
                {control type="textarea" name="meta_description" label="Meta Description" rows=5 cols=35 value=$record->meta_description}
                {control type="textarea" name="meta_keywords" label="Meta Keywords" rows=5 cols=35 value=$record->meta_keywords}
            </div>
            </div>
        </div>
        {control type=buttongroup submit="Save Text" cancel="Cancel"}
    {/form}   
    
</div>
<div class="loadingdiv">Loading</div>

{script unique="blogtabs" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#demo'});
	    tabview.render();
		Y.one('#editblog').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}


