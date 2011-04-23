{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Phillip Ball and  Adam Kessler
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
 *{math equation="x*20" x=$section->depth}
 *}
<div class="navigationmodule yui-side-nav exp-yui-nav">
	<div id="sidenavjavascript" class="yuimenu">
		<div class="bd">
			<ul class="first-of-type">
			{foreach name="children" key=key from=$sections item=section}
				{if $section->active == 1 && $section->depth ==0}
    				<li class="yuimenuitem{if $section->id == $current->id} current{/if}">
        				<a class="yuimenuitemlabel" href="{$section->link}" {if $section->new_window} target="_blank"{/if}>{$section->name}</a>
					</li>
				{elseif $section->active == 0 && $section->depth ==0}
				<li class="yuimenuitem">
					<span class="yuimenuitemlabel">{$section->name|replace:"&":"&amp;"}</span>
				</li>
				{/if}
			{/foreach}
			</ul>
		</div>
	</div>
{yuimenu buildon="sidenavjavascript"}
</div>
