{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="searchform" link="`$asset_path`css/show-form.css"}

{/css}

<div class="module search show-form">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {form action=search}
        {*{control type="search" name="search_string" id="search_string" placeholder=$config.inputtext|default:"Keywords"|gettext prepend="search"}*}
        {*{control type="buttongroup" submit=$config.buttontext|default:"Search"|gettext}*}
        {*<div class="input-prepend input-append">*}
            {*<span class="add-on"><i class="icon-search"></i></span>*}
            {*<input type="search" name="search_string" id="search_string" type="text" placeholder="{$config.inputtext|default:"Keywords"|gettext}">*}
            {*<button type="submit" class="btn">{$config.buttontext|default:"Search"|gettext}</button>*}
        {*</div>*}
        <div class="input-append">
            <input type="search" name="search_string" id="search_string" aria-label="{'search string'|gettext}" placeholder="{$config.inputtext|default:"Keywords"|gettext}">
            <button type="submit" class="btn"><i class="icon-search"></i></button>
        </div>
    {/form}
</div>
