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

{control type="hidden" name="tab_loaded[shipping]" value=1}
{control type="checkbox" id="no_shipping" name="shipping[no_shipping]" label='This item doesn\'t require shipping'|gettext value=1 checked=$record->no_shipping postfalse=1}
<span id="shipping_needed">
{control type="dropdown" name="shipping[required_shipping_calculator_id]" id="required_shipping_calculator_id" label="Required Shipping Service"|gettext includeblank="-- No specific service --"|gettext items=$shipping_services value=$record->required_shipping_calculator_id}
{foreach from=$shipping_methods key=calcid item=methods name=sm}
    <div id="dd-{$calcid}" class="methods" style="display:none;">
        {control type="dropdown" name="required_shipping_methods[`$calcid`]" label="Shipping Methods"|gettext items=$methods value=$record->required_shipping_method includeblank='-- No specific method --'|gettext}
    </div>
{/foreach}
{icon controller="shipping" action="manage" text="Manage Shipping Options"|gettext}
{control type="text" name="shipping[weight]" label="Item Weight (in pounds)"|gettext size=4 filter=decimal value=$record->weight}
{control type="text" name="shipping[width]" label="Width (in inches)"|gettext size=4 filter=decimal value=$record->width}
{control type="text" name="shipping[height]" label="Height (in inches)"|gettext size=4 filter=decimal value=$record->height}
{control type="text" name="shipping[length]" label="Length (in inches)"|gettext size=4 filter=decimal value=$record->length}
{control type="text" name="shipping[surcharge]" label="Freight Surcharge"|gettext size=4 filter=decimal value=$record->surcharge description='per item'|gettext}
</span>

{script unique="prodedit" yui3mods="node"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var switchMethods = function () {
        var dd = Y.one('#required_shipping_calculator_id');
        var ddval = dd.get('value');
        if (ddval != '') {
            var methdd = Y.one('#dd-'+ddval);
        }
        var otherdds = Y.all('.methods');

        otherdds.each(function (odds) {
            if (odds.get('id') == 'dd-'+ddval) {
                odds.setStyle('display', 'block');
            } else {
                odds.setStyle('display', 'none');
            }
        });
    }
    switchMethods();
    Y.one('#required_shipping_calculator_id').on('change', switchMethods);
});
{/literal}
{/script}

{script unique="editshipping2" jquery=1}
{literal}
$('#no_shipping').change(function() {
    if ($('#no_shipping').is(':checked') == true)
        $("#shipping_needed").hide("slow");
    else {
        $("#shipping_needed").show("slow");
    }
});
if ($('#no_shipping').is(':checked') == true)
    $("#shipping_needed").hide("slow");
{/literal}
{/script}
