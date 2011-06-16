{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
 
<div class="extra-data">
    <h3>The following people were registered for this event</h3>
    <ul>
    {foreach from=$extra_data item=person}    
        <li>                
            {$person.name}, {$person.phone}, {$person.email}        
        </li>
    {/foreach}
    </ul>
</div>
