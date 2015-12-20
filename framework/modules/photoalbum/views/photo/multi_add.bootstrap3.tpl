{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
        <div id="editgallery-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{"General"|gettext}</em></a></li>
            </ul>
            <div class="tab-content yui3-skin-sam">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    <h2>{'Photo Items'|gettext}</h2>
                    {control type=text name=title label="Base Title"|gettext value=$record->title description="(Optional) This will become the root title used for these photo album items."|gettext focus=1}
                    {control type="files" name="files" label="Files"|gettext accept="image/*" value=$record->expFile limit=64 folder=$config.upload_folder}
                    {if !$config.disabletags}
                        {control type="tags"}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$model_name`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                </div>
            </div>
        </div>
	    {*<div class="loadingdiv">{"Loading Multi-Photo Uploader"|gettext}</div>*}
        {loading title="Loading Multi-Photo Uploader"|gettext}
        {control type=buttongroup submit="Add Photos to Album"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}