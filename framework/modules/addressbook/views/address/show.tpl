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

<address class="address show">
	{$address->firstname} {$address->middlename} {$address->lastname}{br}
	{$address->address1}
	{if $address->address2 != ""}, {$address->address2}{br}{else}{br}{/if}
	{$address->city}. {$address->state|statename} {$address->zip}{br}
	{$address->phone}
</address>
