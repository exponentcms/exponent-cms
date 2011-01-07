{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
                <a style="margin: 0px; padding:0px" href="{link action=showByTitle controller=store title=$item->product->sef_url}">{img file_id=$item->product->expFile.mainimage[0]->id h=50 w=50 zc=1}</a>
            {else}
                No Image Available
            {/if}
        </td>
        <td>
            <span class="itemname"><strong>{$item->products_name}</strong></span>
            <div class="itembody">
                {*$item->product->body|strip_tags|truncate:50:"..."*}
                {$item->getCartSummary()}
            </div>
            {if $options|@count > 0 || $item->getUserInputFields()!= ''}
                <div class="options">
                    <a href="#" class="infoicon">{gettext str="Additional Information"}</a>
                    <div class="exp-dropmenu">
                        {if $options|@count > 0}
                            <div class="hd" style="padding:0 5px">
                                <span class="type-icon"></span><h6>{gettext str="Selected Product Options"}</h6>
                            </div>
                            <div class="bd">
                                <ul>
                                    {foreach key=key from=$options item=option}
                                    {eDebug var=$key} 
                                    {eDebug var=$option}
                                        <li>{$option[1]} ({$option[3]}${$option[4]})</li>
                                    {/foreach}
                                </ul> 
                            </div>
                        {/if}
                        
                        {if $item->getUserInputFields() != ''}
                            <div class="hd" style="padding:0 5px">
                                {if $options|@count <= 0}<span class="type-icon"></span>{/if}<h6>{gettext str="Additional Information"}</h6>
                            </div>
                            <div class="bd">
                                {$item->getUserInputFields()}
                            </div>
                        {/if}
                    </div>
                </div>        
                {script unique="z-index" }
                {literal}
                
                YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
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
