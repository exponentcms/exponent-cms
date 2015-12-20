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

{*{css unique="breadcrumb" link="`$asset_path`css/breadcrumb.css"}*}

{*{/css}*}

{$i=0}

<div class="module navigation navigation-breadcrumb">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<ul class="breadcrumb">
    {foreach from=$sections item=section}
        {if $current->numParents >= $i && ($current->id == $section->id || $current->parents[$i] == $section->id)}
            {$i=$i+1}
            {if $section->active == 1}
                {if $section->id == $current->id}
                    <li class="active">{$section->name}</li>
                {else}
                    <li><a href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a></li>
                {/if}
            {else}
                <span>{$section->name}</span>
            {/if}
            {*{if $section->id != $current->id}&raquo;&#160;{/if}*}
        {/if}
    {/foreach}
	</ul>
</div>
