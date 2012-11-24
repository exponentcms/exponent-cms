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
 
{css unique="storeListing" link="`$asset_path`css/storefront.css" corecss="button,clearfix"}

{/css}
 
<div class="module store showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>
    {else}
        <h1>{$current_category->title}</h1>
    {/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create == true || $permissions.edit == true}
            {icon class="add" action=create text="Add a Product"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {icon action=manage text="Manage Products"|gettext}
            {icon controller=storeCategory action=manage text="Manage Store Categories"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}

    {* current category image *}
    {if $current_category->expFile[0]->id && $config.banner_width}
        <div class="category-banner">
            {img file_id=$current_category->expFile[0]->id w=522 h=100}
        </div>
    {/if}
    {* current category permissions *}
    {if $current_category->id}
        {permissions}
            <div class="module-actions">
                {if $permissions.edit == 1}
                    {icon action=edit module=storeCategory record=$current_category title="Edit `$current_category->title`" text="Edit this Store Category"|gettext}{br}
                {/if}
                {*if $permissions.manage == 1}
                    {icon action=configure module=storeCategory record=$current_category title="Configure `$current_category->title`" text="Configure this Store Category"}{br}
                {/if*}
                {*if $permissions.manage == 1}
                    {icon action=configure module=ecomconfig hash="#tab2" title="Configure Categories Globally" text="Configure Categories Globally"}{br}
                {/if*}
                {if $permissions.manage == 1 && $config.orderby=="rank"}
                    {ddrerank label="Products"|gettext sql=$rerankSQL model="product" controller="storeCategory" id=$current_category->id}
                {/if}
                {if $permissions.edit == 1}
                     {icon class=add action=create text="Add a New Product"|gettext}
                {/if}
            </div>
        {/permissions}
    {/if}
    {* current category description *}
    {if $current_category->body}
        <div class="bodycopy">
            {$current_category->body}
        </div>
    {/if}
    {* current category's sub-categories *}
    {if $categories|@count > 0}
        <div class="cats">
            <h2>{'Categories'|gettext}{if $current_category->id} {'Under'|gettext} {$current_category->title}{/if}</h2>

            {counter assign="ipcr" name="ipcr" start=1}
            {$open_c_row=1}
            {foreach name="cats" from=$categories item="cat"}
                {if $cat->is_active==1 || $user->isAdmin()}

                    {if $smarty.foreach.cats.first || $open_c_row}
                        <div class="category-row">
                        {$open_c_row=0}
                    {/if}

                    <div class="cat{if $cat->is_active!=1} inactive{/if} clearfix">

                        {permissions}
                        <div class="item-actions">
                            {if $permissions.edit == 1}
                                {icon controller=storeCategory action=edit record=$cat title="Edit `$cat->title`"}
                            {/if}
                            {if $permissions.delete == 1}
                                {icon controller=storeCategory action=delete record=$cat title="Delete `$cat->title`" onclick="return confirm('"|cat:("Are you sure you want to delete this category?"|gettext)|cat:"');"}
                            {/if}
                        </div>
                        {/permissions}

                        <a href="{link controller=store action=showall title=$cat->sef_url}" class="cat-img-link">
                            {if $cat->expFile[0]->id}
                                {img file_id=$cat->expFile[0]->id w=$config.category_thumbnail|default:100 class="cat-image"}
                            {else}
                                {img file_id=$page->records[0]->expFile.mainimage[0]->id w=$config.category_thumbnail|default:100 class="cat-image"}
                            {/if}
                        </a>

                        <h3>
                            <a href="{link controller=store action=showall title=$cat->sef_url}">
                                {$cat->title}
                            </a>
                        </h3>

                    </div>

                    {if $smarty.foreach.cats.last || $ipcr%2==0}
                        </div>
                        {$open_c_row=1}
                    {/if}
                    {counter name="ipcr"}

                {/if}
            {/foreach}

            {* close the row if left open. might happen for non-admins *}
            {if $open_c_row==0}
                </div>
                {$open_c_row=1}
            {/if}
        </div>
    {else}
        <h2>{"All Products"|gettext} {if $current_category->id}{"Under"|gettext} {$current_category->title}{/if}</h2>
        {pagelinks paginate=$page top=1}
        {*control type="dropdown" name="sortme" items=$page->sort_dropdown default=$defaultSort*}

        {*script unique="sort-submit"}
        {literal}
        YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
            Y.all('select[name="sortme"]').on('change',function(e){
                window.location = e.target.get('value');
            });

        });
        {/literal}
        {/script*}
        <div class="products ipr{$config.images_per_row|default:3} listing-row">
            {counter assign="ipr" name="ipr" start=1}
            {foreach from=$page->records item=listing name=listings}
                {if $smarty.foreach.listings.first || $open_row}
                    <div class="product-row">
                    {$open_row=0}
                {/if}
                {include file=$listing->getForm('storeListing')}
                {if $smarty.foreach.listings.last || $ipr%$config.images_per_row==0}
                    </div>
                    {$open_row=1}
                {/if}
                {counter name="ipr"}
                {*if !$listing->active_type==2}

                    {$ipr}
                    {if $smarty.foreach.listings.first || $ipr%0}
                    {counter name="ipr" start=0}
                    <div class="product-row">
                    {/if}

                    {include file=$listing->getForm('storeListing')}

                    {if $smarty.foreach.listings.last || $ipr%0}
                        </div>
                    {/if}
                {counter name="ipr" start=$ipr+1}
                {/if*}
            {/foreach}
        </div>
        {*control type="dropdown" name="sortme" items=$page->sort_dropdown default=$defaultSort*}
        {pagelinks paginate=$page bottom=1}
    {/if}
</div>

{script unique="expanding-text" yui3mods="yui"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use("anim-easing","node","anim", function(Y) {
    
    var modules = Y.all('.showall.store .bodycopy');

    modules.each(function(n,k){
        // add fx plugin to module body
        var content = n.one('.more-text');
        if (!Y.Lang.isNull(content)) {
        content.plug(Y.Plugin.NodeFX, {
            to: { height: 0 },
            from: {
                height: function(node) { // dynamic in case of change
                    return node.get('scrollHeight'); // get expanded height (offsetHeight may be zero)
                }
            },

            easing: Y.Easing.easeOut,
            duration: 0.5
        });

        var onClick = function(e) {
            e.halt();
            n.toggleClass('yui-closed');
            content.fx.set('reverse', !content.fx.get('reverse')); // toggle reverse
            content.fx.run();
        };

        var control = n.one('.toggle');
        control.on('click', onClick);
        //n.one('.more-text .close').on('click', onClick);
        };
    });
    
});
{/literal}
{/script}
