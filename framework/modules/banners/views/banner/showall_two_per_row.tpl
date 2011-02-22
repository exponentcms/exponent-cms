{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module banner two-per-row">
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        <div class="module-actions">
            {if $permissions.manage == 1}
            {icon action=manage id=$item->id title="manage `$modelname`" text="Manage Banners"}
            {/if}
        </div>
    {/permissions}
    {foreach from=$banners item=banner}
    <div class="banneritem">
        <a href="{link action=click id=$banner->id}" target="_blank"><img src="{$banner->expFile[0]->url}"></a>
    </div>
    {/foreach}
    {clear}
</div>
