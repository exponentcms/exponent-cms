{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
    <div id="msg-queue" class="msg-queue notice" xmlns="http://www.w3.org/1999/html">
        <div class="msg">{$product->user_message}</div>
    </div>
{/if}

<div class="module store show event-registration product">
<div class="vevent">
    <div class="image" style="padding:0px 10px 10px;float:left;overflow: hidden;">
    {if $product->expFile.mainimage[0]->url != ""}
        {img file_id=$product->expFile.mainimage[0]->id alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img photo" id="enlarged-image"}
    {else}
        {img src="`$asset_path`images/no-image.jpg" alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image"}
    {/if}
        {clear}
    </div>

    <div class="bd">
        <h2><span class="dtstart">{$product->eventdate|format_date:$smarty.const.DISPLAY_DATE_FORMAT}<span class="value-title" title="{date('c',$product->eventdate)}"></span></span>
            {if (!empty($product->eventenddate) && $product->eventdate != $product->eventenddate)} {'to'|gettext} <span class="dtend">{$product->eventenddate|format_date:$smarty.const.DISPLAY_DATE_FORMAT}<span class="value-title" title="{date('c',$product->eventenddate)}"></span></span>{/if}</h2>
        <hr>
        <h3><div><span class="summary">{$product->title}</span></div></h3>
        {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $product->poster == $user->id)}
                    {icon controller="store" action="edit" id=$product->id title="Edit this entry"|gettext}
                    {icon controller="store" action=copyProduct class="copy" record=$product text="Copy" title="Copy `$product->title` "}
                {/if}
                {if $permissions.delete || ($permissions.create && $product->poster == $user->id)}
                    {icon controller="store" action="delete" id=$product->id title="Delete this entry"|gettext}
                {/if}
            </div>
        {/permissions}
        <span><h4>{($product->eventdate+$product->event_starttime)|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
            {if $product->eventdate+$product->event_starttime != $product->eventdate+$product->event_endtime}
                - {($product->eventdate+$product->event_endtime)|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
                {time_duration start=$product->eventdate+$product->event_starttime end=$product->eventdate+$product->event_endtime assign=dur}
                <span class="duration"><span class="value-title" title="{expDateTime::duration($product->eventdate+$product->event_starttime,$product->eventdate+$product->event_endtime,true)}"></span></span>
                <em class="attribution">({if !empty($dur.h)}{$dur.h} {'hour'|gettext|plural:$dur.h}{/if}{if !empty($dur.h) && !empty($dur.m)} {/if}{if !empty($dur.m)}{$dur.m} {'minute'|gettext|plural:$dur.m}{/if})</em>
            {/if}
            </h4></span>
        {if !empty($product->location)}
            <h4>{'Location'|gettext}: <span class="location">{$product->location}</span></h4>
        {else}
            <span class="hide">
               {'Location'|gettext}:
               <span class="location">
                   {$smarty.const.ORGANIZATION_NAME}
               </span>
           </span>
        {/if}
        <div class="bodycopy">
            <span class="description">
            {if $product->body}
                {$product->body}
            {/if}
            </span>
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
                    <span class="tickets">
                      <span class="hoffer">
                    <span class="label">{'Registration Closes:'|gettext} </span>
                    <span class="value pricevaliduntil">{$product->signup_cutoff|format_date}</span>{br}
                    <span class="label">{'Seats Available:'|gettext} </span>
                    <span class="value"><span class="quantity">{$product->spacesLeft()}</span> {'of'|gettext} {$product->quantity}</span>{br}
                    {if $product->multi_registrant}
                        <div class="seatsContainer">
                            <div class="seatStatus">
                                {$seats = implode(',',range(1,min($product->spacesLeft(),12)))}
                                {control type=dropdown class="2col" name=qtyr label="Select number of seats"|gettext items=$seats value=$count}
                            </div>
                    {else}
                        {control type=hidden name=qtyr value=1}
                    {/if}
                        <div class="seatAmount prod-price">
                            {if $product->hasOptions()}
                                <div class="attribution">{'Starting at'|gettext}:{br}</div>
                            {/if}
                            {if $product->base_price}
                                <span class="currency hide">{$smarty.const.ECOM_CURRENCY}</span>
                                {if $product->use_special_price}
                                    <span class="regular-price on-sale">{$product->base_price|currency}</span>
                                    <span class="sale-price price">{$product->special_price|currency}&#160;<sup>{"Early!"|gettext}</sup></span>
                                {else}
                                    <span class="regular-price price">{$product->base_price|currency}</span>
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
                      </span>
                    </span>
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
                            {control type=hidden name="ticket_types" value="1"}
                            {control type=hidden name="options_shown" value=$product->id}
                            {foreach from=$product->optiongroup item=og}
                                {if $og->hasEnabledOptions()}
                                    <div class="option {cycle values="odd,even"}">
                                        {if $og->allow_multiple}
                                            {*{optiondisplayer product=$product options=$og->title view=checkboxes display_price_as=total selected=$params.options}*}
                                            {optiondisplayer product=$product options=$og->title view=checkboxes display_price_as=diff selected=$params.options}
                                        {else}
                                            {*{optiondisplayer product=$product options=$og->title view=dropdown display_price_as=total selected=$params.options}*}
                                            {optiondisplayer product=$product options=$og->title view=dropdown display_price_as=diff selected=$params.options}
                                        {/if}
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                    {/if}

                    {$controls = $product->getAllControls($product->multi_registrant)}
                    {$paged = false}
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
                            <div id="form-pages"></div>
                            {foreach $controls as $control}
                                {$ctlname = $control->name}
                                {if !$control@first && get_class($control->ctl) == 'pagecontrol'}
                                    {$paged = true}
                                   </fieldset>
                               {/if}
                                {$product->showControl($control,"registrant[`$ctlname`][]",null,$registrant->$ctlname)}
                            {/foreach}
                            {if get_class($controls[0]->ctl) == 'pagecontrol'}
                                </fieldset>
                            {/if}
                            {if $paged}
                                {script unique="paged_event" jquery='jquery.validate,jquery.stepy'}
                                {literal}
                                    $('#addtocart{/literal}{$product->id}{literal}').stepy({
                                        validate: true,
                                        block: true,
                                        errorImage: true,
                                        btnClass: '{/literal}{button_style}{literal}',
                                        titleClick: true,
                                        titleTarget: '#form-pages',
                                    });
                                {/literal}
                                {/script}
                            {/if}
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
                    <button type="submit" class="add-to-cart-btn {button_style}"
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
</div>

{script unique="expanding-text" yui3mods="yui"}
{literal}
    YUI().use("anim-easing","node","anim", function(Y) {
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
    });
{/literal}
{/script}
{if $product->multi_registrant && $product->forms_id}
{script unique="multi-registrants" yui3mods="yui"}
{literal}
    YUI().use("anim-easing","node","io", function(Y) {
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
{/if}
