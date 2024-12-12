{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

<div id="freeshippingcfg">
    <div id="freeship-tabs" class="">
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-bs-toggle="tab"><em>{'General Settings'|gettext}</em></a></li>
        </ul>
        <div class="tab-content">
            <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                <blockquote>{'Offering Free Shipping means every order ships for Free'|gettext}</blockquote>
                {control type="text" name="shipping_service_name" label="Default Name for Shipping Service"|gettext value=$calculator->configdata.shipping_service_name|default:'Free'|gettext}
                {control type="text" name="free_shipping_method_default_name" label="Default Name for Shipping Method"|gettext value=$calculator->configdata.free_shipping_method_default_name}
            </div>
        </div>
    </div>
    {loading}
</div>
