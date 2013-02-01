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

{css unique="add-section" link="`$asset_path`css/add-section.css"}

{/css}

<div class="module navigation add-section">
    <div class="form_header">
        <h1>{'Add New Page to Site Navigation'|gettext}</h1>
        <div class="desc">
            {if $parent->id == 0}{'You are adding a new top-level page.'|gettext}{else}{'You are adding a new sub page to "%s".'|gettext|sprintf:$parent->name}{/if}
            {'Please select the type of page you would like to add.'|gettext}
        </div>
    </div>
    <div class="head">
        <a class="contentpage" href="{link action=edit_contentpage parent=$parent->id}">{'Content Page'|gettext}</a>
    </div>
    <div class="desc">{'Content Pages are regular pages on the site that allow you to add modules to them.  With content pages, you are able to override the global Site Title, Site Description and Site Keywords settings.'|gettext}</div>

    <div class="head">
        <a class="externalpage" href="{link action=edit_externalalias parent=$parent->id}">{'External Website Link'|gettext}</a>
    </div>
    <div class="desc">{'If you need or want a link in your site hierarchy to link to some off-site webpage, create an External Link.'|gettext}</div>

    <div class="head">
        <a class="internalpage" href="{link action=edit_internalalias parent=$parent->id}">{'Page Alias'|gettext}</a>
    </div>
    <div class="desc">{'If you need or want a link to another page in your site hierarchy, use a page alias.'|gettext}</div>

    {if $parent->id == 0}
        <div class="head">
            <a class="freeformpage" href="{link action=edit_freeform parent=$parent->id}">{'Free-form Menu Item'|gettext}</a>
        </div>
        <div class="desc">{'If you want to embed a module/container as menu item, use a free-form menu item.'|gettext}{br}
        <strong>{'This page/menu-item type is only functional on some navigation views such as the Mega view where it will appear without its children!'|gettext}</strong></div>
    {/if}

    {if $haveStandalone != 0 && $isAdministrator}
        <div class="head">
            <a class="standalone" href="{link action=move_standalone parent=$parent->id}">{'Move Standalone Page'|gettext}</a>
        </div>
        <div class="desc">{'Use this if you want to move a standalone page into the navigation hierarchy.'|gettext}</div>
    {/if}
</div>
