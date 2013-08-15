{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{css unique="product-edit" link="`$asset_path`css/product_edit.css" corecss="tree,panels"}

{/css}

<script src="{$smarty.const.PATH_RELATIVE}external/editors/ckeditor/ckeditor.js"></script>

<div id="editproduct" class="module store edit yui-skin-sam exp-skin exp-admin-skin">
    {if $record->id != ""}
        <h1>{'Edit Information for'|gettext}{if $record->childProduct|@count != 0} {'Parent'|gettext}{/if}{if $record->parent_id != 0} {'Child'|gettext}{/if} {$modelname|ucfirst}</h1>
    {else}
        <h1>{'New'|gettext} {$modelname}</h1>
    {/if}

    {form action=update}
        {control type="hidden" name="id" value=$record->id}
		<!-- if it copied -->
		{if $record->original_id}
		{control type="hidden" name="original_id" value=$record->original_id}
		{/if}
        <div id="editproduct-tabs" class="yui-navset exp-skin-tabview hide">
            <ul id="dynamicload" class="exp-ajax-tabs yui-nav">
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_general"}">{'General'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_pricing"}">{'Pricing, Tax'|gettext} &amp; {'Discounts'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_images"}">{'Images'|gettext} &amp; {'Files'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_quantity"}">{'Quantity'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_shipping"}">{'Shipping'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_categories"}">{'Categories'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_options"}">{'Options'|gettext}</a></li>
                {if $record->parent_id == 0}
                    <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id view="edit_featured"}">{'Featured'|gettext}</a></li>
                    <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id view="edit_related"}">{'Related Products'|gettext}</a></li>
                {/if}
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_userinput"}">{'User Input'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_status"}">{'Active'|gettext} &amp; {'Status Settings'|gettext}</a></li>
                {if $record->parent_id == 0}
                    <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id view="edit_meta"}">{'SEO'|gettext}</a></li>
                {/if}
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_notes"}">{'Notes'|gettext}</a></li>
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_extrafields"}">{'Extra Fields'|gettext}</a></li>
                {if $record->parent_id == 0}
                    <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id view="edit_model"}">{'SKUS/Model'|gettext}</a></li>
                {/if}
                <li><a href="{link action="edit" product_type="product" ajax_action=1 id=$record->id parent_id = $record->parent_id view="edit_misc"}">{'Misc'|gettext}</a></li>
            </ul>
            <div id="loadcontent" class="exp-ajax-tabs-content yui-content yui3-skin-sam"></div>
        </div>
        <div id="loading" class="loadingdiv">{"Loading"|gettext} {"Product Edit Form"|gettext}</div>
        {control type="buttongroup" submit="Save Product"|gettext cancel="Cancel"|gettext}
        {if isset($record->original_id)}
            {control type="hidden" name="original_id" value=$record->original_id}
            {control type="hidden" name="original_model" value=$record->original_model}
            {control type="checkbox" name="copy_children" label="Copy Child Products?"|gettext value="1"}
            {control type="checkbox" name="copy_related" label="Copy Related Products?"|gettext value="1"}
            {control type="checkbox" name="adjust_child_price" label="Reset Price on Child Products?"|gettext value="1"}
            {control type="text" name="new_child_price" label="New Child Price"|gettext value=""}
        {/if}
    {/form}
</div>

{script unique="prodtabs" yui3mods="1"}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

    YUI(EXPONENT.YUI3_CONFIG).use("get", "exptabs",'tabview',"node-load","event-simulate",'cookie', function(Y) {
       
//       var lastTab = !Y.Lang.isNull(Y.Cookie.get("edit-tab")) ? Y.Cookie.get("edit-tab") : 0;
       var tabs = Y.all('#dynamicload li a');
       var cdiv = Y.one('#loadcontent');

       tabs.each(function(n,k){
           cdiv.append('<div id="exptab-'+k+'" class="exp-ajax-tab"></div>');
       });

       var cTabs = cdiv.all('.exp-ajax-tab');
       
       var loadTab = function (e){
           e.halt();
           var tab = e.currentTarget;
           var tIndex = tabs.indexOf(tab);
           var cTab = cTabs.item(tIndex);
           var puri =  tab.getAttribute('href');

//           Y.Cookie.set("edit-tab", tIndex);
           
           tabs.removeClass('current');
           tab.addClass('current');
           cTabs.hide();
           if (!cTab.hasChildNodes()) {
               cTab.load(puri,parseScripts);
           };
           cTab.show();
       }
       
       var parseScripts = function (id,o){
           this.all('script').each(function(n){
               if(!n.get('src')){
                   eval(n.get('innerHTML'));
               } else {
                   var url = n.get('src');
                   if (url.indexOf("ckeditor")) {
                       Y.Get.script(url);
                   };
               };
           });
           // css
           //Y.log(tab.all('.io-execute-response link'));
           this.all('link').each(function(n){
               var url = n.get('href');
               Y.Get.css(url);
           });
       }
       
       tabs.on('click',loadTab);

       // load all the tabs if we are copying in order to save all the data
       if ({/literal}{if $copy}1{else}0{/if}{literal}) {
           tabs.each(function(n,k){
               n.simulate('click');
           }
       });
//       tabs.item(lastTab).simulate('click');
       tabs.item(0).simulate('click');

       Y.one('#editproduct-tabs').removeClass('hide');
       Y.one('.loadingdiv').remove();
    });
{/literal}
{/script}
