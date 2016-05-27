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

<div id="storeconfig" class="module ecomconfig configure">
    <h1>{'Store Configuration'|gettext}</h1>
    <div id="mainform">
        {form action=saveconfig}
            <div id="storetabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                    <li><a href="#tab2"><em>{'Site'|gettext}</em></a></li>
                    <li><a href="#tab3"><em>{'Cart'|gettext}</em></a></li>
                    <li><a href="#tab4"><em>{'Display'|gettext}</em></a></li>
                    <li><a href="#tab5"><em>{'Invoices'|gettext}</em></a></li>
                    {*<li><a href="#tab6"><em>{'Notifications'|gettext}</em></a></li>*}
                    <li><a href="#tab7"><em>{'Location'|gettext}</em></a></li>
                    <li><a href="#tab8"><em>{'Product Feeds'|gettext}</em></a></li>
                    <li><a href="#tab9"><em>{'Gift Cards'|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        <h2>{'Store Information'|gettext}</h2>
                        {control type="text" name="storename" label="Store Name"|gettext value=$config.storename|default:'My Store'|gettext focus=1}
                        {control type="text" name="store[address1]" label="Address"|gettext value=$config.store.address1 required=1}
           	            {control type="text" name="store[address2]" label=" " value=$config.store.address2}
           	            {control type="text" name="store[city]" label="City"|gettext value=$config.store.city required=1}
                        {control type="countryregion" name="store[address]" label="Country/State"|gettext country_default=$config.store.country|default:223 region_default=$config.store.state includeblank="-- Choose a State --"|gettext required=1}
           	            {control type="text" name="store[postalCode]" label="Zip Code"|gettext size=10 value=$config.store.postalCode required=1}
                        {*{control type=tel name="store[phone]" label="Phone Number"|gettext value=$calculator->configdata.store.phone}*}
                    </div>
                    <div id="tab2">
                        <h2>{"Site Settings"|gettext}</h2>
                        {control type="html" name="ecomheader" label='Header'|gettext rows=6 cols=60 value=$config.ecomheader description='This will be displayed on the top of your emails and invoices.'|gettext}
                        {control type="html" name="ecomfooter" label='Footer'|gettext rows=6 cols=60 value=$config.ecomfooter description='This will be displayed on the bottom of your emails and invoices.'|gettext}
                    </div>
                    <div id="tab3">
                        <h2>{"Cart Settings"|gettext}</h2>
                        {control type="checkbox" name="show_cart" label="Adding an Item Displays Shopping Cart?"|gettext value=1 checked=$config.show_cart description='Move directly to the shopping cart after adding a new item?'|gettext}
                        {control type="text" name="min_order" label="Minimum order amount to require"|gettext value=$config.min_order filter=money description='Orders less than this amount will not be allowed to complete a checkout'|gettext}
                        {* control type="checkbox" name="allow_anonymous_checkout" label="Allow Anonymous Checkout" value=1 checked=$config.allow_anonymous_checkout *}
                        {group label="Cart"|gettext}
                            {control type="text" name="cart_title_text" label="Shopping Cart Title"|gettext value=$config.cart_title_text description='The title that appears at the top of your shopping cart.'|gettext}
                            {control type="html" name="cart_description_text" label="Shopping Cart Description Text"|gettext value=$config.cart_description_text description='This will be displayed at the top of your shopping cart.'|gettext}
                            {control type="html" name="policy" label="Store Policy"|gettext value=$config.policy description='Policy will be available in checkout view'|gettext}
                        {/group}
                        {group label="Checkout"|gettext}
                            {control type="text" name="checkout_title_top" label="Checkout Title"|gettext value=$config.checkout_title_top description='The title that appears at the top of your final confirmation checkout page.'|gettext}
                            {control type="html" name="checkout_message_top" label='Checkout Message - Top'|gettext rows=6 cols=60 value=$config.checkout_message_top description='This will be displayed on the top of your final confirmation checkout page.'|gettext}
                            {control type="html" name="checkout_message_bottom" label='Checkout Message - Bottom'|gettext rows=6 cols=60 value=$config.checkout_message_bottom description='This will be displayed on the bottom of your final confirmation checkout page.'|gettext}
                            {control type="textarea" name="ssl_seal" label='SSL Display Seal Code'|gettext rows=6 cols=60 value=$config.ssl_seal description='This will be displayed in various places on your site.'|gettext}
                        {/group}
                    </div>
                    <div id="tab4">
                        <h2>{"Display Settings"|gettext}</h2>
                        {group label="Product Listing Pages"|gettext}
                            {control type="number" name="images_per_row" label="Products per Row"|gettext size="3" value=$config.images_per_row|default:3 min=0 max=6 description='0 will use default'|gettext}
                            {control type="text" name="pagination_default" label="Default # of products to show per page"|gettext size=3 filter=integer value=$config.pagination_default}
                            {control type="checkbox" name="show_products" label="Show all products with the category?"|gettext value=1 checked=$config.show_products description='Show all products under category when displaying categories'|gettext}
                            {control type="checkbox" name="show_first_category" label="Show the first category in your store by default?"|gettext value=1 checked=$config.show_first_category description='Show first top-level category instead of all top level categories'|gettext}
                        {/group}
                        {group label="Product Detail Pages"|gettext}
                            {control type="checkbox" name="enable_ratings_and_reviews" label="Enable Ratings & Reviews?"|gettext value=1 checked=$config.enable_ratings_and_reviews}
                            {control type="checkbox" name="enable_lightbox" label="Enable Lightbox Image Viewer?"|gettext value=1 checked=$config.enable_lightbox}
                        {/group}
                        {group label="Product Sorting"|gettext}
                            {control type="dropdown" name="orderby" label="Default sort order"|gettext items="Name, Price, Rank"|gettxtlist values="title,base_price,rank" value=$config.orderby}
                            {control type="dropdown" name="orderby_dir" label="Sort direction"|gettext items="Ascending, Descending"|gettxtlist values="ASC, DESC" value=$config.orderby_dir}
                        {/group}
                        {control type="dropdown" name="ecom_search_results" label="Search Results"|gettext includeblank="All"|gettext items="Limit to eCommerce,Limit to Products"|gettxtlist values="ecom,products" value=$config.ecom_search_results description='Optionally Limit search results to eCommerce related items'|gettext}
                        {*
                        <h2>Sub Category Display</h2>
                        drop down coming soon...

                        <h2>Product Listing Display</h2>
                        drop down coming soon...

                        <h2>Product Detail Display</h2>
                        drop down coming soon...
                        *}
                    </div>
                    <div id="tab5">
                        <h2>{'Invoice Settings'|gettext}</h2>
                        {selectvalue table='orders_next_invoice_id' field='next_invoice_id' assign='inv_num'}
                        {$inv = 'Next Invoice #'|gettext|cat:$inv_num}
                        {control type="text" name="starting_invoice_number" label="Starting Invoice Number"|gettext size=50 value=$config.starting_invoice_number|default:'0001' description=$inv}
                        {control type="checkbox" name="enable_barcode" label="Enable Barcode?"|gettext value=1 checked=$config.enable_barcode}
                        {control type="checkbox" name="email_invoice_to_user" id="invoice_email" label="Email a copy of the invoice to the customer after purchase?"|gettext value=1 checked=$config.email_invoice_to_user}
                        <span id="email_settings">
                        {group label='Invoice Email Settings'|gettext}
                            {control type="text" name="from_name" label="Email From Name"|gettext value=$config.from_name}
                            {control type=email name="from_address" label="Email From Address"|gettext value=$config.from_address}
                            {control type="text" name="invoice_subject" label="Subject of invoice email"|gettext size="40" value=$config.invoice_subject}
                            {control type="textarea" name="invoice_msg" label="Message to put in invoice email:"|gettext rows=5 cols=45 value=$config.invoice_msg}
                        {/group}
                        </span>
                    {*</div>*}
                    {*<div id="tab6">*}
                        {*<h2>{'New Order Notifications'|gettext}</h2>*}
                        {control type="checkbox" name="email_invoice" label="Send email notification of new orders?"|gettext value=1 checked=$config.email_invoice}
                        {*{control type="text" name="email_invoice_addresses" label="Send email notifications to (separate email addresses with a comma)"|gettext size=60 value=$config.email_invoice_addresses}*}
                        {control type=text name="email_invoice_addresses" label="Send email notifications to (separate email addresses with a comma)"|gettext size=60 value=$config.email_invoice_addresses}
                    </div>
                    <div id="tab7">
                        <h2>{'General Location Settings'|gettext}</h2>
                        {control type="checkbox" name="address_allow_admins_all" label="Allow admins access to the full geographical data regardless of other settings?"|gettext value=1 checked=$config.address_allow_admins_all}
                    </div>
                    <div id="tab8">
                        <h2>{"Product Feeds Settings"|gettext}</h2>
                        <blockquote>
                            {'Allows you to activate online shopping service category selection/matching for your store categories.'|gettext}
                            {'Only used when you create an xml feed (file) for the shopping services list below.'|gettext}
                        </blockquote>
                        {control type="checkbox" name="product_types[Google]" label="Google Feed"|gettext value="google_product_type" checked=$config.product_types.Google}
                        {control type="checkbox" name="product_types[Bing]" label="Bing Feed"|gettext value="bing_product_type" checked=$config.product_types.Bing}
                        {control type="checkbox" name="product_types[NexTag]" label="NexTag Feed"|gettext value="nextag_product_type" checked=$config.product_types.NexTag}
                        {control type="checkbox" name="product_types[Shopzilla]" label="Shopzilla Feed"|gettext value="shopzilla_product_type" checked=$config.product_types.Shopzilla}
                        {control type="checkbox" name="product_types[Shopping]" label="Shopping Feed"|gettext value="shopping_product_type" checked=$config.product_types.Shopping}
                        {control type="checkbox" name="product_types[PriceGrabber]" label="Price Grabber Feed"|gettext value="pricegrabber_product_type" checked=$config.product_types.PriceGrabber}
                    </div>
                    <div id="tab9">
                        <h2>{"Gift Card Settings"|gettext}</h2>
                        {control type="text" name="minimum_gift_card_purchase" label="Minimum Gift Card Purchase"|gettext value=$config.minimum_gift_card_purchase filter=money}
                        {control type="text" name="custom_message_product" label="Custom Message Price"|gettext value=$config.custom_message_product filter=money}
                    </div>
                </div>
            </div>
            {*<div class="loadingdiv">{'Loading Settings'|gettext}</div>*}
            {loading title='Loading Settings'|gettext}
            {control type=buttongroup submit="Save Config"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="editchecks" jquery=1}
{literal}
$('#invoice_email').change(function() {
    if ($('#invoice_email').is(':checked') == false)
        $("#email_settings").hide("slow");
    else {
        $("#email_settings").show("slow");
    }
});
if ($('#invoice_email').is(':checked') == false)
    $("#email_settings").hide("slow");
{/literal}
{/script}

{script unique="editform" yui3mods="exptabs"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.expTabs({srcNode: '#storetabs'});
        Y.one('#storetabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
