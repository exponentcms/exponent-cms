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
 
{css unique="storeListing" link="`$asset_path`css/storefront.css" corecss="button,clearfix"}

{/css}

{css unique="storeListing" link="`$asset_path`css/storefront_bs3.css"}

{/css}

<div class="module store showall">
    <div class="category-breadcrumb">
        <a href="{link controller=store action=showall}" title="{'View the Store'|gettext}">{'Store'|gettext}</a>{if count($ancestors)}&#160;&#160;&raquo;&#160;{/if}
        {foreach from=$ancestors item=ancestor name=path}
            {if !$smarty.foreach.path.last}
                <a href="{link controller=store action=showall title=$ancestor->sef_url}" title="{'View this Product Category'|gettext}">{$ancestor->title}</a>&#160;&#160;&raquo;&#160;
            {else}
                {$ancestor->title}
            {/if}
        {/foreach}
    </div>
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>
    {else}
        <{$config.heading_level|default:'h1'}>{$current_category->title}</{$config.heading_level|default:'h1'}>
    {/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class="add" action=create text="Add a Product"|gettext}
            {/if}
            {if $permissions.manage}
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
    {if $current_category->expFile[0]->id}
        <div class="category-banner">
            {img class="img-responsive" file_id=$current_category->expFile[0]->id w=522 h=100}
        </div>
    {/if}
    {* current category permissions *}
    {if $current_category->id}
        {permissions}
            <div class="module-actions">
                {if $permissions.edit}
                    {icon action=edit module=storeCategory record=$current_category title="Edit `$current_category->title`" text="Edit this Store Category"|gettext}{br}
                {/if}
                {if $permissions.manage}
                    {icon action=configure module=storeCategory record=$current_category title="Configure `$current_category->title`" text="Configure this Store Category"|gettext}{br}
                {/if}
                {*if $permissions.manage}
                    {icon action=configure module=ecomconfig hash="#tab2" title="Configure Categories Globally" text="Configure Categories Globally"}{br}
                {/if*}
                {if $permissions.manage && $config.orderby=="rank"}
                    {ddrerank label="Products"|gettext sql=$rerankSQL model="product" controller="storeCategory" id=$current_category->id}
                {/if}
                {*{if $permissions.create}*}
                     {*{icon class=add action=create text="Add a New Product"|gettext}*}
                {*{/if}*}
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
                            {if $permissions.edit}
                                {icon controller=storeCategory action=edit record=$cat title="Edit `$cat->title`"}
                            {/if}
                            {if $permissions.manage}
                                {icon controller=storeCategory action=configure record=$cat title="Configure `$cat->title`"}
                            {/if}
                            {if $permissions.delete}
                                {icon controller=storeCategory action=delete record=$cat title="Delete `$cat->title`" onclick="return confirm('"|cat:("Are you sure you want to delete this category?"|gettext)|cat:"');"}
                            {/if}
                        </div>
                        {/permissions}

                        <a href="{link controller=store action=showall title=$cat->sef_url}" class="cat-img-link" title="{$cat->body|summarize:"html":"para"}">
                            {if $cat->expFile[0]->id}
                                {img file_id=$cat->expFile[0]->id w=$config.category_thumbnail|default:100 class="cat-image img-responsive"}
                            {elseif $page->records[0]->expFile.mainimage[0]->id}
                                {img file_id=$page->records[0]->expFile.mainimage[0]->id w=$config.category_thumbnail|default:100 class="cat-image img-responsive"}
                            {else}
                                {img src="`$asset_path`images/no-image.jpg" w=$config.category_thumbnail|default:100 class="cat-image img-responsive" alt="'No Image Available'|gettext"}
                            {/if}
                        {*</a>*}

                        <h3>
                            {*<a href="{link controller=store action=showall title=$cat->sef_url}">*}
                                {$cat->title}
                            {*</a>*}
                        </h3>
                        {*<div class="body-copy">*}
                            {*{$cat->body}*}
                        {*</div>*}
                        </a>
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
    {/if}
    {if !$categories|@count || $config.show_products}
        <{$config.item_level|default:'h2'}>{"All Products"|gettext} {if $current_category->id}{"Under"|gettext} {$current_category->title}{/if}</{$config.item_level|default:'h2'}>
        <div class="row">
            <div class="col-sm-5 col-sm-push-5"><div class="row">{control type="dropdown" name="sortme" label="Sort By"|gettext items=$page->sort_dropdown default=$defaultSort horizontal=1}</div></div>
            <div class="btn-group pull-right list-grid">
                <a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list"></span>{'List'|gettext}</a>
                <a href="#" id="grid" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th"></span>{'Grid'|gettext}</a>
            </div>
        </div>
        {script unique="sort-submit"}
        {literal}
            YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
                Y.all('select[name="sortme"]').on('change',function(e){
                    window.location = e.target.get('value');
                });
            });
        {/literal}
        {/script}
        {pagelinks paginate=$page top=1}
        <div id="products" class="products z-ipr{$config.images_per_row|default:3} listing-row row list-group">
            <div class="col-sm-12">
            {counter assign="ipr" name="ipr" start=1}
            {foreach from=$page->records item=listing name=listings}
                {if $smarty.foreach.listings.first || $open_row}
                    <div class="z-product-row row">
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
        </div>
        {*control type="dropdown" name="sortme" items=$page->sort_dropdown default=$defaultSort*}
        {pagelinks paginate=$page bottom=1}
    {/if}
</div>

{script unique="expanding-text" yui3mods="anim-easing,node,anim"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    
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

{script unique="list-grid" jquery='jquery.cookie'}
{literal}
    $(document).ready(function() {
        $('#list').click(function(event){
            event.preventDefault();
            $('#products .item').addClass('list-group-item');
            $.cookie('ecommerce-view', 'list', { expires: 7, path: '/' });
        });
        $('#grid').click(function(event){
            event.preventDefault();
            $('#products .item').removeClass('list-group-item');
            $('#products .item').addClass('grid-group-item');
            $.cookie('ecommerce-view', 'grid', { expires: 7, path: '/' });
        });
        var view = $.cookie('ecommerce-view');
        if (view == 'list') {
            $('#products .item').addClass('list-group-item');
        } else {
            $('#products .item').removeClass('list-group-item');
            $('#products .item').addClass('grid-group-item');
        }
    });
{/literal}
{/script}
