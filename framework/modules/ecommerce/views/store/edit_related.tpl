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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[related]" value=1}
    {if count($record->childProduct)}
        <h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
    <h2>{'Related Products'|gettext}</h2>
    {capture assign="callbacks"}
    {literal}
    // the text box for the title
    var tagInput = Y.one('#related_items_autoc');

    // the UL to append to
    var tagUL = Y.one('#relatedItemsList');

    var clearEntry = function(e) {
        e.target.set('value', '');
    }

    var removeLI = function(e) {
        e.target.get('parentNode').remove();
    }

    var createHTML = function(val) {
        var li = '<li>'+val.title+' - <a class="delete" href="javascript:{}" title="{/literal}{'Remove Related Item'|gettext}">{'Remove'|gettext}{literal}</a><br />';
            li += '{/literal}{"Model"|gettext}{literal} #: '+val.model+'';
            li += '<br /><input type="checkbox" name="relateBothWays['+val.id+']" value="'+val.id+'"> {/literal}{"Also relate this related product to this one on save"|gettext}{literal}';
            li += '<input type=hidden name="relatedProducts['+val.id+']" value="'+val.id+'" /></li>';
        var newLI = Y.Node.create(li);
        return newLI;
    }

    tagInput.on('click',clearEntry);
    tagUL.on('click',removeLI);

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
        tagUL.appendChild(createHTML(e.result.raw));
        return true;
    });

    {/literal}
    {/capture}

    {control type="autocomplete" controller="store" action="search" name="related_items" label="Related Products"|gettext placeholder="Search Title or SKU"|gettext schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}
    {*{br}*}
    <ul id="relatedItemsList">
        {foreach from=$record->crosssellItem item=prod name=prods}
            <li>
                <strong>{$prod->title|strip_tags}</strong> - <a class="delete" href="javascript:{ldelim}{rdelim}" title="{'Delete'|gettext}">{'Delete'|gettext}</a>{br}
                {'Model'|gettext} #: {$prod->model|strip_tags}
                <input type=hidden name="relatedProducts[{$prod->id}]" value="{$prod->id}" />
            </li>
        {/foreach}
    </ul>
{else}
	<h4><em>({'Related Products'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}
