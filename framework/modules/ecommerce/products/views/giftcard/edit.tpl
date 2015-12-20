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

{css unique="product-edit" link="`$asset_path`css/product_edit.css" corecss="tree,panels"}

{/css}

<div id="editproduct" class="module store edit">
    {if $record->id != ""}
        <h1>{'Edit Information for'|gettext} {$record->product_name}</h1>
    {else}
        <h1>{'New'|gettext} {$record->product_name}</h1>
    {/if}

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="product_type" value=$record->product_type}
        
        <div id="editproduct-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>{'General Info'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Pricing'|gettext}</em></a></li>
	            <li><a href="#tab3"><em>{'Files & Images'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
	            <div id="tab1">
	                {control type="text" name="title" label="Title"|gettext value=$record->title focus=1}
	                {*{control type="textarea" name="summary" label="Gift Card Summary"|gettext rows=3 cols=45 value=$record->summary}*}
	                {control type="editor" name="body" label="Gift Card Description"|gettext height=250 value=$record->body}
	            </div>
                <div id="tab2">
   	                {control type="text" name="base_price" label="Purchase increment dollar amount"|gettext value=$record->base_price filter=money description='Enter the minimum/multiple amount for gift cards'|gettext}
   	            </div>
	            <div id="tab3">
	                {control type=files label="Main Image"|gettext name=files subtype="mainimage" accept="image/*" value=$record->expFile limit=1 folder=$config.upload_folder}
	            </div>
            </div>
        </div>
	    {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
        {loading}
        {control type="buttongroup" submit="Save Gift Card"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="authtabs" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#editproduct-tabs'});
		Y.one('#editproduct-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
