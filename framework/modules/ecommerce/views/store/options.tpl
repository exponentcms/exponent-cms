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

{if $product->hasOptions()}
    <div class="product-options">
        {control type="hidden" name="options_shown" value=$product->id}
        {group label='Options'|gettext}
            {if !$product->segregate_options}
                {foreach from=$product->optiongroup item=og}
                    {if $og->hasEnabledOptions()}
                        <div class="option {cycle values="odd,even"}">
                            {optiondisplayer product=$product options=$og->title view=$og->allow_multiple display_price_as=diff selected=$params.options required=$og->required}
                        </div>
                    {/if}
                {/foreach}
                <span style="font-variant:small-caps;"><span class="required" title="{'This entry is required'|gettext}">*&#160;</span>{'Selection required'|gettext}</span>
            {else}
                {$optional_opts = 1}
                {if $product->hasRequiredOptions()}
                    <h4>{'Standard Feature Options'|gettext}</h4>
                    {$optional_opts = 0}
                    {foreach from=$product->optiongroup item=og}
                        {if $og->hasEnabledOptions()}
                            {if $og->required}
                                <div class="option {cycle values="odd,even"}">
                                    {optiondisplayer product=$product options=$og->title view=$og->allow_multiple display_price_as=diff selected=$params.options required=true}
                                </div>
                            {else}
                                {$optional_opts = $optional_opts + 1}
                            {/if}
                        {/if}
                    {/foreach}
                    <span style="font-variant:small-caps;"><span class="required" title="{'This entry is required'|gettext}">*&#160;</span>{'Selection required'|gettext}.</span>
                {/if}
                {if $optional_opts}
                    <h4>{'Optional Features'|gettext}</h4>
                    {foreach from=$product->optiongroup item=og}
                        {if $og->hasEnabledOptions()}
                            {if !$og->required}
                                <div class="option {cycle values="odd,even"}">
                                    {optiondisplayer product=$product options=$og->title view=$og->allow_multiple display_price_as=diff selected=$params.options}
                                </div>
                            {/if}
                        {/if}
                    {/foreach}
                {/if}
            {/if}
        {/group}
    </div>
{/if}