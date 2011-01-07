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
 
{css unique="storeListing" link="`$asset_path`css/storeListing.css"}

{/css}


 
<div class="module store showall">
    
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    <h1>
    <span>{$current_category->title}</span>
    {if $current_category->expFile[0]->id}
        {img file_id=$current_category->expFile[0]->id w=600 h=100 zc=1}
    {/if}
    </h1>
    {if $current_category->id}
    {permissions}
        {if $permissions.edit == 1}
            {icon class="edit" action=edit module=storeCategory id=$current_category->id title="Edit `$current_category->title`" text="Edit this Store Category"}{br}
        {/if}
        {*if $permissions.manage == 1}
            {icon class="configure" action=configure module=storeCategory id=$current_category->id title="Configure `$current_category->title`" text="Configure this Store Category"}{br}
        {/if*}
        {*if $permissions.manage == 1}
            {icon class="configure" action=configure module=ecomconfig hash="#tab2" title="Configure Categories Globally" text="Configure Categories Globally"}{br}
        {/if*}
        {if $permissions.edit == 1 && $config.orderby=="rank"}
            {ddrerank label="Products" sql=$rerankSQL model="product" controller="storeCategory" id=$current_category->id}
        {/if}
        {if $permissions.edit == 1}
              {icon class="add" action=create title="Add a new product" text="Add a New Product"}
        {/if}
    {/permissions}
    {/if}
    
    <div class="bodycopy">{$current_category->body}</div>

    {if $categories|@count > 0}
    <div class="cats">
    <h2>Categories Under {$current_category->title}</h2>
    {foreach name="cats" from=$categories item="cat"}
    {if $cat->is_active==1 || $user->is_acting_admin}
	{counter assign=iteration}
        {if $iteration%2==0}
            {assign var="positioninfo" value=" last-in-row"}
        {else}
            {assign var="positioninfo" value=""}
        {/if}
        
        <div class="cat{$positioninfo}{if $cat->is_active!=1} inactive{/if}">
            {permissions level=$smarty.const.UILEVEL_PERMISSIONS}
            <div class="item-permissions">
                {if $permissions.edit == 1}
                    {icon img=edit.png controller=storeCategory action=edit id=$cat->id title="Edit `$cat->title`"}
                {/if}
                {if $permissions.delete == 1}
                    {icon img=delete.png controller=storeCategory action=delete id=$cat->id title="Delete `$cat->title`" onclick="return confirm('Are you sure you want to delete this category?');"}
                {/if}
            </div>
            {/permissions}
            <a href="{link controller=store action=showall title=$cat->sef_url}" class="cat-img">
                {if $cat->expFile[0]->id}
                    {img file_id=$cat->expFile[0]->id w=100 class="cat-image"}
                {else}
                    {img file_id=$page->records[0]->expFile.mainimage[0]->id w=100 class="cat-image"}
                {/if}
                <h3>{$cat->title}</h3>
            </a>
        </div>
    {/if}
    {/foreach}
    <div style="clear:both"></div>
    </div>
    {/if}
    
    <h2>All Products Under {$current_category->title}</h2>
        
        
    {$page->links}
    {*control type="dropdown" name="sortme" items=$page->sort_dropdown default=$defaultSort*}
    
    {*script unique="sort-submit"}
    {literal}
    YUI({ base:EXPONENT.URL_FULL+'external/yui3/build/',loadOptional: true}).use('node', function(Y) {
        Y.all('select[name="sortme"]').on('change',function(e){
            window.location = e.target.get('value');
        });

    });
    {/literal}
    {/script*}
   
    <div class="products">
        {foreach from=$page->records item=listing name=listings}
        
        {if $smarty.foreach.listings.iteration%3==0}
            {assign var="positioninfo" value=" last-in-row"}
        {else}
            {assign var="positioninfo" value=""}
        {/if}
        
        <div class="product{$positioninfo}">
            {include file=$listing->getForm('storeListing')}
        </div>
        
        {if $positioninfo!="" || $smarty.foreach.listings.last==true}
            <div class="break">&nbsp;</div>
        {/if}
        
        {/foreach}
    </div>
    {*control type="dropdown" name="sortme" items=$page->sort_dropdown default=$defaultSort*}

    {$page->links}
    
</div>
