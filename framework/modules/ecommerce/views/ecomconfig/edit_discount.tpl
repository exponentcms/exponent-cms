{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
    <h1>{'Manage Discounts'|gettext}</h1>
    <div id="mainform">
        {form action=update_discount}
            {control type="hidden" name="id" value=$discount->id}
            <div id="discounttabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                    <li><a href="#tab2"><em>{"Usage"|gettext}</em></a></li>
                    <li><a href="#tab3"><em>{"Conditions"|gettext}</em></a></li>
                    <li><a href="#tab4"><em>{"Actions"|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        <h2>{"General Configuration"|gettext}</h2>
                        {control type="text" name="title" label="Name"|gettext value=$discount->title}
                        {control type="text" name="coupon_code" label="Coupon Code"|gettext value=$discount->coupon_code}
                        {control type="editor" name="body" label="Description"|gettext height=250 value=$discount->body}
                        {*control type="text" name="priority" label="Priority"|gettext value=$discount->priority*}
                    </div>
                     <div id="tab2">
                        <h2>{"Usage Rules"|gettext}</h2>
                        {* control type="text" name="uses_per_coupon" label="Uses Per Coupon"|gettext value=$discount->uses_per_coupon}
                        {control type="text" name="uses_per_user" label="Uses Per Customer"|gettext value=$discount->uses_per_user *}
                        {control type="checkbox" name="never_expires" label="Offer Never Expires"|gettext value=1 checked=$discount->never_expires}
                        {control type="datetimecontrol" name="startdate" label="Valid From"|gettext value=$discount->startdate showtime=false}
                        {control type="datetimecontrol" name="startdate_time" label=" " value=$discount->startdate_time showdate=false}
                        {control type="datetimecontrol" name="enddate" label="Valid To"|gettext value=$discount->enddate showtime=false}
                        {control type="datetimecontrol" name="enddate_time" label=" " value=$discount->enddate_time showdate=false}
                        {* control type="checkbox" name="allow_other_coupons" label="All Use of Other Coupons"|gettext value=$discount->allow_other_coupons *}
                        {* control type="radiogroup?" name="apply_before_after_tax" label="All Use of Other Coupons"|gettext value=$discount->allow_other_coupons *}
                        {'If the discount is related to free or discounted shipping, or you simply want to force the shipping method used when this discount is applied, you may force the shipping method used here:'|gettext}
                        {control type="dropdown" name="required_shipping_calculator_id" id="required_shipping_calculator_id" label="Required Shipping Service" includeblank="-- Select a shipping service --"|gettext items=$shipping_services value=$discount->required_shipping_calculator_id onchange="switchMethods();"}
                        {foreach from=$shipping_methods key=calcid item=methods name=sm}
                            <div id="dd-{$calcid}" class="hide methods">
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
                        {control type="dropdown" name="action_type" label="Discount Action"|gettext items=$discount->actions default=$discount->action_type}
                        {'If you selected \'Percentage off entire cart\', enter the percentage discount you would like applied with this coupon code here.'|gettext}
                        {control type="text" name="discount_percent" label="Discount Percent"|gettext filter=percent value=$discount->discount_percent}
                        {'If you selected \'Fixed amount off entire cart\', enter dollar amount discount you would like applied with this coupon code here.'|gettext}
                        {control type="text" name="discount_amount" label="Discount Amount"|gettext filter=money value=$discount->discount_amount}
                        {'If you selected \'Fixed amount off shipping\', enter dollar amount you would like discounted off the shipping.'|gettext}
                        {control type="text" name="shipping_discount_amount" label="Shipping Discount Amount"|gettext filter=money value=$discount->shipping_discount_amount}
                    </div>
                </div>
            </div>
            <div class="loadingdiv">{'Loading'|gettext}</div>
            {control type=buttongroup submit="Save Discount"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="discountedit" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-element', function(Y) {
    var YAHOO=Y.YUI2;

    function switchMethods() {
        var dd = YAHOO.util.Dom.get('required_shipping_calculator_id');
        var methdd = YAHOO.util.Dom.get('dd-'+dd.value);
        var otherdds = YAHOO.util.Dom.getElementsByClassName('methods', 'div');
        
        for(i=0; i<otherdds.length; i++) {
            if (otherdds[i].id == 'dd-'+dd.value) {
                YAHOO.util.Dom.setStyle(otherdds[i].id, 'display', 'block');
            } else {
                YAHOO.util.Dom.setStyle(otherdds[i].id, 'display', 'none');
            }
        }
        YAHOO.util.Dom.setStyle(methdd, 'display', 'block');
        //Y.log(methdd);
        //Y.log(dd.value);
    }
    YAHOO.util.Event.onDOMReady(switchMethods);
});
{/literal}
{/script}

{script unique="discounttabs" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#discounttabs'});
		tabview.render();
		Y.one('#discounttabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
