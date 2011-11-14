{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module shipping configure">
    <h1>Configure {$calculator->title} Speed</h1>
    <p>Use this form to configure the {$calculator->title} speed</p>
    
    {form action=saveEditSpeed}
  		{control type="hidden" name="shippingcalculator_id" value=$calculator->id}
        {include file=$calculator->editspeed()}
        {control type="buttongroup" submit="Save"|gettext cancel="Cancel"|gettext}
    {/form}
</div>