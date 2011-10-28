{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Phillip Ball
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
<div class="navigationmodule children-and-grandchildren">
	<ul>
	{foreach from=$sections item=section}
	{if $section->parent==$current->id}
		{if $section->active == 1}
		<li class="expandablenav"><img id="{$section->name|replace:' ':''}" class="twisty" src="{$smarty.const.THEME_RELATIVE|cat:'images/expand.gif'}"><a class="childlink" title="{$section->name}" href="{$section->link}"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>
			{getnav type="children" of=$section->id assign=grandchildren}
			{foreach key=skey name=grandchildren from=$grandchildren item=grandchild}
			{if $smarty.foreach.grandchildren.first}<ul id="{$section->name|replace:' ':''}gc" class="grandchildren">{/if}
				<li><a href="{$grandchild->link}" class="navlink"{if $grandchild->new_window} target="_blank"{/if} title="{$grandchild->name}">{$grandchild->name}</a>
			{if $smarty.foreach.grandchildren.last}</ul>{/if}
			{/foreach}		
		</li>
		{/if}
	{/if}
	{/foreach}
	</ul>
</div>
