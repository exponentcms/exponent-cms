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

<table border="0" cellspacing="0" cellpadding="0" class="cart-item">
    <tr>
        <td class="cart-image">
            {if $item->product->expFile.mainimage[0]->id}
                <a style="margin: 0px; padding:0px" href="{link action=show controller=store title=$item->product->getSEFURL()}">{img file_id=$item->product->expFile.mainimage[0]->id h=50 w=50 zc=1 class="border"}</a>
            {else}
                {img src="`$asset_path`images/no-image.jpg"}
                {'No Image Available'|gettext}
            {/if}
        </td>
        <td>
            <span class="itemname"><strong><a style="margin: 0px; padding:0px" href="{link action=show controller=store title=$item->product->getSEFURL()}">{$item->products_name}</a></strong></span>
            <div class="itembody">
                {*$item->product->body|strip_tags|truncate:50:"..."*}
                {$item->getCartSummary()}
                {$item->getShippingSurchargeMessage()}
            </div>
            {if $options|@count > 0 || $item->getUserInputFields()!= ''}
                <div class="options">
                    <a href="#" class="infoicon">{"Additional Information"|gettext}</a>
                    <div class="exp-dropmenu">
                        {if $options|@count > 0}
                            <div class="hd" style="padding:0 5px">
                                <span class="type-icon"></span><h6>{"Selected Product Options"|gettext}</h6>
                            </div>
                            <div class="bd">
                                <ul>
                                    {foreach key=key from=$options item=option}
                                        <li>{$option[1]} {if $option[4]!=0}({$option[3]}${$option[4]}){/if}</li>
                                    {/foreach}
                                </ul> 
                            </div>
                        {/if}
                        
                        {if $item->getUserInputFields() != ''}
                            <div class="hd" style="padding:0 5px">
                                {if $options|@count <= 0}<span class="type-icon"></span>{/if}<h6>{"Additional Information"|gettext}</h6>
                            </div>
                            <div class="bd">
                                {$item->getUserInputFields()}
                            </div>
                        {/if}
                    </div>
                </div>        
                {script unique="z-index" }
                {literal}
                
                YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
                    var opts = Y.all(".options");
                    opts.each(function(n,k){
                        n.setStyle('zIndex',opts.size()-k);
                        n.one(".exp-dropmenu").setStyle('zIndex',opts.size()+1);
                    });
                });
                
                {/literal}
                {/script}
            {/if}

        </td>
    </tr>
</table>
