{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div id="editcategory" class="storecategory edit">
	<div class="form_header">
        <h2>{'Edit Store Category'|gettext}</h2>
        <blockquote>{'Complete and save the form below to configure this store category'|gettext}</blockquote>
	</div>
	{if $node->id == ""}
        {$action=create}
	{else}
        {$action=update}
	{/if}
    <div id="mainform">
        {form controller=storeCategory action=$action}
            {control type=hidden name=id value=$node->id}
            {control type=hidden name=parent_id value=$node->parent_id}
            {control type=hidden name=rgt value=$node->rgt}
            {control type=hidden name=lft value=$node->lft}
            <div id="cattabs" class="">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#general" role="tab" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
                    <li role="presentation"><a href="#seo" role="tab" data-toggle="tab"><em>{'SEO'|gettext}</em></a></li>
                    {*<li><a href="#events1"><em>{'Events'|gettext}</em></a></li>*}
                    {if $product_types}
                        {foreach from=$product_types key=key item=item}
                            <li role="presentation"><a href="#{$item}" role="tab" data-toggle="tab"><em>{$key} {'Product Types'|gettext}</em></a></li>
                        {/foreach}
                    {/if}
                </ul>
                <div class="tab-content">
                    <div id="general" role="tabpanel" class="tab-pane fade in active">
                        {control type=text name=title label="Category Name"|gettext value=$node->title focus=1}
                        {control type="checkbox" name="is_active" label="This category is active"|gettext value=1 checked=$node->is_active|default:1}
                        {control type="files" name="image" label="Category Image"|gettext accept="image/*" value=$node->expFile folder=$config.upload_folder}
                        {control type=editor name=body label="Category Description"|gettext value=$node->body}
                    </div>
                    <div id="seo" role="tabpanel" class="tab-pane fade">
                        {control type=text name=sef_url label="SEF URL"|gettext value=$node->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
                        {control type=text name=canonical label="Canonical URL"|gettext value=$node->canonical description='Helps get rid of duplicate search engine entries'|gettext}
                        {control type=text name=meta_title label="Meta Title"|gettext value=$node->meta_title description='Override the item title for search engine entries'|gettext}
                        {control type=text name=meta_description label="Meta Description"|gettext value=$node->meta_description description='Override the item summary for search engine entries'|gettext}
                        {control type=text name=meta_keywords label="Meta Keywords"|gettext value=$node->meta_keywords description='Comma separated phrases - overrides site keywords and item tags'|gettext}
                        {control type="checkbox" name="meta_noindex" label="Do Not Index"|gettext|cat:"?" checked=$section->meta_noindex value=1 description='Should this page be indexed by search engines?'|gettext}
                        {control type="checkbox" name="meta_nofollow" label="Do Not Follow Links"|gettext|cat:"?" checked=$section->meta_nofollow value=1 description='Should links on this page be indexed and followed by search engines?'|gettext}
                    </div>
                     {*<div id="events1">*}
                        {*{control type="checkbox" name="is_events" label="This category is used for events"|gettext value=1 checked=$node->is_events}*}
                        {*{control type="checkbox" name="hide_closed_events" label='Don\'t Show Closed Events'|gettext value=1 checked=$node->hide_closed_events}*}
                    {*</div>*}
                    {if $product_types}
                        {foreach from=$product_types key=key item=item}
                            <div id="{$item}" role="tabpanel" class="tab-pane fade">
                                <h2>{$key} {'Product Types'|gettext}</h2>
                                {$product_type.$item}
                            </div>
                        {/foreach}
                    {/if}
                </div>
            </div>
            {loading}
            {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>
