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

<div id="editgallery" class="module photoalbum edit multi-add">
    <h1>{'Add Multiple Images to the Photo Album'|gettext}</h1>
    <blockquote>
        {"This form allows you to add multiple photo items at one time."|gettext}&#160;&#160;
        {"You will likely though have to edit them individually later to provide a better title."|gettext}
    </blockquote>
    {form action=multi_update}
        <div id="editgallery-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
            </ul>
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'Photo Items'|gettext}</h2>
                    {control type=text name=title label="Base Title"|gettext value=$record->title description="(Optional) This will become the root title used for these photo album items."|gettext}
                    {control type="files" name="files" label="Files"|gettext accept="image/*" value=$record->expFile limit=64}
                    {if !$config.disabletags}
                        {control type="tags"}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Multi-Photo Uploader"|gettext}</div>
        {control type=buttongroup submit="Add Photos to Album"|gettext cancel="Cancel"|gettext}
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
