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

<div id="editcategory" class="storecategory edit hide exp-skin-tabview">
	<div class="form_header">
        	<h1>Edit Store Category</h1>
        	<p>Complete and save the form below to configure this store category</p>
	</div>
    
    {script unique="cattabs" yuimodules="tabview, element"}
    {literal}
        var tabView = new YAHOO.widget.TabView('demo');
        
        var url = location.href.split('#');
        if (url[1]) {
            //We have a hash
            var tabHash = url[1];
            var tabs = tabView.get('tabs');
            for (var i = 0; i < tabs.length; i++) {
                if (tabs[i].get('href') == '#' + tabHash) {
                    tabView.set('activeIndex', i);
                    break;
                }
            }
        }
        
        
        YAHOO.util.Dom.removeClass("editcategory", 'hide');
        var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
        YAHOO.util.Dom.setStyle(loading, 'display', 'none');
        
    {/literal}
    {/script}
    
	{if $node->id == ""}
		{assign var=action value=create}
	{else}
		{assign var=action value=update}
	{/if}
	{form controller=storeCategory action=$action}
        {control type=hidden name=id value=$node->id}
        {control type=hidden name=parent_id value=$node->parent_id}
        {control type=hidden name=rgt value=$node->rgt}
        {control type=hidden name=lft value=$node->lft}                
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
				<li class="selected"><a href="#general"><em>General</em></a></li>
				<li><a href="#seo"><em>Meta Info</em></a></li>
				<li><a href="#events"><em>Events</em></a></li>
				<li><a href="#bing_product_types"><em>Bing Product Types</em></a></li>
				<li><a href="#google_product_types"><em>Google Product Types</em></a></li>
            </ul>            
            <div class="yui-content">
                <div id="general">   
					{control type=text name=title label="Category Name" value=$node->title}
					{control type="checkbox" name="is_active" label="This category is active" value=1 checked=$node->is_active|default:1}                                                
					{control type="files" name="image" label="Category Image" value=$node->expFile}
					{control type=editor name=body label="Category Description" value=$node->body}
	            </div>
                <div id="seo">
                    {control type=text name=sef_url label="SEF URL" value=$node->sef_url}                                                                                 
                    {control type=text name=meta_title label="Meta Title" value=$node->meta_title}
                    {control type=text name=meta_keywords label="Meta Keywords" value=$node->meta_keywords}
                    {control type=text name=meta_description label="Meta Description" value=$node->meta_description}                        
                </div>        
                 <div id="events">   
                    {control type="checkbox" name="is_events" label="This category is used for events" value=1 checked=$node->is_events}                        
                    {control type="checkbox" name="hide_closed_events" label="Don't Show Closed Events" value=1 checked=$node->hide_closed_events}
                </div>  
				<div id="bing_product_types">	
					<h1>Bing Product Types</h1>
					{* control type="tagtree" name="manage_bing_product_types" id="manage_bing_product_types" model="bing_product_types" draggable=false addable=false menu=true checkable=true expandonstart=false values=`$record->bing_product_types` *}
                    Coming soon....
				</div>
				<div id="google_product_types">	
					<h1>Google Product Types</h1>
					{* control type="tagtree" name="manage_google_product_types" id="manage_google_product_types" model="google_product_types" draggable=false addable=false menu=true checkable=true expandonstart=false values=`$record->google_product_types` *}
                    {* control type="dropdown" multiple=true items=$google_product_types size=20 *}
                    Coming soon....
				</div>
            </div>    
        </div>
        {control type=buttongroup submit=Save cancel=Cancel}
        {/form}                      
</div>
<div class="loadingdiv">Loading</div> 

