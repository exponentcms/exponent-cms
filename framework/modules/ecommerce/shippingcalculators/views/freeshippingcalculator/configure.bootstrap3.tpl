{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
	        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{'General Settings'|gettext}</em></a></li>
        </ul>            
        <div class="tab-content">
            <div id="tab1" role="tabpanel" class="tab-pane fade in active">
                <blockquote>{'Offering Free Shipping means every order ships for Free'|gettext}</blockquote>
                {control type="text" name="shipping_service_name" label="Default Name for Shipping Service"|gettext value=$calculator->configdata.shipping_service_name|default:'Free'|gettext}
                {control type="text" name="free_shipping_method_default_name" label="Default Name for Shipping Method"|gettext value=$calculator->configdata.free_shipping_method_default_name}
            </div>
        </div>
    </div>
	{*<div class="loadingdiv">{'Loading'|gettext}</div>*}
    {loading}
</div>

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}