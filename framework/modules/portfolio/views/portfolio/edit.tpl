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

<div id="editportfolio" class="module blog edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
        <div id="editportfolio-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li><a href="#tab2"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li><a href="#tab3"><em>{'SEO'|gettext}</em></a></li>
            </ul>            
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'Portfolio Piece'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title}
                    {control type=html name=body label="Description"|gettext value=$record->body}
                    {control type="checkbox" name="featured" label="Feature this Portfolio Piece"|gettext|cat:"?" checked=$record->featured value=1}
                    {if !$config.disabletags}
                        {foreach from=$record->expTag item=tag name=tags}
                            {if $smarty.foreach.tags.first == false}
                                {assign var=tags value="`$tags`,`$tag->title`"}
                            {else}
                                {assign var=tags value=$tag->title}
                            {/if}
                        {/foreach}
                        {if $tags != ""}{$tags=$tags|cat:','}{/if}
                        {control type="text" id="expTag" name="expTag" label="Tags (comma separated)"|gettext value=$tags size=45}
                    {/if}
                    {if $config.usecategories}
                        {control type="dropdown" name=expCat label="Category"|gettext frommodel="expCat" where="module='`$modelname`'" orderby="rank" display=title key=id includeblank="Not Categorized"|gettext value=$record->expCat[0]->id}
                    {/if}
                </div>
                {if $config.filedisplay}
                    <div id="tab2">
                        {control type="files" name="files" label="Files"|gettext value=$record->expFile}
                    </div>
                {/if}
                <div id="tab3">
                     <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{'Loading Portfolio Item'|gettext}</div>
        {control type=buttongroup submit="Save Portfolio Piece"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="editform" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_PATH+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('autocomplete','autocomplete-filters','autocomplete-highlighters','exptabs', function(Y) {
//	    var tabview = new Y.TabView({srcNode:'#editportfolio-tabs'});
//	    tabview.render();
        Y.expTabs({srcNode: '#editportfolio-tabs'});
		Y.one('#editportfolio-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();

		var inputNode = Y.one('#expTag');
		var tags = [{/literal}{$taglist}{literal}];

		inputNode.plug(Y.Plugin.AutoComplete, {
		  activateFirstItem: true,
		  allowTrailingDelimiter: true,
		  minQueryLength: 0,
		  queryDelay: 0,
		  queryDelimiter: ',',
		  source: tags,
          resultFilters    : 'phraseMatch',
          resultHighlighter: 'phraseMatch',

		  // Chain together a phraseMatch filter followed by a custom result filter
		  // that only displays tags that haven't already been selected.
		  resultFilters: ['phraseMatch', function (query, results) {
		    // Split the current input value into an array based on comma delimiters.
		    var selected = inputNode.ac.get('value').split(/\s*,\s*/);

		    // Pop the last item off the array, since it represents the current query
		    // and we don't want to filter it out.
		    selected.pop();

		    // Convert the array into a hash for faster lookups.
		    selected = Y.Array.hash(selected);

		    // Filter out any results that are already selected, then return the
		    // array of filtered results.
		    return Y.Array.filter(results, function (result) {
		      return !selected.hasOwnProperty(result.text);
		    });
		  }]
		});

		// When the input node receives focus, send an empty query to display the full
		// list of tag suggestions.
			inputNode.on('focus', function () {
			inputNode.ac.sendRequest('');
		});

		// After a tag is selected, send an empty query to update the list of tags.
		inputNode.ac.after('select', function () {
			inputNode.ac.sendRequest('');
			inputNode.ac.show();
		});

    });
{/literal}
{/script}
