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

{css unique="product-show" link="`$asset_path`css/storefront.css" corecss="button,tables"}

{/css}

{css unique="product-show" link="`$asset_path`css/ecom.css"}

{/css}

{css unique="giftcard-listing" link="`$asset_path`css/giftcards.css"}

{/css}
<div class="module store show giftcards">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=create product_type=giftcard text="Add a Gift Card"|gettext}
        {/if}
        {if $permissions.manage}
             {icon action=manage text="Manage Products"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
	{form id="addtocart" controller=cart action=addItem}
        {control type="hidden" name="product_type" value="giftcard"}
        <table cellspacing="0" cellpadding="0" border="0" width="100%">
            <tbody>
                <tr>
                    <td>
                        {group label="Select the style of your gift card"|gettext}
                            <h3>{'Card Style'|gettext}</h3>
                            {foreach from=$giftcards item=giftcard name=gifts}
                                <div class="picwrapper">
                                    {img file_id=$giftcard->expFile.mainimage[0]->id w=250 alt=$giftcard->image_alt_tag|default:"Image of `$giftcard->title`" title="`$giftcard->title`"  class="gc-preview"}
                                    {if $records.product_id == $giftcard->id || (empty($records.product_id) && $smarty.foreach.gifts.first)}
                                        {control type="radio" class="radio-gift" name="product_id" label="`$giftcard->title`" value="`$giftcard->id`" checked=1}
                                        {$option_set=$giftcard->id}
                                    {else}
                                        {control type="radio" class="radio-gift" name="product_id" label="`$giftcard->title`" value="`$giftcard->id`"}
                                    {/if}
                                </div>
                            {foreachelse}
                                {message text='There are no gift cards available!'|gettext}
                                {permissions}
                                    <div class="module-actions">
                                        {if $permissions.create || $permissions.edit}
                                            {icon class="add" controller=store action=edit product_type=giftcard text="Add a Gift Card"|gettext}
                                        {/if}
                                    </div>
                                {/permissions}
                            {/foreach}
                        {/group}
                    </td>
                </tr>
                <tr>
                    <td>
                        {group label="Select the amount of the gift card"|gettext}
                            <h3>{'Card Amount'|gettext}</h3>
                            {* FIXME Need to convert to smarty controls *}
                            <div class="radio control" id="25dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="25" name="card_amount" id="25d" {if $records.card_amount == "25"  || empty($records.card_amount) && empty($records.card_amount_txt)}checked="checked"{/if}></td><td><label class="label " for="25d">$25</label></td></tr></tbody></table></div>
                            <div class="radio control" id="50dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="50" name="card_amount" id="50d" {if $records.card_amount == "50"}checked="checked"{/if}></td><td><label class="label " for="50d">$50</label></td></tr></tbody></table></div>
                            <div class="radio control" id="75dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="75" name="card_amount" id="75d" {if $records.card_amount == "75"}checked="checked"{/if}></td><td><label class="label " for="75d">$75</label></td></tr></tbody></table></div>
                            <div class="radio control" id="100dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="100" name="card_amount" id="100d" {if $records.card_amount == "100"}checked="checked"{/if}></td><td><label class="label " for="100d">$100</label></td></tr></tbody></table></div>
                            <div class="radio control" id="150dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="150" name="card_amount" id="150d" {if $records.card_amount == "150"}checked="checked"{/if}></td><td><label class="label " for="150d">$150</label></td></tr></tbody></table></div>
                            <div class="radio control" id="200dControl"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td class="input"><input type="radio" onclick="clearTxtField()" class="radio amount" value="200" name="card_amount" id="200d" {if $records.card_amount == "200"}checked="checked"{/if}></td><td><label class="label " for="200d">$200</label></td></tr></tbody></table></div>
                            <div class="text-control control  man_amount " id="card_amount_txtControl"><label class="label" for="card_amount_txt">{'Other Amount'|gettext}</label><input type="text" onchange="clearRadioButtons();" onpaste="return money_filter.onpaste(this, event);" onfocus="money_filter.onfocus(this);" onblur="money_filter.onblur(this);" onkeypress="return money_filter.on_key_press(this, event);" class="text man_amount form-control" size="6" value="{$records.card_amount_txt}" name="card_amount_txt" id="card_amount_txt"></div>
                            {if !empty($config.minimum_gift_card_purchase)}<em>{'There is a'|gettext} {$config.minimum_gift_card_purchase|currency} {'Minimum on gift card purchases.'|gettext}</em>{/if}
                        {/group}
                    </td>
                </tr>
                <tr>
                    <td>
                        {group label='Personalize your gift card'|gettext}
                            {control type="hidden" name="options_shown" id="options_shown" value=$option_set}
                            <h4>{'The \'To\' and \'From\' name may be added at no additional charge.'|gettext}</h4>
                            <div class="text-control control  "><label class="label">{"To:"|gettext}</label><input type="text" class="text form-control" size="20" name="toname" id="toname" value="{$records.toname}"></div>
                            <div class="text-control control  "><label class="label">{"From"|gettext}:</label><input type="text" class="text form-control" size="20" value="{$records.fromname}" name="fromname" id="fromname"></div>
                            {if !empty($config.custom_message_product)}<h4><em>{'Adding a custom message will add'|gettext} {$config.custom_message_product|currency} {'to the price of your gift card.'|gettext}</em></h4>{/if}
                            <div class="text-control control "><label class="label">{"Custom Message (100 characters max)"|gettext}</label>{control type="textarea" name="msg" cols="45" rows="3" value=$records.msg}</textarea></div>
                        {/group}
                    </td>
                </tr>
            </tbody>
        </table>
        <a id="submit-giftcard" href="javascript:{ldelim}{rdelim}" class="{button_style size=large color=blue} exp-ecom-link" rel="nofollow">{'Add selected gift card to cart'|gettext}</a>
	{/form}
</div>

{script unique="giftcard-submit" jquery="1"}
	{literal}
        var clearTxtField = function() {
            $('#card_amount_txt').val("");
        };
        var clearRadioButtons = function() {
            $('.radio.amount').each(function() {
                this.checked = false;
            });
        };
        $('.radio-gift').change(function(ev) {
            $( "#options_shown" ).val(ev.target.value);
        });
        $( "#submit-giftcard" ).click(function() {
            $( "#addtocart" ).submit();
        });
	{/literal}
{/script}

{script unique="a2cgc" yui3mods="node"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var bp = {/literal}{$config.minimum_gift_card_purchase}{literal};
        var da = Y.one('#card_amount_txt');
        da.on('blur', function(e,o){
            var newint = parseInt(e.target.get('value').replace('$',""));
            e.target.set('value','{/literal}{currency_symbol}{literal}'+Math.ceil(newint/bp)*bp+'.00');
        });

        Y.all('#toname','#fromname').on('keyup', function(e){
            var targ = e.target;
            var junk = [':',')','-','!','@','#','$','%','^','&','*','(',')','_','+','=','-','`','~','{','}','|','[',']','\\',':','"',';','\'','<','>','?',',','.','/'];
            for (var jk in junk ) {
                targ.set('value', targ.get('value').replace(junk[jk],""));
            }
        });
    });
{/literal}
{/script}