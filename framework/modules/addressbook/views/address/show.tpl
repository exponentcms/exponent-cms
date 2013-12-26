{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<address class="address show">
	<span class="fullname">
        {$address->firstname} {$address->middlename} {$address->lastname}
	</span>
	{if $address->organization}<span class="company">{$address->organization}</span>{/if}
	<span class="address1">{$address->address1}</span>
	{if $address->address2 != ""}<span class="address2">{$address->address2}</span>{/if}
	<span class="citystatzip">
        {$address->city},
        {if $address->state == -2}
            {$address->non_us_state}
        {else}
            {$address->state|statename}
        {/if}
        {$address->zip}{br}
        {if $address->state == -2}
            {$address->country|countryname}
        {/if}
    </span>
	{if $address->address_type}<span class="address_type">({$address->address_type})</span>{/if}
	<span class="phone">{$address->phone}</span>
	<span class="email">{$address->email}</span>
</address>
