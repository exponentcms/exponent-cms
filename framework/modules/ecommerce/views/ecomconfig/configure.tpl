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

<div id="storeconfig" class="module ecomconfig configure">
    <h1>{'Store Configuration'|gettext}</h1>
    <div id="mainform">
        {form action=saveconfig}
            <div id="storetabs" class="yui-navset exp-skin-tabview hide">
                <ul class="yui-nav">
                    <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                    <li><a href="#tab2"><em>{'Cart Messages'|gettext}</em></a></li>
                    <li><a href="#tab3"><em>{'Categories and Display Options'|gettext}</em></a></li>
                    <li><a href="#tab4"><em>{'Notifications'|gettext}</em></a></li>
                    <li><a href="#tab5"><em>{'Emails'|gettext}</em></a></li>
                    <li><a href="#tab6"><em>{'Geography'|gettext}</em></a></li>
                    <li><a href="#tab7"><em>{'Invoice'|gettext}</em></a></li>
                    <li><a href="#tab8"><em>{'Display & Feature Settings'|gettext}</em></a></li>
                    <li><a href="#tab9"><em>{'Product Types'|gettext}</em></a></li>
                    <li><a href="#tab10"><em>{'Gift Cards'|gettext}</em></a></li>
                </ul>
                <div class="yui-content">
                    <div id="tab1">
                        <h2>{'General Configuration'|gettext}</h2>
                        {control type="text" name="storename" label="Store Name"|gettext value=$config.storename|default:'My Store'|gettext}
                        {* control type="checkbox" name="allow_anonymous_checkout" label="Allow Anonymous Checkout" value=1 checked=$config.allow_anonymous_checkout *}
                        {control type="text" name="starting_invoice_number" label="Starting Invoice Number"|gettext size=50 value=$config.starting_invoice_number|default:'0001'}
                        <h2>{'Header'|gettext}</h2>
                        <p>{'This will be displayed on the top of your emails and invoices.'|gettext}</p>
                        {control type="html" name="ecomheader" label=" " rows=6 cols=60 value=$config.ecomheader}
                        <h2>{'Footer'|gettext}</h2>
                        <p>{'This will be displayed on the bottom of your emails and invoices.'|gettext}</p>
                        {control type="html" name="ecomfooter" label=" " rows=6 cols=60 value=$config.ecomfooter}
                    </div>
                    <div id="tab2">
                        <h2>{'Cart Title'|gettext}</h2>
                        <p>{'The title that appears at the top of your shopping cart.'|gettext}</p>
                        {control type="text" name="cart_title_text" label="Shopping Cart Title"|gettext value=$config.cart_title_text}
                        <h2>{'Cart Message'|gettext}</h2>
                        <p>{'This will be displayed at the top of your shopping cart.'|gettext}</p>
                        {control type="html" name="cart_description_text" label="Shopping Cart Description Text"|gettext value=$config.cart_description_text}
                        <hr>
                        <h2>{'Checkout Title'|gettext}</h2>
                        <p>{'The title that appears at the top of your final confirmation checkout page.'|gettext}</p>
                        {control type="text" name="checkout_title_top" label="Checkout Title"|gettext value=$config.checkout_title_top}
                        <h2>{'Checkout Message - Top'|gettext}</h2>
                        <p>{'This will be displayed on the top of your final confirmation checkout page.'|gettext}</p>
                        {control type="html" name="checkout_message_top" label=" " rows=6 cols=60 value=$config.checkout_message_top}
                        <h2>{'Checkout Message - Bottom'|gettext}</h2>
                        <p>{'This will be displayed on the bottom of your final confirmation checkout page.'|gettext}</p>
                        {control type="html" name="checkout_message_bottom" label=" " rows=6 cols=60 value=$config.checkout_message_bottom}
                        <h2>{'SSL Display Seal Code'|gettext}</h2>
                        <p>{'This will be displayed in various places on your site.'|gettext}</p>
                        {control type="textarea" name="ssl_seal" label=" " rows=6 cols=60 value=$config.ssl_seal}
                    </div>
                    <div id="tab3">
                        <h2>{'Product Sorting'|gettext}</h2>
                        {control type="dropdown" name="orderby" label="Default sort order"|gettext items="Name, Price, Rank"|gettxtlist values="title,base_price,rank" value=$config.orderby}
                        {control type="dropdown" name="orderby_dir" label="Sort direction"|gettext items="Ascending, Descending"|gettxtlist values="ASC, DESC" value=$config.orderby_dir}

                        <h2>{'Pagination and Display'|gettext}</h2>
                        {control type="text" name="pagination_default" label="Default # of products to show per page"|gettext size=3 filter=integer value=$config.pagination_default}
                        {control type="checkbox" name="show_first_category" label="Show the first category in your store by default?"|gettext value=1 checked=$config.show_first_category}
                        {*
                        <h2>Sub Category Display</h2>
                        drop down coming soon...

                        <h2>Product Listing Display</h2>
                        drop down coming soon...

                        <h2>Product Detail Display</h2>
                        drop down coming soon...
                        *}
                    </div>
                    <div id="tab4">
                        <h2>{'Notifications'|gettext}</h2>
                        {control type="checkbox" name="email_invoice" label="Send email notification of new orders?"|gettext value=1 checked=$config.email_invoice}
                        {control type="text" name="email_invoice_addresses" label="Send email notifications to (separate email addresses with a comma)"|gettext size=60 value=$config.email_invoice_addresses}
                    </div>
                    <div id="tab5">
                        <h2>{'Store Email Settings'|gettext}</h2>
                        {control type="text" name="from_name" label="Email From Name"|gettext value=$config.from_name}
                        {control type="text" name="from_address" label="Email From Address"|gettext value=$config.from_address}
                        {control type="checkbox" name="email_invoice_to_user" label="Email a copy of the invoice to the user after purchase?"|gettext value=1 checked=$config.email_invoice_to_user}
                        {control type="text" name="invoice_subject" label="Subject of invoice email"|gettext size="40" value=$config.invoice_subject}
                        {control type="textarea" name="invoice_msg" label="Message to put in invoice email:"|gettext rows=5 cols=45 value=$config.invoice_msg}
                    </div>
                    <div id="tab6">
                        <h2>{'General Address/Geo Settings'|gettext}</h2>
                        {control type="checkbox" name="address_allow_admins_all" label="Allow admins access to the full geographical data regardless of other settings?"|gettext value=1 checked=$config.address_allow_admins_all}
                    </div>
                    <div id="tab7">
                        <h2>{'Invoice Settings'|gettext}</h2>
                        {control type="checkbox" name="enable_barcode" label="Enable Barcode?"|gettext value=1 checked=$config.enable_barcode}
                    </div>
                    <div id="tab8">
                        <h2>{"Product Listing Pages"|gettext}</h2>
                        {control type="text" name="images_per_row" label="Products per row"|gettext size="3" value=$config.images_per_row}
                        <h2>{"Product Detail Pages"|gettext}</h2>
                        {control type="checkbox" name="enable_ratings_and_reviews" label="Enable Ratings & Reviews?"|gettext value=1 checked=$config.enable_ratings_and_reviews}
                        {control type="checkbox" name="enable_lightbox" label="Enable Lightbox Image Viewer?"|gettext value=1 checked=$config.enable_lightbox}
                    </div>
                    <div id="tab9">
                        <h2>{"Product Type Settings"|gettext}</h2>
                        {control type="checkbox" name="product_types[Google]" label="Google Feed"|gettext value="google_product_type" checked=$config.product_types.Google}
                        {control type="checkbox" name="product_types[Bing]" label="Bing Feed"|gettext value="bing_product_type" checked=$config.product_types.Bing}
                        {control type="checkbox" name="product_types[NexTag]" label="NexTag Feed"|gettext value="nextag_product_type" checked=$config.product_types.NexTag}
                        {control type="checkbox" name="product_types[Shopzilla]" label="Shopzilla Feed"|gettext value="shopzilla_product_type" checked=$config.product_types.Shopzilla}
                        {control type="checkbox" name="product_types[Shopping]" label="Shopping Feed"|gettext value="shopping_product_type" checked=$config.product_types.Shopping}
                        {control type="checkbox" name="product_types[PriceGrabber]" label="Price Grabber Feed"|gettext value="pricegrabber_product_type" checked=$config.product_types.PriceGrabber}
                    </div>
                    <div id="tab10">
                        <h2>{"Gift Card Settings"|gettext}</h2>
                        {control type="text" name="minimum_gift_card_purchase" label="Minimum Gift Card Purchase"|gettext value=$config.minimum_gift_card_purchase filter=money}
                        {control type="text" name="custom_message_product" label="Custom Message Price"|gettext value=$config.custom_message_product filter=money}
                    </div>
                </div>
            </div>
            <div class="loadingdiv">{'Loading Settings'|gettext}</div>
            {control type=buttongroup submit="Save Config"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#storetabs'});
        Y.one('#storetabs').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
