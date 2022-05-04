{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-toggle="tab"><em>{'General Settings'|gettext}</em></a></li>
        </ul>
        <div class="tab-content">
	        <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                {control type="text" name="rate" label="Per Weight Shipping Rate"|gettext size=5 filter=money value=$calculator->configdata.rate description='The shipping cost will be multiplied by the overall weight of an order that uses this shipping method'|gettext}
                {control type="text" name="handling" label="Handling Charge"|gettext size=5 filter=money value=$calculator->configdata.handling|default:0 description='Charge added to each shipment regardless of weight'|gettext}
                {control type="text" name="shipping_service_name" label="Default Name for Shipping Service"|gettext value=$calculator->configdata.shipping_service_name|default:'Per Weight'|gettext}
                {control type="text" name="shipping_method_name" label="Default Name for Shipping Method"|gettext value=$calculator->configdata.shipping_method_name|default:'Per Weight'|gettext}
	        </div>
        </div>
    </div>
    {loading}
</div>
