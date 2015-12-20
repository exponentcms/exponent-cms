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

 <div id="discountconfig" class="module discountconfig configure">
    <h1>{'Edit Discount'|gettext}</h1>
    <div id="mainform">
        {form action=update_discount}
            {control type="hidden" name="id" value=$discount->id}
            <div id="discounttabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                    <li><a href="#tab2"><em>{"Usage Rules"|gettext}</em></a></li>
                    <li><a href="#tab3"><em>{"Conditions"|gettext}</em></a></li>
                    <li><a href="#tab4"><em>{"Actions"|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        <h2>{"General Configuration"|gettext}</h2>
                        {control type="text" name="title" label="Name"|gettext value=$discount->title focus=1}
                        {control type="text" name="coupon_code" label="Coupon Code"|gettext value=$discount->coupon_code}
                        {control type="editor" name="body" label="Description"|gettext height=250 value=$discount->body}
                        {*control type="text" name="priority" label="Priority"|gettext value=$discount->priority*}
                    </div>
                     <div id="tab2">
                        <h2>{"Usage Rules"|gettext}</h2>
                        {* control type="text" name="uses_per_coupon" label="Uses Per Coupon"|gettext value=$discount->uses_per_coupon}
                        {control type="text" name="uses_per_user" label="Uses Per Customer"|gettext value=$discount->uses_per_user *}
                        {control type="checkbox" name="never_expires" id="never_expires" label="Offer Never Expires"|gettext value=1 checked=$discount->never_expires}
                         <div class="validity">
                            {control type="datetimecontrol" name="startdate" label="Valid From"|gettext value=$discount->startdate showtime=false}
                            {control type="datetimecontrol" name="startdate_time" label=" " value=$discount->startdate_time showdate=false}
                            {control type="datetimecontrol" name="enddate" label="Valid To"|gettext value=$discount->enddate showtime=false}
                            {control type="datetimecontrol" name="enddate_time" label=" " value=$discount->enddate_time showdate=false}
                         </div>
                        {* control type="checkbox" name="allow_other_coupons" label="All Use of Other Coupons"|gettext value=$discount->allow_other_coupons *}
                        {* control type="radiogroup?" name="apply_before_after_tax" label="All Use of Other Coupons"|gettext value=$discount->apply_before_after_tax *}
                        {'If the discount is related to free or discounted shipping, or if you simply want to force the shipping method used when this discount is applied, you may force the shipping method used here:'|gettext}
                        {control type="dropdown" name="required_shipping_calculator_id" id="required_shipping_calculator_id" label="Required Shipping Service" includeblank="-- Select a shipping service --"|gettext items=$shipping_services value=$discount->required_shipping_calculator_id}
                        {foreach from=$shipping_methods key=calcid item=methods name=sm}
                            <div id="dd-{$calcid}" class="methods" style="display:none;">
                                {control type="dropdown" name="required_shipping_methods[`$calcid`]" label="Required Shipping Method" items=$methods value=$discount->required_shippng_method}
                            </div>
                        {/foreach}
                    </div>
                    <div id="tab3">
                        <h2>{"Conditions"|gettext}</h2>
                        {* control type="dropdown" name="group_ids[]" label="Groups"|gettext items=$groups default=$selected_groups multiple=true size=10 *}
                        {control type="text" name="minimum_order_amount" label="Minimum Order Amount"|gettext filter=money value=$discount->minimum_order_amount}
                    </div>
                    <div id="tab4">
                        <h2>{"Actions and Amounts"|gettext}</h2>
                        {control type="dropdown" name="action_type" id="action_type" label="Discount Action"|gettext items=$discount->actions default=$discount->action_type}
                        <div id="aa-3" class="actions">
                            {control type="text" name="discount_percent" label="Discount Percent"|gettext filter=percent value=$discount->discount_percent description='Enter the percentage would like discounted off the total order.'|gettext}
                        </div>
                        <div id="aa-4" class="actions">
                            {control type="text" name="discount_amount" label="Discount Amount"|gettext filter=money value=$discount->discount_amount description='Enter dollar amount would like discounted off the total order.'|gettext}
                        </div>
                        <div id="aa-5" class="actions">
                            <label>{'Free shipping will be used for the order.'|gettext}</label>
                        </div>
                        <div id="aa-6" class="actions">
                            {control type="text" name="shipping_discount_amount" label="Shipping Discount Amount"|gettext filter=money value=$discount->shipping_discount_amount description='Enter dollar amount you would like discounted off the shipping cost.'|gettext}
                        </div>
                    </div>
                </div>
            </div>
            {*<div class="loadingdiv">{'Loading'|gettext}</div>*}
            {loading}
            {control type=buttongroup submit="Save Discount"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="discountedit" yui3mods="node"}
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

{script unique="discountedit2" yui3mods="node"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        var switchMethods2 = function () {
            var dd = Y.one('#action_type');
            var ddval = dd.get('value');
            if (ddval != '') {
                var methdd = Y.one('#aa-'+ddval);
            }
            var otherdds = Y.all('.actions');

            otherdds.each(function (odds) {
                if (odds.get('id') == 'aa-'+ddval) {
                    odds.setStyle('display', 'block');
                } else {
                    odds.setStyle('display', 'none');
                }
            });
        }
        switchMethods2();
        Y.one('#action_type').on('change', switchMethods2);
    });
{/literal}
{/script}

{script unique="authtabs" yui3mods="get,exptabs,node-load,event-simulate"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	 YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#discounttabs'});
		Y.one('#discounttabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}

{script unique="tabload" jquery=1 bootstrap="transition"}
{literal}
    $(document).ready(function(){
        $("#never_expires").click(function(){
            if (this.checked) {
                $("#validity").hide("slow");
            } else {
                $("#validity").show("slow");
            }
        });
        if ($("#never_expires").checked) $("#validity").hide();
    });
{/literal}
{/script}
