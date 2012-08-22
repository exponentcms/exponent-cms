{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="breadcrumb" link="`$asset_path`css/breadcrumb.css"}

{/css}

{assign var=i value=0}

<div class="module navigation breadcrumb">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {foreach from=$sections item=section}
        {if $current->numParents >= $i && ($current->id == $section->id || $current->parents[$i] == $section->id)}
            {$i=$i+1}
            {if $section->active == 1}
                {if $section->id == $current->id}
                    <a class="current"
                {else}
                    <a class="trail"
                {/if}
                    href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>&#160;
            {else}
                <span>{$section->name}</span>&#160;
            {/if}
            {if $section->id != $current->id}&raquo;&#160;{/if}
        {/if}
    {/foreach}
</div>
