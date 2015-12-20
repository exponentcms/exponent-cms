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
        
        <div id="editproduct-tabs" class="">
            <ul class="nav nav-tabs" role="tablist">
	            <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General Info'|gettext}</em></a></li>
                <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{'Pricing'|gettext}</em></a></li>
	            <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{'Files & Images'|gettext}</em></a></li>
            </ul>            
            <div class="tab-content">
	            <div id="tab1" role="tabpanel" class="tab-pane fade in active">
	                {control type="text" name="title" label="Title"|gettext value=$record->title focus=1}
	                {*{control type="textarea" name="summary" label="Gift Card Summary"|gettext rows=3 cols=45 value=$record->summary}*}
	                {control type="editor" name="body" label="Gift Card Description"|gettext height=250 value=$record->body}
	            </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
   	                {control type="text" name="base_price" label="Purchase increment dollar amount"|gettext value=$record->base_price filter=money description='Enter the minimum/multiple amount for gift cards'|gettext}
   	            </div>
	            <div id="tab3" role="tabpanel" class="tab-pane fade">
	                {control type=files label="Main Image"|gettext name=files subtype="mainimage" accept="image/*" value=$record->expFile limit=1 folder=$config.upload_folder}
	            </div>
            </div>
        </div>
	    {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
        {loading}
        {control type="buttongroup" submit="Save Gift Card"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}