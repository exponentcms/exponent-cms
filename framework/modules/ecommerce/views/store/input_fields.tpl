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

{if !empty($product->user_input_fields) && $product->user_input_fields|@count>0 }
    <div class="user-input-fields">
        {control type="hidden" name="input_shown" value=$product->id}
        <h2>{'Additional Information for'|gettext} {$product->title}</h2>
        <blockquote>{'This item would like the following additional information. Items marked with an * are required:'|gettext}</blockquote>
        {foreach from=$product->user_input_fields key=uifkey item=uif}
            <div class="user-input {cycle values="odd,even"}">
                {if $uif.use}
                    {control type=text name="user_input_fields[`$uifkey`]" size=50 maxlength=$uif.max_length label=$uif.name|cat:':' required=$uif.is_required value=$params.user_input_fields.$uifkey}
                    {if $uif.description != ''}{$uif.description}{/if}
                {/if}
            </div>
        {/foreach}
    </div>
{/if}