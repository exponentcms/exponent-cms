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

{css unique="top-nav" link="`$asset_path`css/topnav.css"}

{/css}

<div class="module navigation top-nav">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<ul>
        {$isparent=0}
        {foreach from=$sections item=section}
            {if $section->parent == 0}
                {if $current->parents[0]!=""}
                    {foreach from=$current->parents item=parent}
                        {if $parent==$section->id}
                            {$isparent=1}
                        {/if}
                    {/foreach}
                {/if}
                {if $section->active == 1}
                    <li class="{if $section->id==$current->id || $isparent==1}current{/if}{if $section->last==1} last{/if}{if $section->first==1} first{/if}"><a class="navlink" href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a></li>
                {else}
                    <li><span class="navlink">{$section->name}</span></li>
                {/if}
            {/if}
            {$isparent=0}
        {/foreach}
	</ul>
</div>
