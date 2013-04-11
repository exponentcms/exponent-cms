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

{css unique="product-edit" link="`$asset_path`css/product_edit.css" corecss="tree,panels"}

{/css}

<div id="editevent" class="module event edit">
    {if $record->id != ""}
        <h1>{'Edit Information for'|gettext} {$record->product_name}</h1>
    {else}
        <h1>{'New'|gettext} {$record->product_name}</h1>
    {/if}
    {ddrerank model="expDefinableField" items=$definablefields label="User Input Fields"|gettext id="definable_field_registrant" sortfield="name"}
    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="product_type" value=$record->product_type}
        {control type="hidden" name="product_type_id" value=$record->product_type_id}
        
        <div id="editproduct-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
	            <li><a href="#tab2"><em>{'Dates'|gettext}</em></a></li>
	            <li><a href="#tab3"><em>{'Pricing'|gettext}</em></a></li>
                <li><a href="#tab4"><em>{'Options'|gettext}</em></a></li>
	            <li><a href="#tab5"><em>{'Images & Files'|gettext}</em></a></li>
	            <li><a href="#tab6"><em>{'SEO'|gettext}</em></a></li>
				<li><a href="#tab7"><em>{'User Input Fields'|gettext}</em></a></li>
				<li><a href="#tab8"><em>{'Waiver'|gettext}</em></a></li>
				<li><a href="#tab9"><em>{'Status'|gettext}</em></a></li>
            </ul>
            <div class="yui-content">
                <div id="tab1">
                    {control type="text" name="title" label="Event Title"|gettext value=$record->title}
                    {control type="text" name="quantity" label="Number of seats available"|gettext filter=integer size=4 value=$record->quantity}
					{control type="text" name="location" label="Event Location"|gettext value=$record->location}
                    {*{control type="editor" name="summary" label="Event Summary"|gettext rows=3 cols=45 value=$record->summary}*}
                    {control type="textarea" name="summary" label="Event Summary"|gettext rows=3 cols=45 value=$record->summary}
                    {control type="editor" name="body" label="Event Description"|gettext height=250 value=$record->body}
                </div>
                <div id="tab2">
                    <h2>{'Event Date/Time'|gettext}</h2>
					{control type="yuicalendarcontrol" name="eventdate" label="Start Date of Event"|gettext value=$record->eventdate}
					{control type="yuicalendarcontrol" name="eventenddate" label="End Date of Event"|gettext value=$record->eventenddate}
                    {control type="datetimecontrol" name="event_starttime" label="Start Time"|gettext value=$record->event_starttime+$record->eventdate showdate=false}
                    {control type="datetimecontrol" name="event_endtime" label="End Time"|gettext value=$record->event_endtime+$record->eventdate showdate=false}
                    <h2>{'Signup Cutoff'|gettext}</h2>
					{control type="yuicalendarcontrol" name="signup_cutoff" label="No registrations after"|gettext value=$record->signup_cutoff showtime = true}
                </div>
                <div id="tab3">
                    {control type="text" name="base_price" label="Event Price"|gettext value=$record->base_price filter=money}
                    {*{group label="General Pricing"|gettext}*}
                        {*<table>*}
                            {*<tr>*}
                                {*<td>{control type="text" name="base_price" label="Base Price"|gettext value=$record->base_price filter=money}</td>*}
                            {*</tr>*}
                            {*<tr>*}
                                {*<td colspan="2">{control type="checkbox" name="use_special_price" label="Use Special Price"|gettext value=1 checked=$record->use_special_price postfalse=1}</td>*}
                            {*</tr>*}
                        {*</table>*}
                    {*{/group}*}
                    {toggle unique="early-discount" title="Early Registration Discounts"|gettext collapsed=!$record->use_early_price}
                        <blockquote>
                            {'Early Registration discounts are discounts applied when a customer registers for this event before a specified date.'|gettext}
                            {'You can configure the discount below.'|gettext}
                        </blockquote>
                        <table class="early-discount">
                            <tr>
                                <td>{control type="checkbox" name="use_early_price" label="Use an Early Registration Discount if a customer registers before"|gettext value=1 checked=$record->use_early_price postfalse=1}</td>
                            </tr>
                            <tr>
                                <td>{control type="yuicalendarcontrol" name="earlydiscountdate" label="" value=$record->earlydiscountdate showtime = true}</td>
                            <tr>
                                <td>{control type="text" name="special_price" label="then discount the price to"|gettext value=$record->special_price filter=money}</td>
                                {*<td>{control type="text" name="early_discount_amount" label=" " value=$record->early_discount_amount size=3 filter=decimal}</td>*}
                                {*<td>{control type="dropdown" name="early_discount_amount_mod" label=" " items=$record->early_discount_amount_modifiers value=$record->early_discount_amount_mod}</td>*}
                            </tr>
                        </table>
                    {/toggle}
                    {toggle unique="quantity-discount" title="Quantity Discounts"|gettext collapsed=empty($record->quantity_discount_amount)}
                        <blockquote>
                            {'Quantity discounts are discounts applied when a customer registers a certain number of people for this event.'|gettext}
                            {'You can configure how the discounts work by setting the discount rules below.'|gettext}
                        </blockquote>
                        <table class="qty-discount">
                            <tr>
                                <td>{'If a customer registers more than'|gettext} </td>
                                <!--td>{control type="dropdown" name="quantity_discount_num_items_mod" label=" " items=$record->quantity_discount_items_modifiers value=$record->quantity_discount_num_items}</td-->
                                <td>{control type="text" name="quantity_discount_num_items" label=" " value=$record->quantity_discount_num_items size=3 filter=integer}</td>
                                <td>{'people, then discount the price by'|gettext}</td>
                                <td>{control type="text" name="quantity_discount_amount" label=" " value=$record->quantity_discount_amount size=3 filter=decimal}
                                <td>{control type="dropdown" name="quantity_discount_amount_mod" label=" " items=$record->early_discount_amount_modifiers value=$record->quantity_discount_amount_mod}</td>
                            </tr>
                            <tr>
                                <td colspan="6">{control type="checkbox" name="quantity_discount_apply" label="Only apply a discount to registrations over the discount threshold"|gettext value=1 checked=$record->quantity_discount_apply postfalse=1}</td>
                            </tr>
                        </table>
                    {/toggle}
                    {group label="Tax Class"|gettext}
                        {control type="dropdown" name="pricing[tax_class_id]" label="" frommodel=taxclass key=id display=name includeblank="-- No Tax Required --"|gettext value=$record->tax_class_id|default:1}
                        {icon controller="tax" action="manage" text="Manage Tax Classes"|gettext}
                    {/group}
                </div>
                <div id="tab4">
                    <h2>{'Add options to your product.'|gettext}</h2>
                    {icon class="manage" controller=ecomconfig action=options text="Manage Product Options"|gettext}{br}
                    {'By simply selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.'|gettext}{br}
                    {'By default, the user is NOT required to make a selection.  However, if you select the Required checkbox, the user will be forced to make a selection from that option group.'|gettext} {br}
                    {'Select Single presents the option group as a dropdown field where they may select one and only option.'|gettext}{br}
                    {'Select Multiple presents the options as a checkbox group where the user may select multiple options'|gettext}.{br}
                    {'Selecting the Default radio button for an option will cause that option to be selected by default.'|gettext} {br}{br}
                    {include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
                </div>
                <div id="tab5">
                    {control type=files name=mainimages label="Main Images"|gettext subtype="mainimage" value=$record->expFile description="Images to show for your event"|gettext}
                    <div class="additional-images">
                        {control type=files name=images label="Additional Images"|gettext subtype="images" value=$record->expFile description="Additional images to show for your event"|gettext}
                    </div>
					{control type=files name=brochures label="Additional File Attachments"|gettext subtype="brochures" value=$record->expFile description="Attach Product Brochures, Docs, Manuals, etc."|gettext}
                </div>
                <div id="tab6">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Description"|gettext value=$record->meta_description}
                    {control type="textarea" name="meta_description" label="Meta Keywords"|gettext value=$record->meta_keywords}
                </div>
				<div id="tab7">
			        <h2>{'User Input Fields'|gettext}</h2>
                    {icon class="manage" controller="expDefinableField" action="manage"}
					{foreach from=$definablefields item=fields}
                        {$checked = false}
                        {foreach from=$record->expDefinableField.registrant item=selected}
                            {if $fields->id == $selected->id}
                                {$checked = true}
                            {/if}
                        {/foreach}
						{control type="checkbox" name="expDefinableField[registrant][]" label="`$fields->name` - `$fields->type`" value="`$fields->id`" checked="`$checked`"}
					{/foreach}
                </div>
				<div id="tab8">
					{control type="checkbox" name="require_terms_and_condition" label="Require Waiver"|gettext value=1 checked=$record->require_terms_and_condition}
					{control type="editor" name="terms_and_condition" label="Waiver"|gettext rows=8 cols=55 value=$record->terms_and_condition}
					{control type="radiogroup" name="terms_and_condition_toggle" label=" " items="Always Show,Toggle"|gettxtlist values="0,1" default=$record->terms_and_condition_toggle|default:0}
				</div>
				<div id="tab9">
					<h2>{'Active/Inactive'|gettext}</h2>
					{control type="radiogroup" name="active_type" label=" " items="Active,Inactive"|gettxtlist values="0,2" default=$record->active_type|default:0}
					<h2>{'Status'|gettext}</h2>
					{control type="checkbox" name="product_status_id" label="Open for Registration"|gettext value=1 checked=$record->product_status_id|default:1}
				</div>
            </div>
        </div>
        <div class="loadingdiv">{'Loading'|gettext}</div>
        {control type="buttongroup" submit="Save Event"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="authtabs" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	 YUI(EXPONENT.YUI3_CONFIG).use("get", "exptabs", "node-load","event-simulate", function(Y) {
        Y.expTabs({srcNode: '#editproduct-tabs'});
		Y.one('#editproduct-tabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}