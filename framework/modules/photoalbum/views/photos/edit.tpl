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

<div id="editgallery" class="module imagegallery edit">
    {if $record->id}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="editgallery-tabs" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                <li><a href="#tab3"><em>{"Image"|gettext}</em></a></li>
                <li><a href="#tab4"><em>{"SEO"|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    {control type=text name=title label="Title"|gettext value=$record->title}
                    {control type=html name=body label="Description"|gettext value=$record->body}
                    {control type="text" name="link" label="Link a Slideshow Slide"|gettext value=$record->link}
                    {control type="text" name="alt" label="Alt Tag (overwrites alt supplied in file manager)"|gettext value=$record->alt}
                </div>
                <div id="tab3">
                    {control type="files" name="files" label="Files"|gettext value=$record->expFile limit=1}
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
	    <div class="loadingdiv">{"Loading Photo Item"|gettext}</div>
        {control type=buttongroup submit="Save Photo"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="editform" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#editgallery-tabs'});
	    tabview.render();
		Y.one('#editgallery-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
