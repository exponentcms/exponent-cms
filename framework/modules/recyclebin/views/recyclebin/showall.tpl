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

{css unique="recyclebin" corecss="admin-global" link="`$asset_path`css/recyclebin.css"}

{/css}

<div class="recyclebin orphan-content">
    <h1 class="main">{'Recycle Bin'|gettext}</h1>
    {$oldmod = ''}
    {foreach from=$items item=item}
        {if $item->module != $oldmod}
            <h2 class="main">{$item->module|getcontrollername|capitalize} {'modules'|gettext}</h2>
        {/if}
        <div class="rb-item">
            <div class="recycledcontent">
                {*{icon action=delete id=$item->id mod=$item->module src=$item->source text='Remove this'|gettext|cat:' '|cat:$item->module|getcontrollername|capitalize|cat:' '|cat:'Module from Recycle Bin'|gettext onclick="return confirm('Are you sure you want to delete this recyclebin item?');window.close();"}*}
                {icon class=delete action=remove mod=$item->module src=$item->source text='Remove this'|gettext|cat:' '|cat:$item->module|getcontrollername|capitalize|cat:' '|cat:'Module from Recycle Bin'|gettext onclick="return confirm('Are you sure you want to permanently delete this module and all it\'s items from the recyclebin?');window.close();"}
                {*{icon controller=$item->module action=delete_instance src=$item->source text='Remove this'|gettext|cat:' '|cat:$item->module|getcontrollername|capitalize|cat:' '|cat:'Module from Recycle Bin'|gettext onclick="return confirm('Are you sure you want to delete this recyclebin item?');window.close();"}*}
                {$item->html}
            </div>
        </div>
        {$oldmod = $item->module}
    {foreachelse}
        <div class="rb-item">
            {'There\'s nothing in the Recycle Bin'|gettext}.
        </div>
    {/foreach}
</div>
