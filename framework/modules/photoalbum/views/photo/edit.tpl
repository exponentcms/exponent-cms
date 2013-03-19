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

<div id="editgallery" class="module photoalbum edit">
    {if $record->id}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        <div id="editgallery-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                <li><a href="#tab2"><em>{"SEO"|gettext}</em></a></li>
            </ul>            
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'Photo Item'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title description='Overrides file manager \'title\''|gettext}
                    {control type="text" name="alt" label="'Alt' tag"|gettext value=$record->alt description='Overrides file manager \'alt\''|gettext}
                    {control type="files" name="files" label="Files"|gettext value=$record->expFile limit=1}
                    {control type=html name=body label="Description"|gettext value=$record->body}
                    {*{control type="text" name="link" label="Link this Slideshow Slide to a URL"|gettext value=$record->link}*}
                    {control type=url name="link" label="Link this Slideshow Slide to a URL"|gettext value=$record->link}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                </div>
                <div id="tab2">
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
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#editgallery-tabs'});
		Y.one('#editgallery-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
