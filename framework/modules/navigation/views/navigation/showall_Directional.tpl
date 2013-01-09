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

 <div class="module navigation directional">
     {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$prevrank=$current->rank-1}
    {if $prevrank < 0}
    	&lt; {'Prev Page'|gettext}
    {else}
    	{foreach from=$sections item=section}
            {if $section->parent ==$current->parent && $section->rank==$prevrank}
                <a href="{$section->link}"{if $section->new_window} target="_blank"{/if}>&lt; {'Prev Page'|gettext}</a>
            {/if}
    	{/foreach}
    {/if}

    &#160;|&#160;

    {*if $current->parent == 0}
    	{'Up'|gettext}
    {else}
    	<a href="?section={$current->parent}">{'Up'|gettext}</a>
    	&#160;|&#160;
    	<a href="?section={$current->parents[0]}">{'Top'|gettext}</a>
    {/if*}

    &#160;|&#160;

    {$nextrank=$current->rank+1}
    {$gotlink=0}
    {foreach from=$sections item=section }
        {if $section->parent == $current->parent && $section->rank == $nextrank}
            <a href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{'Next Page'|gettext} &gt;</a>
            {$gotlink=1}
        {/if}
    {/foreach}
    {if $gotlink == 0}
        {'Next Page'|gettext} &gt;
    {/if}
</div>
