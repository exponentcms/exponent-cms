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

<div id="instore">
    <div id="instore-tabs" class="">
        <ul class="nav nav-tabs">
	        <li class="active"><a href="#tab1" data-toggle="tab"><em>{'General'|gettext}</em></a></li>
        </ul>            
        <div class="tab-content">
	        <div id="tab1" class="tab-pane fade in active">
                {control type="text" name="rate" label="Per Weight Shipping Rate"|gettext size=5 filter=money value=$calculator->configdata.rate description='The shipping cost will be multiplied by the overall weight of an order that uses this shipping method'|gettext}
               {control type="text" name="handling" label="Handling Charge"|gettext size=5 filter=money value=$calculator->configdata.handling description='Charge added to each shipment regardless of weight'|gettext}
	        </div>
        </div>
    </div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}