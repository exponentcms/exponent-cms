{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

{clear}
<div class="module motd show">
    <h1>{$moduletitle|default:"Message of the Day"}</h1>
    <div class="motd-message">
        <div class="motd-date">
            <span class="date-header">{$smarty.now|expdate:"M, y"}</span>
            <span class="date-day">
                <!--span class="day-name">{$smarty.now|expdate:"D"}{br}</span-->
                {$smarty.now|expdate:"j"}
            </span>
            {clear}
        </div>
        <div class="bodycopy">
            {$message->body}
        </div>
        {clear}
        <a class="link" href="{link action=showall}">View Previous Tips</a>
    
        {permissions}
			<div class="module-actions">
				{if $permissions.edit == 1}
					{icon class="add" action=create text="Add a tip"}
			  {/if}
			 </div>
        {/permissions}    
    </div>
</div>
