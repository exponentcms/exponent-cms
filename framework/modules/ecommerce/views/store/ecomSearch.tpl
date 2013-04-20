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

{*{css unique="autocomplete-extras" link="`$asset_path`css/ecom_search.css"}*}

{*{/css}*}

<div class="module ecommerce ecom-search yui3-skin-sam yui-skin-sam">
    <div id="search-autocomplete" class="control">
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<label class="label" for="ac-input">{$moduletitle}</label>{/if}
        {*<input id="ac-input" type="text" class="text">*}
        {control name="ac-input" type="search" class="text" prepend="search"}
    </div>
</div>

{script unique="ecom-autocomplete" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use("datasource-io","datasource-jsonschema","autocomplete", "autocomplete-highlighters", "datasource-get", function (Y) {
    
    var formatResults = function (query, results) {
        return Y.Array.map(results, function (result) {
            var result = result.raw;

            var template = (result.fileid != '') ? '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+result.fileid+'&w=30&h=30&zc=1" />' : '<pre>';
            // title
            template += ' <strong class="title">'+result.title+'</strong>';
            // model/SKU
            template += ' <em class="title">SKU: '+result.model+'</em></div>';
//            template += '<div style="clear:both;"></pre>';

            return template;
        });
     }
    
    var autocomplete = Y.one('#ac-input');
    
    autocomplete.plug(Y.Plugin.AutoComplete, {
        width:"250px",
        maxResults: 10,
        resultListLocator: 'data',
        resultTextLocator: 'title', // the field to place in the input after selection
        resultFormatter: formatResults,
        source: EXPONENT.PATH_RELATIVE+'index.php?controller=store&action=search&json=1&ajax_action=1',
        requestTemplate: '&query={query}'
    });
    
    autocomplete.ac.on('select', function (e) {
        window.location = EXPONENT.PATH_RELATIVE+"store/show/title/"+e.result.raw.sef_url;
        return e.result.raw.title;
    });
    
});

{/literal}
{/script}
