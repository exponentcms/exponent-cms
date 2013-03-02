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


{if $config.title}<h3>{$config.title}</h3>{/if}
<ul class="filelist">
    {foreach from=$files item=file}
        {if $file->alt != ""}
            {$alt = $file->alt}
        {elseif $file->title!=""}
            {$alt = $file->title}
        {else}
            {$alt = $file->filename}
        {/if}
        {if (($file->title != "") && !$config.usefilename)}
            {$title = $file->title}
        {else}
            {$title = $file->filename}
        {/if}
        <li>
            <a class="downloadfile" href="{link action="downloadfile" id=$file->id}" title="{$alt}">{$title}</a>
        </li>
    {/foreach}
</ul>
