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

<div id="editcategory" class="storecategory edit hide exp-skin-tabview">
	<div class="form_header">
        	<h1>{'Edit Store Category'|gettext}</h1>
        	<p>{'Complete and save the form below to configure this store category'|gettext}</p>
	</div>
        
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
				<li class="selected"><a href="#general"><em>{'General'|gettext}</em></a></li>
				<li><a href="#seo"><em>{'Meta Info'|gettext}</em></a></li>
				<li><a href="#events"><em>{'Events'|gettext}</em></a></li>
				{if $product_types}
					{foreach from=$product_types key=key item=item}
						<li><a href="#{$item}"><em>{$key} {'Product Types'|gettext}</em></a></li>
					{/foreach}
				{/if}
            </ul>            
            <div class="yui-content">
                <div id="general">   
					{control type=text name=title label="Category Name"|gettext value=$node->title}
					{control type="checkbox" name="is_active" label="This category is active"|gettext value=1 checked=$node->is_active|default:1}
					{control type="files" name="image" label="Category Image"|gettext value=$node->expFile}
					{control type=editor name=body label="Category Description"|gettext value=$node->body}
	            </div>
                <div id="seo">
                    {control type=text name=sef_url label="SEF URL"|gettext value=$node->sef_url}
                    {control type=text name=meta_title label="Meta Title"|gettext value=$node->meta_title}
                    {control type=text name=meta_keywords label="Meta Keywords"|gettext value=$node->meta_keywords}
                    {control type=text name=meta_description label="Meta Description"|gettext value=$node->meta_description}
                </div>        
                 <div id="events">   
                    {control type="checkbox" name="is_events" label="This category is used for events"|gettext value=1 checked=$node->is_events}
                    {control type="checkbox" name="hide_closed_events" label="Don\'t Show Closed Events"|gettext value=1 checked=$node->hide_closed_events}
                </div>  
				{if $product_types}
					{foreach from=$product_types key=key item=item}
					<div id="{$item}">	
						<h1>{$key} {'Product Types'|gettext}</h1>
						{$product_type.$item}
					</div>
					{/foreach}
				{/if}
            </div>    
        </div>
        {control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
        {/form}                      
</div>
<div class="loadingdiv">{'Loading'|gettext}</div>
{script unique="cattabs" src="`$smarty.const.PATH_RELATIVE`framework/core/subsystems/forms/controls/listbuildercontrol.js"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','event','yui2-tabview','yui2-element', function(Y) {
    var YAHOO=Y.YUI2;
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
});    
{/literal}
{/script}


