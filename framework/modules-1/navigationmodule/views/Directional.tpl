{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

 <div class="navigationmodule directional">
    {math equation="x-1" x=$current->rank assign="prevrank"}
    {if $prevrank < 0}
    	&lt; {'Prev Page'|gettext}
    {else}
    	{foreach from=$sections item=section}
            {if $section->parent ==$current->parent && $section->rank==$prevrank}
                <a href="{$section->link}"{if $section->new_window} target="_blank"{/if}>&lt; {'Prev Page'|gettext}</a>
            {/if}
    	{/foreach}
    {/if}

    &nbsp;|&nbsp;

    {*if $current->parent == 0}
    	{'Up'|gettext}
    {else}
    	<a href="?section={$current->parent}">{'Up'|gettext}</a>
    	&nbsp;|&nbsp;
    	<a href="?section={$current->parents[0]}">{'Top'|gettext}</a>
    {/if*}

    &nbsp;|&nbsp;

    {math equation="x+1" x=$current->rank assign="nextrank"}
    {assign var=gotlink value=0}
    {foreach from=$sections item=section }
        {if $section->parent == $current->parent && $section->rank == $nextrank}
            <a href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{'Next Page'|gettext} &gt;</a>
            {assign var=gotlink value=1}
        {/if}
    {/foreach}
    {if $gotlink == 0}
        {'Next Page'|gettext} &gt;
    {/if}
</div>