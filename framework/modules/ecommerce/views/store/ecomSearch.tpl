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
    {form id="autocompsearch" controller=search action=search}
        {control type="autocomplete" controller="store" action="search" name="search_string" label=$moduletitle placeholder="Search title or SKU to locate item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" maxresults=30 width="80%" jsinject=$callbacks}
    {/form}
</div>
