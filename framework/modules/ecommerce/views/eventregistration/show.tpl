{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
 
{css unique="event-show" link="`$asset_path`css/storefront.css" corecss="button,tables"}
{literal}
	#eventregform .text-control p.helper {
		text-align: center;
		margin-left:210px;
		width:230px;
	}
	
	#eventregform .control .text {
		width: 230px;
	}
	
	#eventregform .text-control p.helper span {
		font-size: 10px;
		display: block;
	}
	
	#eventregform .radiogroup label {
		float: none;
		padding-top: 10px;
		width: 100px;
	}	
	
	#eventregform .checkbox  label {
		padding-top: 10px;
	}
	
	#eventregform .label {
		display: block;
		font-size: 114%;
		font-weight: bold;
		margin-bottom: 4px;
		margin-right: 5px;
	}
	
	#eventregform .p_addElement {
		text-align: center;
	}
	
	#eventregform .terms_and_conditions #terms_and_conditionControl {
		width: 270px;
		margin: auto;
	}

	.more-text {
		overflow: hidden;
	}
{/literal}
{/css}

 <div class="module store show event-registration">
    <h1>{$product->title}</h1>
	<div class="image" style="padding:0px 10px 10px;float:left;">
		 {if $product->expFile.mainimage[0]->url == ""}
			 {img src="{$smarty.const.ICON_RELATIVE|cat:'ecom/no-image.jpg'}"}
		 {else}
			 {img file_id=$product->expFile.mainimage[0]->id alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`"  class="large-img" id="enlarged-image"}
		   
		 {/if}
		 {clear}
	</div>
	
     <div class="bd">
         {permissions}
             <div class="item-actions">
                 {if $permissions.configure == 1 or $permissions.manage == 1}
                     {icon controller="store" action="edit" id=$product->id title="Edit this entry"|gettext}
                     {icon controller="store" action="delete" id=$product->id title="Delete this entry"|gettext}
                 {/if}
             </div>
         {/permissions}

         <div class="bodycopy">
            {if $product->summary}
                {$product->summary}
            {/if}
         </div>
         <span class="date">
             <span class="label">{'Event Date'|gettext}: </span><span class="value">{$product->eventdate|date_format:"%A, %B %e, %Y"}</span>{br}
             <span class="label">{'Start Time'|gettext}: </span><span class="value">{($product->eventdate+$product->event_starttime)|expdate:"g:i a"}</span>{br}
             <span class="label">{'End Time'|gettext}: </span><span class="value">{($product->eventdate+$product->event_endtime)|expdate:"g:i a"}</span>{br}
             {br}
             <span class="label">{'Seats Available:'|gettext} </span><span class="value">{$product->spacesLeft()} {'of'|gettext} {$product->quantity}</span>{br}
             <span class="label">{'Registration Closes:'|gettext} </span><span class="value">{$product->signup_cutoff|expdate:"l, F j, Y, g:i a"}</span>{br}
         </span>

		<div id="eventregform">		 
			
            {form id="addtocart`$product->id`" controller=cart action=addItem}

                {control type="hidden" name="product_id" value="`$product->id`"}
                {control type="hidden" name="base_price" value="`$product->base_price`"}
                {control type="hidden" name="product_type" value="`$product->product_type`"}

                {if $product->hasOptions()}
                    <div class="product-options">
                        {control type="hidden" name="ticket_types" value="1"}
                        {foreach from=$product->optiongroup item=og}
                            {if $og->hasEnabledOptions()}
                                <div class="option {cycle values="odd,even"}">
                                    {if $og->allow_multiple}
                                        {optiondisplayer product=$product options=$og->title view=checkboxes_with_quantity display_price_as=total selected=$params.options}
                                    {else}
                                        {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=total selected=$params.options}
                                    {/if}
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                {/if}

                {foreach from=$product->expDefinableField.registrant item=fields}
                    {$product->getControl($fields)}
                {/foreach}

                {if $product->require_terms_and_condition}
                    <div class="terms_and_conditions">
                        {if $product->terms_and_condition_toggle}
                            {control type=checkbox name=terms_and_condition value=1 label="I Agree To The Following <a class='toggle' href='javascript:;'>Waiver</a>" required=1}
                            <div class="more-text" style="height: 0px;">
                                {$product->terms_and_condition}
                            </div>
                        {else}
                            {control type=checkbox name=terms_and_condition value=1 label="I Agree To The Following Waiver"|gettext required=1}
                            <div class="more-text" style="height: auto;">
                                {$product->terms_and_condition}
                            </div>
                        {/if}
                    </div>
                {else}
                    <div class="more-text" style="height: 0px;"></div>
                {/if}

                <button type="submit" class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" style="margin: 20px auto; display: block;" rel="nofollow">
                    {"Register Now"|gettext}
                </button>
            {/form}
		
		</div>
     </div>
     {clear}
 </div>
 
{script unique="expanding-text" yui3mods="yui"}
{literal}
YUI().use("anim-easing","node","anim","io", function(Y) {
	// This is for the terms and condition toogle
	var content = Y.one('.more-text').plug(Y.Plugin.NodeFX, {
		to: { height: 0 },
		from: {
			height: function(node) { // dynamic in case of change
				return node.get('scrollHeight'); // get expanded height (offsetHeight may be zero)
			}
		},

		easing: Y.Easing.easeOut,
		duration: 0.5
	});

	var onClick = function(e) {
		e.halt();
		//e.toggleClass('yui-closed');
		Y.one('.event-registration .bodycopy').toggleClass('yui-closed');  
		content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
		content.fx.run();
	};
	
	var control = Y.one('.toggle');
	
	if(control != null) {
		control.on('click', onClick);
	}
	// End of script for terms and condition toogle
});
{/literal}
{/script}