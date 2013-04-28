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

{css unique="event-show" link="`$asset_path`css/storefront.css" corecss="button,tables"}

{/css}

{css unique="event-show1" link="`$asset_path`css/eventregistration.css"}

{/css}

{if $product->user_message != ''}
    <div id="msg-queue" class="msg-queue notice">
        <div class="msg">{$product->user_message}</div>
    </div>
{/if}

<div class="module store show event-registration product">
    <div class="image" style="padding:0px 10px 10px;float:left;overflow: hidden;">
    {if $product->expFile.mainimage[0]->url == ""}
        {img src="`$asset_path`images/no-image.jpg"}
    {else}
        {img file_id=$product->expFile.mainimage[0]->id alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image"}
    {/if}
        {clear}
    </div>

    <div class="bd">
        <h2>{$product->eventdate|date_format:"%A, %B %e, %Y"}
            {if (!empty($product->eventenddate) && $product->eventdate != $product->eventenddate)} {'to'|gettext} {$product->eventenddate|date_format:"%A, %B %e, %Y"}{/if}</h2>
        <hr>
        <h3>{$product->title}</h3>
        {permissions}
            <div class="item-actions">
                {if $permissions.configure == 1 or $permissions.manage == 1}
                    {icon controller="store" action="edit" id=$product->id title="Edit this entry"|gettext}
                    {icon controller="store" action="delete" id=$product->id title="Delete this entry"|gettext}
                {/if}
            </div>
        {/permissions}
        <span><h4>{($product->eventdate+$product->event_starttime)|expdate:"g:i a"}
            {if $product->eventdate+$product->event_starttime != $product->eventdate+$product->event_endtime}
                - {($product->eventdate+$product->event_endtime)|expdate:"g:i a"}
                {time_duration start=$product->eventdate+$product->event_starttime end=$product->eventdate+$product->event_endtime assign=dur}
                <em class="attribution">({if !empty($dur.h)}{$dur.h} {'hour'|gettext|plural:$dur.h}{/if}{if !empty($dur.h) && !empty($dur.m)} {/if}{if !empty($dur.m)}{$dur.m} {'minute'|gettext|plural:$dur.m}{/if})</em>
            {/if}
            </h4></span>
        {if !empty($product->location)}
            <h4>{'Location'|gettext}: {$product->location}</h4>
        {/if}
        <div class="bodycopy">
            {if $product->body}
                {$product->body}
            {/if}
        </div>
        {clear}

        <div id="eventregform">
            {form id="addtocart`$product->id`" controller=cart action=addItem}
                {control type="hidden" name="product_id" value="`$product->id`"}
                {control type="hidden" name="base_price" value="`$product->getBasePrice()`"}
                {control type="hidden" name="product_type" value="`$product->product_type`"}
                {control type="hidden" name="orderitem_id" value="`$orderitem_id`"}
                {*{control type="hidden" name="quick" value="1"}*}
                {if $product->spacesLeft() && $product->signup_cutoff >= time()}
                    <span class="label">{'Registration Closes:'|gettext} </span>
                    <span class="value">{$product->signup_cutoff|expdate:"l, F j, Y, g:i a"}</span>{br}
                    <span class="label">{'Seats Available:'|gettext} </span>
                    <span class="value">{$product->spacesLeft()} {'of'|gettext} {$product->quantity}</span>{br}
                    {if $product->multi_registrant && $product->forms_id}
                        <div class="seatsContainer">
                            <div class="seatStatus">
                                {$seats = implode(',',range(1,min($product->spacesLeft(),12)))}
                                {control type=dropdown class="2col" name=qtyr label="Select number of seats"|gettext items=$seats value=count($registered)}
                            </div>
                    {else}
                        {control type=hidden name=qtyr value=1}
                    {/if}
                        <div class="seatAmount prod-price">
                            {if $product->hasOptions()}
                                <div class="attribution">{'Starting at'|gettext}:{br}</div>
                            {/if}
                            {if $product->base_price}
                                {if $product->use_special_price}
                                    <span class="regular-price on-sale">{$product->base_price|currency}</span>
                                    <span class="sale-price">{$product->special_price|currency}&#160;<sup>{"Early!"|gettext}</sup></span>
                                {else}
                                    <span class="regular-price">{$product->base_price|currency}</span>
                                {/if}
                                {*<span class="seatCost">{$product->base_price|currency}</span>{br}{'per seat'|gettext}*}
                                {br}{'per seat'|gettext}
                            {else}
                                <span class="seatCost">{'No Cost'|gettext}</span>
                            {/if}
                        </div>
                    {if $product->multi_registrant && $product->forms_id}
                    </div>
                    {/if}
                    {if $product->multi_registrant && $product->quantity_discount_num_items}
                        {clear}
                        <div class="label">
                            {'There is a discount of'|gettext} {if ($product->quantity_discount_amount_mod == '%')}%{$product->quantity_discount_amount}{else}{$product->quantity_discount_amount|currency}{/if}
                            {if ($product->quantity_discount_apply)}
                                {'for additional registrations'|gettext}
                            {/if}
                            {'if more than'|gettext} {$product->quantity_discount_num_items} {'people are registered'|gettext}.
                        </div>{br}{br}
                    {/if}

                    {if $product->hasOptions()}
                        {clear}
                        <h4>{'Options'|gettext}</h4>
                        <div class="product-options">
                            {control type="hidden" name="ticket_types" value="1"}
                            {control type=hidden name=options_shown value=$product->id}
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

                    {$controls = $product->getAllControls($product->multi_registrant)}
                    {if !empty($controls)}
                        {clear}
                        {if $product->multi_registrant}
                            <h2>{'Who\'s Coming?'|gettext}</h2>
                            {'Please provide the list of the people who will be attending this event'|gettext},
                            <strong>{'including yourself, if you are attending'|gettext}</strong>.
                            <div  style="overflow: auto; overflow-y: hidden;">
                            <table class="exp-skin-table" id="reg" border="0" cellpadding="3" cellspacing="0">
                                <thead>
                                    {foreach $controls as $control}
                                        <th>
                                            {if $control->ctl->required}
                                                <span style="color:Red;" title="{'This entry is required'|gettext}">*</span>
                                            {/if}
                                            <span>{$control->caption}</span>
                                        </th>
                                    {/foreach}
                                </thead>
                                <tbody>
                                    {if empty($registered)}
                                            <tr id="regform">
                                                {foreach $controls as $control}
                                                    <td>
                                                        {$product->showControl($control,"registrant[`$control->name`][]",null,null,false,true)}
                                                    </td>
                                                {/foreach}
                                            </tr>
                                    {else}
                                        {foreach $registered as $registrant}
                                            <tr id="regform">
                                                {foreach $controls as $control}
                                                    {$ctlname = $control->name}
                                                    <td>
                                                        {$product->showControl($control,"registrant[`$ctlname`][]",null,$registrant->$ctlname,false,true)}
                                                    </td>
                                                {/foreach}
                                            </tr>
                                        {/foreach}
                                    {/if}
                                </tbody>
                            </table>
                            </div>
                        {else}
                            <h3>{'Registration'|gettext}</h3>
                            {'Please complete the following information to register'|gettext}
                            {foreach $controls as $control}
                                {$ctlname = $control->name}
                                <td>
                                    {$product->showControl($control,"registrant[`$ctlname`][]",null,$registrant->$ctlname)}
                                </td>
                            {/foreach}
                        {/if}
                    {/if}
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
                    {/if}
                    {clear}
                    <button type="submit" class="add-to-cart-btn awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}"
                            style="margin: 20px auto; display: block;" rel="nofollow">
                        {"Register for this Event"|gettext}
                    </button>
                {else}
                    {if $product->spacesLeft()}
                        <span class="label">{'Seats Available:'|gettext} </span>
                        <span class="value"> {'None'|gettext}</span>{br}
                    {/if}
                    <span class="label">{'Registration is Closed!'|gettext}</span>
                {/if}
            {/form}
        </div>
    </div>
{clear}
</div>

{script unique="expanding-text" yui3mods="yui"}
{literal}
    YUI().use("anim-easing","node","anim","io", function(Y) {
        // This is for the terms and condition toogle
        if (Y.one('.more-text') != null) {
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
        }

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

        var addcounter = 0;

        function addRow(tableID) {
            var row = Y.one("#regform"); // find row to copy
			var table = Y.one("#reg"); // find table to append to
			var clone = row.cloneNode(true); // copy children too
			clone.set('id', ""); // remove id to maintain row #1 as template
            // remove current value(s) of cloned row
            clone.all('input').each(function(ctrl) {
                ctrl.set('value','');
            });
            clone.all('option').each(function(ctrl) {
                ctrl.removeAttribute('selected');
            });
			table.appendChild(clone); // add new row to end of table
        }

        function deleteRow(tableID) {
            try {
                var table = document.getElementById(tableID);
                table.deleteRow(table.rows.length - 1);
            } catch (e) {
                alert(e);
            }
        }

        if (Y.one('#qtyr') != null) {
            Y.one('#qtyr').on('change', function(e) {
                var numAsked = e.target.get('value') * 1; // because it is a string and I want a number
                var table = document.getElementById('reg');
                var change = numAsked - table.rows.length + 1;
                if (change == 0) {
                    return;
                }
                if (change < 0) {
                    // remove extra lines
                    for (var i=change; i<=-1; i++) {
                        deleteRow('reg')
                    }
                    return;
                }
                if (change > 0) {
                    // build new lines
                    for (var i=1; i<=change; i++) {
                        addRow('reg');
                    }
                    return;
                }
            });
        }
    });
{/literal}
{/script}
