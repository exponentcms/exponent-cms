{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

<div id="editproduct" class="module store edit">

    {if $record->id != ""}
        <h1>Edit Information for {$record->product_name}</h1>
    {else}
        <h1>New {$record->product_name}</h1>
    {/if}

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="product_type" value=$record->product_type}
        {control type="hidden" name="product_type_id" value=$record->product_type_id}
        
        <div id="editproduct-tabs" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>General Info</em></a></li>
	            <li><a href="#tab2"><em>Event Info</em></a></li>
	            <li><a href="#tab3"><em>Pricing</em></a></li>
	            <li><a href="#tab4"><em>Files & Images</em></a></li>
	            <li><a href="#tab5"><em>Categories</em></a></li>
	            <li><a href="#tab6"><em>SEO</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    {control type="text" name="title" label="Title" value=$record->title}
                    {control type="textarea" name="summary" label="Product Summary" rows=3 cols=45 value=$record->summary}
                    {control type="editor" name="body" label="Product Description" height=250 value=$record->body}          
                </div>
                <div id="tab2">
                    <h2>Number of Seats available</h2>
                    {control type="text" name="quantity" label="Number of seats" filter=integer size=4 value=$record->quantity}
                    <h2>Event Date/Time</h2>                
                    {control type="datetimecontrol" name="eventdate" label="Date of Event" value=$record->eventdate showtime=false}
                    {control type="datetimecontrol" name="event_starttime" label="Start Time" value=$record->event_starttime showdate=false}
                    {control type="datetimecontrol" name="event_endtime" label="End Time" value=$record->event_endtime showdate=false}
                    <h2>Signup Cutoff</h2> 
                    {control type="datetimecontrol" name="signup_cutoff" label="No registrations after" value=$record->signup_cutoff showtime=true}
                </div>
                <div id="tab3">
                    {control type="text" name="base_price" label="Event Price" value=$record->base_price filter=money}
                </div>
                <div id="tab4">
                    {control type=files name=files subtype="images" value=$record->expFile}
                </div>            
                <div id="tab5">
                    {control type="tagtree" id="managecats" name="managecats" model="storeCategory" draggable=false checkable=true values=$record->storeCategory}
                </div>
                <div id="tab6">
                    <h2>SEO Settings</h2>
                    {control type="text" name="sef_url" label="SEF URL" value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title" value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Description" value=$record->meta_description}
                    {control type="textarea" name="meta_description" label="Meta Keywords" value=$record->meta_keywords}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{'Loading'|gettext}</div>
        {control type="buttongroup" submit="Save Product"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="authtabs" yui3mods=1}
{literal}
//    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-tabview', function(Y) {
//        var YAHOO=Y.YUI2;
//        var tabView = new YAHOO.widget.TabView('demo');
//        YAHOO.util.Dom.removeClass("editproduct", 'hide');
//        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
//        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#editproduct-tabs'});
		tabview.render();
		Y.one('#editproduct-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
