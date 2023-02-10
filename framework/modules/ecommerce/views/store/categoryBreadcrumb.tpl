{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

<div class="category-breadcrumb col-sm-12">
    {if empty(ecomconfig::getConfig('store_home'))}
        <a href="{link controller=store action=showall}" title="{'View the Store'|gettext}">{'Store'|gettext}</a>{if count($ancestors)}&#160;&#160;&raquo;&#160;{/if}
    {else}
        <a href="{$home}" title="{'View the Store'|gettext}">{'Store'|gettext}</a>{if count($ancestors)}&#160;&#160;&raquo;&#160;{/if}
    {/if}
    {foreach from=$ancestors item=ancestor name=path}
        {if !$smarty.foreach.path.last}
            <a href="{link controller=store action=showall title=$ancestor->sef_url}" title="{'View this Product Category'|gettext}">{$ancestor->title}</a>&#160;&#160;&raquo;&#160;
        {elseif empty($show_product)}
            {$ancestor->title}
        {else}
            <a href="{link controller=store action=showall title=$ancestor->sef_url}" title="{'View this Product Category'|gettext}">{$ancestor->title}</a>&#160;&#160;&raquo;&#160;
            {$show_product}
        {/if}
    {/foreach}
</div>
