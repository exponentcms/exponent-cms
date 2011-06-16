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
<div class="module ealerts confirmation-email">
	<p>You requested to subscribe to the following E-Alert topics</p>
	
	<ul class="new-collections">
        {foreach from=$ealerts item=ealert}
            <li>{$ealert->ealert_title}</li>
        {/foreach}
    </ul>  
	<p>
	    <a href="{link controller=ealert action=confirm id=$subscriber->id key=$subscriber->hash}">Click here to confirm your subscription.</a>
	</p>
		
	<p>>If you did not request this email, you can safely ignore it.</p>
</div>
