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

<div class="module navigation children-and-grandchildren">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
	<ul>
        {foreach from=$sections item=section}
            {if $section->parent==$current->id}
                <li class="expandablenav"><img id="{$section->name|replace:' ':''}" class="twisty" src="{$smarty.const.PATH_RELATIVE}framework/modules/navigation/assets/images/expand.gif"><a class="childlink" title="{$section->name}" href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
                    {getnav type="children" of=$section->id assign=grandchildren}
                    {foreach key=skey name=grandchildren from=$grandchildren item=grandchild}
                        {if $smarty.foreach.grandchildren.first}<ul id="{$section->name|replace:' ':''}gc" class="grandchildren">{/if}
                            {if $grandchild->active == 1}
                                <li><a href="{$grandchild->link}" class="navlink"{if $grandchild->new_window} target="_blank"{/if} title="{$grandchild->name}">{$grandchild->name}</a></li>
                            {else}
                                <li><span class="navlink">{$grandchild->name}</span></li>
                            {/if}
                        {if $smarty.foreach.grandchildren.last}</ul>{/if}
                    {/foreach}
                </li>
            {/if}
        {/foreach}
	</ul>
</div>
