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

{*{css unique="ecom-search"}*}
    {*.yui3-aclist {*}
        {*z-index: 99!important;*}
    {*}*}
    {*#ac-input {*}
        {*width: 80%;*}
    {*}*}
{*{/css}*}

{*<div class="module ecommerce ecom-search yui3-skin-sam">*}
    {*<div id="search-autocomplete" class="control" style="z-index: 999;">*}
        {*{if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<label for="ac-input">{$moduletitle}</label>{/if}*}
        {*{control name="ac-input" type="search" class="text" prepend="search"}*}
    {*</div>*}
{*</div>*}

{capture assign="callbacks"}
{literal}
    // format the results coming back in from the query
    autocomplete.ac.set('resultFormatter', function(query, results) {
        return Y.Array.map(results, function (result) {
            var result = result.raw;

            var template;
            // image
            if (result.fileid) {
                template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+result.fileid+'&w=30&h=30&zc=1" />';
            } else {
                template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'framework/modules/ecommerce/assets/images/no-image.jpg" />';
            }
            // title
            template += ' <strong class="title">'+result.title+'</strong>';
            // model/SKU
            if (result.model) template += ' <em class="title">SKU: '+result.model+'</em>';
            //template += '<div style="clear:both;">';
            template += '</pre>';

            return template;
        });
    })

    // what should happen when the user selects an item?
    autocomplete.ac.on('select', function (e) {
        window.location = EXPONENT.PATH_RELATIVE+"store/show/title/"+e.result.raw.sef_url;  //FIXME requires SEF_URLs
        return e.result.raw.title;
    });
{/literal}
{/capture}

<div class="module ecommerce ecom-search">
    {control type="autocomplete" controller="store" action="search" name="search_item" label=$moduletitle placeholder="Search title or SKU to locate item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" maxresults=30 width="80%" jsinject=$callbacks}
</div>

{*{script unique="ecom-autocomplete" yui3mods="autocomplete,autocomplete-highlighters"}*}
{*{literal}*}
{*YUI(EXPONENT.YUI3_CONFIG).use('*', function (Y) {*}
    {*var formatResults = function (query, results) {*}
        {*return Y.Array.map(results, function (result) {*}
            {*var result = result.raw;*}

            {*var template;*}
            {*// image*}
            {*if (result.fileid) {*}
                {*template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'thumb.php?id='+result.fileid+'&w=30&h=30&zc=1" />';*}
            {*} else {*}
                {*template = '<pre><img width="30" height="30" class="srch-img" src="'+EXPONENT.PATH_RELATIVE+'framework/modules/ecommerce/assets/images/no-image.jpg" />';*}
            {*}*}
            {*// title*}
            {*template += ' <strong class="title">'+result.title+'</strong>';*}
            {*// model/SKU*}
            {*if (result.model) template += ' <em class="title">SKU: '+result.model+'</em>';*}
            {*//template += '<div style="clear:both;">';*}
            {*template += '</pre>';*}

            {*return template;*}
        {*});*}
    {*}*}
    {**}
    {*var autocomplete = Y.one('#ac-input');*}
    {*autocomplete.plug(Y.Plugin.AutoComplete, {*}
        {*width:"320px",*}
        {*maxResults: 10,*}
        {*resultListLocator: 'data',  // 'data' field of json response*}
        {*resultTextLocator: 'title', // the field to place in the input after selection*}
        {*resultFormatter: formatResults,*}
        {*source: EXPONENT.PATH_RELATIVE+'index.php?controller=store&action=search&json=1&ajax_action=1',*}
        {*requestTemplate: '&query={query}'*}
    {*});*}

    {*// display 'loading' icon*}
    {*autocomplete.ac.on('query', function (e) {*}
        {*Y.one('#ac-inputControl span i').removeClass('{/literal}{expTheme::iconStyle('search')}{literal}').addClass('{/literal}{expTheme::iconStyle('ajax')}{literal}');*}
    {*});*}

    {*// display regular icon*}
    {*autocomplete.ac.on('results', function (e) {*}
        {*Y.one('#ac-inputControl span i').removeClass('{/literal}{expTheme::iconStyle('ajax')}{literal}').addClass('{/literal}{expTheme::iconStyle('search')}{literal}');*}
    {*});*}

    {*// action when item selected*}
    {*autocomplete.ac.on('select', function (e) {*}
        {*window.location = EXPONENT.PATH_RELATIVE+"store/show/title/"+e.result.raw.sef_url;  //FIXME requires SEF_URLs*}
        {*return e.result.raw.title;*}
    {*});*}
{*});*}
{*{/literal}*}
{*{/script}*}
