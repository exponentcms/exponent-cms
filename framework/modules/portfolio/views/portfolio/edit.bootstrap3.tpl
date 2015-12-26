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

<div id="editportfolio" class="module blog edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$model_name}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        <div id="editportfolio-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="tab-content yui3-skin-sam">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                    <h2>{'Portfolio Piece'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title focus=1}
                    {control type=html name=body label="Description"|gettext value=$record->body}
                    {control type="checkbox" name="featured" label="Feature this Portfolio Piece"|gettext|cat:"?" checked=$record->featured value=1}
                    {if !$config.disabletags}
                        {control type="tags" value=$record}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$model_name`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                </div>
                {if $config.filedisplay}
                    <div id="tab2" role="tabpanel" class="tab-pane fade">
                        {control type="files" name="files" label="Files"|gettext value=$record->expFile folder=$config.upload_folder}
                    </div>
                {/if}
                <div id="tab3" role="tabpanel" class="tab-pane fade">
                     <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                    {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title description='Override the item title for search engine entries'|gettext}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description description='Override the item summary for search engine entries'|gettext}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                    {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                    {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                </div>
            </div>
        </div>
	    {*<div class="loadingdiv">{'Loading Portfolio Item'|gettext}</div>*}
        {loading title='Loading Portfolio Item'|gettext}
        {control type=buttongroup submit="Save Portfolio Piece"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}