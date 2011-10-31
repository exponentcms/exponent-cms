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
        
        <div id="editproduct-tabs" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>General Info</em></a></li>
	            <li><a href="#tab2"><em>Files & Images</em></a></li>
            </ul>            
            <div class="yui-content">
	            <div id="tab1">
	                {control type="text" name="title" label="Title" value=$record->title}
	                {control type="textarea" name="summary" label="Gift Card Summary" rows=3 cols=45 value=$record->summary}
	                {control type="editor" name="body" label="Gift Card Description" height=250 value=$record->body}
	            </div>
	     
	            <div id="tab2">
	                {control type=files name=files subtype="mainimage" value=$record->expFile}
	            </div>
	          
            </div>
        </div>
	    <div class="loadingdiv">{'Loading'|gettext}</div>
        {control type="buttongroup" submit="Save Gift Card" cancel="Cancel"}
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
