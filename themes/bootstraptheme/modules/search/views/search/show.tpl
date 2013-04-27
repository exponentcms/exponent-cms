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
 
{css unique="searchform" corecss="forms" link="`$asset_path`css/show-form.css"}
    
{/css}
 
<div class="module search show-form">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <form id="form" name="form" method="POST" action="{$smarty.const.PATH_RELATIVE}index.php">
        <input type="hidden" name="action" id="action" value="search">
        <input type="hidden" name="module" id="module" value="search">
        {*{control type="search" name="search_string" id="search_string" placeholder=$config.inputtext|default:"Keywords"|gettext prepend="search"}*}
        {*{control type="buttongroup" submit=$config.buttontext|default:"Search"|gettext}*}
        {*<div class="input-prepend input-append">*}
            {*<span class="add-on"><i class="icon-search"></i></span>*}
            {*<input type="search" name="search_string" id="search_string" type="text" placeholder="{$config.inputtext|default:"Keywords"|gettext}">*}
            {*<button type="submit" class="btn">{$config.buttontext|default:"Search"|gettext}</button>*}
        {*</div>*}
        <div class="input-append">
            <input type="search" name="search_string" id="search_string" type="text" placeholder="{$config.inputtext|default:"Keywords"|gettext}">
            <button type="submit" class="btn"><i class="icon-search"></i></button>
        </div>
    </form>
</div>
