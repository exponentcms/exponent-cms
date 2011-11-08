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

<div id="editfile" class="module filedownload edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New File Download'|gettext}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="editfile-tabs" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
				{if $config.usestags}<li><a href="#tab2"><em>{'Tags'|gettext}</em></a></li>{/if}
                <li><a href="#tab3"><em>{'Files'|gettext}</em></a></li>
                <li><a href="#tab4"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
            <div id="tab1">
                {control type=text name=title label="Title"|gettext value=$record->title}
                {control type=html name=body label="Body Content"|gettext value=$record->body}
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
                {control type="textarea" name="tags" label="Tags (comma separated)"|gettext rows=3 cols=35 value=$tags}
            </div>
			{/if}
            <div id="tab3">
                {control id="downloadable" type="files" name="downloadable" label="File for Download"|gettext subtype=downloadable value=$record->expFile}
                {control id="preview" type="files" name="preview" label="Preview Image to display with above 'File for Download'"|gettext subtype=preview value=$record->expFile}
            </div>
            <div id="tab4">
                 <h2>{'SEO Settings'|gettext}</h2>
                {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description}
                {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords}
            </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading File Download Item"|gettext}</div>
        {control type=buttongroup submit="Save File"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="editform" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#editfile-tabs'});
	    tabview.render();
		Y.one('#editfile-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
