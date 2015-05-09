{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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
 
{css unique="storefront" link="`$asset_path`css/storefront.css" corecss="button,tables"}

{/css}

{css unique="ecom" link="`$asset_path`css/ecom.css"}

{/css}

{if $product->user_message != ''}
    {message class=notice text=$product->user_message}
{/if}

<div class="module store show product row">
    <div class="category-breadcrumb col-sm-12">
        <a href="{link controller=store action=showall}" title="{'View the Store'|gettext}">{'Store'|gettext}</a>&#160;&#160;&raquo;&#160;
        {foreach from=$ancestors item=ancestor name=path}
            <a href="{link controller=store action=showall title=$ancestor->sef_url}" title="{'View this Product Category'|gettext}">{$ancestor->title}</a>&#160;&#160;&raquo;&#160;
        {/foreach}
        {$product->title}
    </div>
    <div itemscope itemtype="http://data-vocabulary.org/Product">
        {if !empty($product->storeCategory[0]->title)}<span itemprop="category" content="{$product->storeCategory[0]->title}"></span>{/if}
        {permissions}
        <div class="item-actions col-sm-12">
            {if $permissions.edit}
                {icon action=edit record=$product title="Edit `$product->title`"}
                {icon action=copyProduct class="copy" text="Copy Product"|gettext title="Copy `$product->title` " record=$product}
                {icon class="add" action=edit parent_id=$product->id product_type='childProduct' text='Add Child Product'|gettext}
            {/if}
            {if $permissions.delete}
                {icon action=delete record=$product title="Delete `$product->title`" onclick="return confirm('Are you sure you want to delete this product?');"}
            {/if}
        </div>
        {/permissions}

        {******* IMAGES *****}
        <div class="col-sm-6">
            <div class="large-ecom-image" style="float: none;">
                {if $product->main_image_functionality=="iws"}
                    {* Image with swatches *}
                    {if $product->expFile.imagesforswatches[0]->id != ""}
                        {img file_id=$product->expFile.imagesforswatches[0]->id w=250 alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image" itemprop=1}
                    {else}
                        {img src="`$asset_path`images/no-image.jpg" w=250 alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image" itemprop=1}
                    {/if}
                    {$mainimg=$product->expFile.imagesforswatches.0}
                {else}
                    {* Single Image *}
                    {if $config.enable_lightbox}
                        <a href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$product->expFile.mainimage[0]->id}&w={$config.enlrg_w|default:500}" title="{$product->expFile.mainimage[0]->title|default:$product->title}" rel="lightbox[g{$product->id}]" id="enlarged-image-link">
                    {/if}
                    {if $product->expFile.mainimage[0]->id != ""}
                        {img file_id=$product->expFile.mainimage[0]->id w=250 alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`"  class="large-img" id="enlarged-image" itemprop=1}
                    {else}
                        {img src="`$asset_path`images/no-image.jpg" w=250 alt=$product->image_alt_tag|default:"Image of `$product->title`" title="`$product->title`" class="large-img" id="enlarged-image" itemprop=1}
                    {/if}
                    {if $config.enable_lightbox}
                        </a>
                    {/if}
                    {$mainimg=$product->expFile.mainimage.0}
                {/if}

                {if $product->expFile.images[0]->id}
                    {* Additional Images *}
                    <div class="additional thumbnails">
                        <h3>{"Additional Images"|gettext}</h3>
                        <ul>
                            {*<li>*}
                                {*{if $config.enable_lightbox}*}
                                    {*<a href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$product->expFile.mainimage[0]->id}&w={$config.enlrg_w|default:500}" title="{$mainimg->title|default:$product->title}" rel="lightbox[g{$product->id}]">*}
                                {*{/if}*}
                                {*{img file_id=$product->expFile.mainthumbnail[0]->id|default:$mainimg->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$mainimg->id`"}*}
                                {*{if $config.enable_lightbox}*}
                                    {*</a>*}
                                {*{/if}*}
                            {*</li>*}
                            {foreach from=$product->expFile.images item=thmb}
                                <li>
                                    {if $config.enable_lightbox}
                                        <a href="{$smarty.const.PATH_RELATIVE}thumb.php?id={$thmb->id}&w={$config.enlrg_w|default:500}" title="{$thmb->title|default:$product->title}" rel="lightbox[g{$product->id}]">
                                    {/if}
                                    {img file_id=$thmb->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$thmb->id`"}
                                    {if $config.enable_lightbox}
                                        </a>
                                    {/if}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}

                {if $config.enable_lightbox}
                    {script unique="thumbswap-shadowbox" yui3mods="node-event-simulate,gallery-lightbox"}
                    {literal}
                        EXPONENT.YUI3_CONFIG.modules = {
                            'gallery-lightbox' : {
                                fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
                                requires : ['base','node','anim','selector-css3','lightbox-css']
                            },
                            'lightbox-css': {
                                fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/css/gallery-lightbox.css',
                                type: 'css'
                            }
                        }

                        YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
                            Y.Lightbox.init();

                            if (Y.one('#enlarged-image-link') != null) {
                                Y.one('#enlarged-image-link').on('click',function(e){
                                   if(!Y.Lang.isNull(Y.one('.thumbnails'))) {
                                      e.halt();
                                      e.currentTarget.removeAttribute('rel');
                                      Y.Lightbox.init();
                                      Y.one('.thumbnails ul li a').simulate('click');
                                   }
                                });
                            }
                        //}

                        // Shadowbox.init({
                        //     modal: true,
                        //     overlayOpacity: 0.8,
                        //     continuous: true
                        // });
                        //
                        // var mainimg = Y.one('#main-image');
                        // if (!Y.Lang.isNull(mainimg)) {
                        //     mainimg.on('click',function(e){
                        //         e.halt();
                        //         var content = Shadowbox.cache[1].content;
                        //         Shadowbox.open ({
                        //                         content: content,
                        //                         player: "img",
                        //                         gallery: "images"
                        //                         });
                        //     })
                        // };
                        });
                    {/literal}
                    {/script}
                {/if}
                {script unique="thumbswap-shadowbox2" yui3mods="node"}
                {literal}
                    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
                        var thumbs = Y.all('.thumbnails li img.thumbnail');
                        var swatches = Y.all('.swatches li img.swatch');
                        var mainimg = Y.one('#enlarged-image');

                        var swapimage = function(e){
                            var tmbid = e.target.get('id').split('-')[1];
                            mainimg.set('src',EXPONENT.PATH_RELATIVE+"thumb.php?id="+tmbid+"&w=250");
                        };

                    {/literal}
                    {if !$config.enable_lightbox}
                        thumbs.on('click',swapimage);
                    {/if}
                    {literal}
                        swatches.on('click',swapimage);
                    });
                {/literal}
                {/script}
            </div>

            {if $product->main_image_functionality=="iws"}
                <div class="swatches thumbnails">
                    <h3>{"Available Patterns"|gettext}</h3>
                    <ul>
                        {foreach from=$product->expFile.swatchimages item=swch key=key}
                            <li>
                                {img file_id=$swch->id w=32 h=32 zc=1 class="swatch" id="thumb-`$product->expFile.imagesforswatches[$key]->id`"}
                                <div>{img file_id=$product->expFile.imagesforswatches[$key]->id w=100 h=100 zc=1 class="swatch" id="thumb-`$product->expFile.imagesforswatches[$key]->id`"}{if $swch->title}<strong>{$swch->title}</strong>{/if}</div>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            {/if}

            {if $product->main_image_functionality=="iws" || $product->expFile.images[0]->id}

            {/if}

        </div>

        <div class="col-sm-6">
            <{$config.heading_level|default:'h1'}><span itemprop="name">{$product->title}</span></{$config.heading_level|default:'h1'}>

            <div class="prod-price">
                <span itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
                <meta itemprop="currency" content="{$smarty.const.ECOM_CURRENCY}" />
                {* availability type
                    [0] => Always available even if out of stock.
                    [1] => Available but shown as backordered if out of stock.
                    [2] => Unavailable if out of stock.
                    [3] => Show as &quot;Call for Price&quot;.
                *}
                {if $product->availability_type == 2 && $product->quantity - $product->minimum_order_quantity < 0}
                    <span itemprop="availability" content="out_of_stock"></span>
                {else}
                    <span itemprop="availability" content="in_stock"></span>
                {/if}
                {if $product->availability_type == 3}
                    <strong>{"Call for Price"|gettext}</strong>
                {elseif $product->childProduct|@count >= 1}
                    {$child_price = $product->childProduct[0]->base_price}
                    {foreach from=$product->childProduct item=chiprod}
                        {if $child_price > $chiprod->base_price}{$child_price = $chiprod->base_price}{/if}
                        {if $chiprod->use_special_price}
                            {if $child_price > $chiprod->special_price}{$child_price = $chiprod->special_price}{/if}
                        {/if}
                    {/foreach}
                    <span class="regular-price">{'Starting at'|gettext} <span itemprop="price">{$child_price|currency}</span></span>
                {else}
                    {if $product->use_special_price}
                        <span class="regular-price on-sale">{$product->base_price|currency}</span>
                        <span class="sale-price"><span id="item-price" itemprop="price">{$product->special_price|currency}</span>&#160;<sup>{"SALE!"|gettext}</sup></span>
                    {else}
                        <span class="regular-price"><span id="item-price" itemprop="price">{$product->base_price|currency}</span></span>
                    {/if}
                {/if}
                </span>
            </div>

            {if $product->company->id}
                <p class="manufacturer">
                    {"Manufactured by"|gettext}:
                    <a href="{link controller=company action=show id=$product->company->id}">
                        {if $product->company->expFile.logo[0]->id}
                            {img file_id=$product->company->expFile.logo[0]->id w=24 alt="Image of `$product->company->title`" title="`$product->company->title`" class="large-img" id="enlarged-image"}
                        {/if}
                        <span itemprop="brand">{$product->company->title}</span>
                    </a>
                </p>
            {/if}

            {if $product->model && ($product->childProduct|@count == 0)}
                <p class="sku">
                    {"SKU"|gettext}:
                    <strong><span itemprop="identifier" content="{$product->model}">{$product->model}</span></strong>
                </p>
            {/if}

            {if $product->warehouse_location}
                <p class="warehouse-location">
                    LOC:{$product->warehouse_location}
                </p>
            {/if}

            {*{chain controller="snippet" action="showall" source="prodsnip`$product->id`"}*}
            {showmodule controller=snippet action=showall source="prodsnip`$product->id`"}

            {if $product->minimum_order_quantity > 1}
                <p>
                    <span>{"This item has a minimum order quantity of"|gettext} {$product->minimum_order_quantity}</span>
                </p>
            {/if}
            {if $product->multiple_order_quantity > 1}
                <p>
                    <span>{"This item must be ordered in quantities of"|gettext} {$product->multiple_order_quantity}</span>
                </p>
            {/if}

            {*if $product->expFile.images[0]->id}
            <div class="additional thumbnails">
                <h3>{"Additional Images"|gettext}</h3>
                <ul>
                    {if $product->expFile.mainthumbnail[0]->id}
                        <li>{img file_id=$product->expFile.mainthumbnail[0]->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$mainimg`"}</li>
                    {else}
                        <li>{img file_id=$mainimg w=50 h=50 zc=1 class="thumbnail" id="thumb-`$mainimg`"}</li>
                    {/if}
                {foreach from=$product->expFile.images item=thmb}
                    <li>{img file_id=$thmb->id w=50 h=50 zc=1 class="thumbnail" id="thumb-`$thmb->id`"}</li>
                {/foreach}
                </ul>
            </div>
            {/if*}
            {if $config.enable_ratings_and_reviews}
                <div class="reviews well">
                    {rating content_type="product" subtype="quality" label="Product Rating"|gettext record=$product itemprop=1 readonly=1}
                    {comments_count record=$product show=1 type='Review'|gettext}
                </div>
            {/if}

            <div class="bodycopy">
                <span itemprop="description">
                    {$product->body}
                </span>
            </div>

            {if $product->expFile.brochures[0]->id}
                <div class="more-information">
                    <h3>{"Additional Product Information"|gettext}</h3>
                    <ul>
                        {foreach from=$product->expFile.brochures item=doc}
                            <li><a class="downloadfile" href="{link action=downloadfile id=$doc->id}" title="{'Click to download file'|gettext}">{if $doc->title}{$doc->title}{else}{$doc->filename}{/if}</a></li>
                        {/foreach}
                    </ul>
                </div>
            {/if}
        </div>

             {if $product->childProduct|@count == 0}
                 <div class="addtocart col-sm-12">
                     <div class="row">
                     {form id="addtocart`$product->id`" controller=cart action=addItem}
                         {control type="hidden" name="product_id" value="`$product->id`"}
                         {control type="hidden" name="product_type" value="`$product->product_type`"}
                         {*control name="qty" type="text" value="`$product->minimum_order_quantity`" size=3 maxlength=5 class="lstng-qty"*}

                     {* NOTE display product options *}
                     <div class="col-sm-6">
                         {if $product->show_options}
                             {exp_include file="options.tpl"}
                         {/if}
                     </div>

                        <div class="add-to-cart-btn input col-sm-6">
                            {if $product->availability_type == 0 && $product->active_type == 0}
                                <input type="text" class="text form-control" size="5" value="{$product->minimum_order_quantity|default:1}" name="quantity">
                                <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                    {"Add to Cart"|gettext}
                                </button>
                            {elseif $product->availability_type == 1 && $product->active_type == 0}
                                <input type="text" class="text form-control" size="5" value="{$product->minimum_order_quantity|default:1}" name="quantity">
                                <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                    {"Add to Cart"|gettext}
                                </button>
                                {if $product->quantity <= 0}<span class="error">{$product->availability_note}</span>{/if}
                            {elseif $product->availability_type == 2}
                                {if $product->quantity - $product->minimum_order_quantity >= 0}
                                    <input type="text" class="text form-control" size="5" value="{$product->minimum_order_quantity|default:1}" name="quantity">
                                    <button type="submit" class="add-to-cart-btn {button_style color=blue size=large}" rel="nofollow">
                                        {"Add to Cart"|gettext}
                                    </button>
                                {else}
                                    {if $user->isAdmin()}
                                        <input type="text" class="text form-control" size="5" value="{$product->minimum_order_quantity|default:1}" name="quantity">
                                        <button type="submit" class="add-to-cart-btn {button_style color=red size=large}" rel="nofollow">
                                            {"Add to Cart"|gettext}
                                        </button>
                                    {/if}
                                    <span class="error">{$product->availability_note}</span>
                                {/if}
                            {elseif $product->active_type == 1}
                                {if $user->isAdmin()}
                                    <input type="text" class="text form-control" size="5" value="{$product->minimum_order_quantity|default:1}" name="quantity">
                                    <button type="submit" class="add-to-cart-btn {button_style color=red size=large}" rel="nofollow">
                                        {"Add to Cart"|gettext}
                                    </button>
                                {/if}
                                <em class="unavailable">{"Product currently unavailable for purchase"|gettext}</em>
                            {/if}
                        </div>
                     {/form}
                     </div>
                 </div>
             {/if}

            {clear}
            {permissions}
                <div class="item-actions col-sm-12">
                    {if $permissions.create || $permissions.edit}
                        {icon class="add" action=edit parent_id=$product->id product_type='childProduct' text='Add Child Product'|gettext}
                    {/if}
                    {if $product->childProduct|@count >= 1 && $permissions.delete}
                        {icon class=delete action=deleteChildren record=$product text="Delete All Child Products"|gettext title="Delete `$product->title`'s Children" onclick="return confirm('Are you sure you want to delete ALL child products?  This is permanent.');"}
                    {/if}
                </div>
            {/permissions}
            {if $product->childProduct|@count >= 1}
                <div class="col-sm-12">
                {form id="child-products-form" controller=cart action=addItem}

                    {* NOTE display product options *}
                    {if $product->show_options}
                        {exp_include file="options.tpl"}
                        <div>
                            <strong>{'Total Cost of Options'|gettext}:</strong>
                            <span id="item-price">$0.00</span>
                        </div>
                    {/if}

                    <div id="child-products">
                        <table border="0" cellspacing="0" cellpadding="0" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>&#160;</th>
                                    <th><strong>{"QTY"|gettext}</strong></th>
                                    <th><strong>{"SKU"|gettext}</strong></th>
                                    {if !empty($product->extra_fields)}
                                        {foreach from=$product->extra_fields item=chiprodname}
                                            <th><span>{$chiprodname.name}</span></th>
                                        {/foreach}
                                    {/if}
                                    <th style="text-align: right;"><strong>{"PRICE"|gettext}</strong></th>
                                    <th>&#160;</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$product->childProduct item=chiprod}
                                    <tr class="{cycle values="odd,even"}">
                                        {*
                                            [0] => Always available even if out of stock.
                                            [1] => Available but shown as backordered if out of stock.
                                            [2] => Unavailable if out of stock.
                                            [3] => Show as &quot;Call for Price&quot;.
                                        *}
                                        {if  $chiprod->active_type == 0 && $product->active_type == 0 && ($chiprod->availability_type == 0 || $chiprod->availability_type == 1 || ($chiprod->availability_type == 2 && ($chiprod->quantity - $chiprod->minimum_order_quantity >= 0))) }
                                            <td>
                                                <input name="prod-check[]" type="checkbox" value="{$chiprod->id}">
                                            </td>
                                            <td>
                                                <input class="form-control" name="prod-quantity[{$chiprod->id}]" type="text" value="{$chiprod->minimum_order_quantity}" size=3 maxlength=5>
                                            </td>
                                        {elseif ($chiprod->availability_type == 2 && $chiprod->quantity <= 0) && $chiprod->active_type == 0}
                                            <td>

                                            </td>
                                            <td>
                                                <span><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">{"Out Of Stock"|gettext}</a></span>
                                            </td>
                                        {elseif $product->active_type != 0 || $chiprod->availability_type == 3 || $chiprod->active_type == 1 || $chiprod->active_type == 2}
                                            <td>

                                            </td>
                                             <td>
                                                 {'N/A'|gettext}
                                            </td>
                                        {/if}

                                        <td>
                                            <span>{$chiprod->model}</span>
                                        </td>
                                        {if !empty($chiprod->extra_fields)}
                                            {foreach from=$chiprod->extra_fields item=ef}
                                                <td>
                                                    <span>{$ef.value|stripslashes}</span>
                                                </td>
                                            {/foreach}
                                        {/if}
                                        <td style="text-align: right;">
                                            {if $chiprod->availability_type == 3 && $chiprod->active_type == 0}
                                                <strong><a href="javascript:void();" rel=nofollow title="{$chiprod->availability_note}">{'Call for Price'|gettext}</a></strong>
                                            {else}
                                                {if $chiprod->use_special_price}
                                                    <span style="color:red; font-size: 8px; font-weight: bold;">{'SALE!'|gettext}</span>{br}
                                                    <span style="color:red; font-weight: bold;">{$chiprod->special_price|currency}</span>
                                                {else}
                                                    <span>{$chiprod->base_price|currency}</span>
                                                {/if}
                                            {/if}
                                        </td>
                                        <td>
                                            {permissions}
                                                <div class="item-actions">
                                                    {if $permissions.edit || ($permissions.create && $chiprod->poster == $user->id)}
                                                        {icon img="edit.png" action=edit id=$chiprod->id title="Edit `$chiprod->title`"}
                                                        {icon img="copy.png" action=copyProduct title="Copy `$chiprod->title` " record=$chiprod}
                                                    {/if}
                                                    {if $permissions.delete || ($permissions.create && $chiprod->poster == $user->id)}
                                                        {icon img="delete.png" action=delete record=$chiprod title="Delete `$chiprod->title`" onclick="return confirm('"|cat:("Are you sure you want to delete this child product?"|gettext)|cat:"');"}
                                                    {/if}
                                                </div>
                                            {/permissions}
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>

                        {if $product->active_type == 0}
                            {*<a id="submit-chiprods" href="javascript:{ldelim}{rdelim}" class="add-to-cart-btn {button_style color=blue size=large} exp-ecom-link" rel="nofollow"><strong><em>{"Add selected items to cart"|gettext}</em></strong></a>*}
                            {control type="buttongroup" id="submit-chiprods" size=large color=green submit="Add selected items to cart"|gettext}
                        {/if}
                    </div>
                {/form}

                {script unique="children-submit" yui3mods="node"}
                {literal}
                YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
                    Y.one('#submit-chiprodsSubmit').on('click',function(e){
                        var frm = Y.one('#child-products');
                        var chcks = frm.all('input[type="checkbox"]');
                        var txts = frm.all('input[type="text"]');

                        bxchkd=0;
                        var msg = ""

                        chcks.each(function(bx,key){
                            if (bx.get('checked')) {
                                bxchkd++;
                                if (parseInt(txts.item(key).get('value'))<=0) {
                                    msg = "{/literal}{"You\'ll also need a value greater than 0 for a quantity."|gettext}{literal}"
                                }
                            };
                        });

                        if (bxchkd==0 || msg!="") {
                            alert('{/literal}{"You need to check at least 1 product before it can be added to your cart"|gettext}{literal}'+msg);
                            e.halt();
                        } else {
//                            Y.one('#child-products-form').submit();
                        };
                    });
                });
                {/literal}
                {/script}
                </div>
            {/if}
        {*</div>*}

        {if $product->crosssellItem|@count >= 1}
            <div class="col-sm-12">
                 <div class="products related-products">
                     <{$config.item_level|default:'h2'}>{"Related Items"|gettext}</{$config.item_level|default:'h2'}>

                     {counter assign="ipr" name="ipr" start=1}

                     {foreach name=listings from=$product->crosssellItem item=listing}

                         {if $smarty.foreach.listings.first || $open_row}
                             <div class="">
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
        {/if}
    </div>
    {if $config.enable_ratings_and_reviews}
        <div class="col-sm-12">
            {comments record=$product type='Review'|gettext title='Reviews'|gettext formtitle='Leave a review'|gettext ratings=1}
        </div>
    {/if}
</div>
