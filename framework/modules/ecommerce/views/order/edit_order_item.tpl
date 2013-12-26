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

<div class="module order edit">
    <h1>{'Editing order totals'|gettext}</h1>
    
    {form action=save_order_item}
        {control type=hidden name=id value=$oi->id}
        <blockquote>
            {'You may change the item quantity here, price, as well as edit the options and user input fields.'|gettext}{br}
            {'If you would like to change the product, please delete it and add the correct item.'|gettext}{br}
            {'Note'|gettext}:{br}
            <strong>* {'If you edit, add, or remove order items, the order will automatically recalculate the order totals.'|gettext}</strong>{br}
            <strong>* {'If this item has product options and those options modify the price, YOU must adjust the price below manually if you change the options. This will NOT recalculate the option price modifiers automatically.'|gettext}</strong>{br}
        </blockquote>
        <table width='60%'>
            <tr>
                <td>{'Item name:'|gettext}</td>
                <td>{control type=textarea name=products_name cols=40 rows=2 label="" value=$oi->products_name}</td>
            </tr>
            <tr>
                <td>{'Item model:'|gettext}</td>
                <td>{$oi->products_model}</td>
            </tr>
            <tr>
                <td>{'Item price:'|gettext}</td>
                <td>{control type=text name=products_price label="" value=$oi->products_price filter=money}</td>
            </tr>
            <tr>
                <td>{'Item quantity:'|gettext}</td>
                <td>{control type=text name=quantity label="" value=$oi->quantity}</td>
            </tr>
        </table>
        {if $oi->product->hasOptions()}
            <div class="product-options">
                <h2>{$oi->products_name} {'Options'|gettext}</h2>
                {foreach from=$oi->product->optiongroup item=og}
                    {if $og->hasEnabledOptions()} 
                        <div class="option {cycle values="odd,even"}"> 
                            {if $og->allow_multiple}
                                {optiondisplayer product=$oi->product options=$og->title view=checkboxes display_price_as=diff selected=$oi->selectedOpts}
                            {else}
                                {if $og->required}
                                    {optiondisplayer product=$oi->product options=$og->title view=dropdown display_price_as=diff selected=$oi->selectedOpts required=true}
                                {else}
                                    {optiondisplayer product=$oi->product options=$og->title view=dropdown display_price_as=diff selected=$oi->selectedOpts}
                                {/if}                                           
                            {/if}
                        </div> 
                    {/if}
                {/foreach}
                <span style="font-variant:small-caps;">* {'Selection required'|gettext}.</span>
            </div>
        {/if}
        
        {if !empty($oi->product->user_input_fields) && $oi->product->user_input_fields|@count>0 }
            <div class="user-input-fields">
                <h2>{'User Input Fields'|gettext}</h2>
                {foreach from=$oi->product->user_input_fields key=uifkey item=uif}
                    <div class="user-input {cycle values="odd,even"}">
                        {if $uif.use}
                             {if $uif.is_required}
                                 {control type=text name='user_input_fields[$uifkey]' size=50 maxlength=$uif.max_length label='* '|cat:$uif.name|cat:':' required=$uif.is_required value=$oi->user_input_fields.$uifkey[$uif.name]}
                             {else}
                                 {control type=text name='user_input_fields[$uifkey]' size=50 maxlength=$uif.max_length label=$uif.name|cat:':' required=$uif.is_required value=$oi->user_input_fields.$uifkey[$uif.name]}
                             {/if}
                             {if $uif.description != ''}{$uif.description}{/if}
                        {/if}
                    </div>
                {/foreach}
            </div>
        {/if}
        {control type=buttongroup submit="Save Order Item Change"|gettext cancel="Cancel"|gettext}
    {/form}
</div>
