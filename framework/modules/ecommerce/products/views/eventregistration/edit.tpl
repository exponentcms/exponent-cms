
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

<div id="editproduct" class="module store edit">

    {if $record->id != ""}
        <h1>{'Edit Information for'|gettext} {$record->product_name}</h1>
    {else}
        <h1>{'New'|gettext} {$record->product_name}</h1>
    {/if}

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
        {control type="hidden" name="product_type" value=$record->product_type}
        {control type="hidden" name="product_type_id" value=$record->product_type_id}
        
        <div id="editproduct-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>{'General Info'|gettext}</em></a></li>
	            <li><a href="#tab2"><em>{'Event Info'|gettext}</em></a></li>
	            <li><a href="#tab3"><em>{'Pricing'|gettext}</em></a></li>
	            <li><a href="#tab4"><em>{'Files & Images'|gettext}</em></a></li>
	            <li><a href="#tab5"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    {control type="text" name="title" label="Title"|gettext value=$record->title}
                    {control type="textarea" name="summary" label="Product Summary"|gettext rows=3 cols=45 value=$record->summary}
                    {control type="editor" name="body" label="Product Description"|gettext height=250 value=$record->body}
                </div>
                <div id="tab2">
                    <h2>{'Number of Seats available'|gettext}</h2>
                    {control type="text" name="quantity" label="Number of seats"|gettext filter=integer size=4 value=$record->quantity}
                    <h2>{'Event Date/Time'|gettext}</h2>
					{control type="yuicalendarcontrol" name="eventdate" label="Date of Event"|gettext value=$record->eventdate}
                    {control type="datetimecontrol" name="event_starttime" label="Start Time"|gettext value=$record->event_starttime showdate=false}
                    {control type="datetimecontrol" name="event_endtime" label="End Time"|gettext value=$record->event_endtime showdate=false}
                    <h2>{'Signup Cutoff'|gettext}</h2>
					{control type="yuicalendarcontrol" name="signup_cutoff" label="No registrations after"|gettext value=$record->signup_cutoff showtime = true}
                </div>
                <div id="tab3">
                    {control type="text" name="base_price" label="Event Price"|gettext value=$record->base_price filter=money}
                </div>
                <div id="tab4">
                    {control type=files name=mainimages subtype="mainimage" value=$record->expFile}
                </div>            
                <div id="tab5">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Description"|gettext value=$record->meta_description}
                    {control type="textarea" name="meta_description" label="Meta Keywords"|gettext value=$record->meta_keywords}
                </div>
            </div>
        </div>
	   
        {control type="buttongroup" submit="Save Product"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="authtabs" yui3mods=1}
{literal}
	 YUI(EXPONENT.YUI3_CONFIG).use("get", "tabview", "node-load","event-simulate", function(Y) {
		var tabview = new Y.TabView({srcNode:'#editproduct-tabs'});
		tabview.render();
		Y.one('#editproduct-tabs').removeClass('hide');

    });
{/literal}
{/script}
