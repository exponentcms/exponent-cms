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

<div class="navigationmodule yui-top-nav exp-yui-nav">
	<div id="yuimenubar" class="yuimenubar yuimenubarnav">
		<div class="bd">
			<ul class="first-of-type">
                {foreach name="children" key=key from=$sections item=section}
                    {if $section->depth==0}
                        <li class="yuimenubaritem">
                            <a class="yuimenubaritemlabel" href="{link section=$section->id}">{$section->name|replace:"&":"&amp;"}</a>
                        </li>
                    {/if}
                {/foreach}
			</ul>
		</div>
	</div>
    {yuimenubar buildon="yuimenubar"}
</div>
