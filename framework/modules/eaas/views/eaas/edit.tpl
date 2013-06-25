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

<div id="editblog" class="module blog edit">
    {if $record->id != ""}<h1>{'Editing'|gettext} {$record->title}</h1>{else}<h1>{'New'|gettext} {$modelname}</h1>{/if}
    {form action=update}
        {control type=hidden name=id value=$record->id}
        <div id="editblog-tabs" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
                <li class="selected"><a href="#tab1"><em>{'General'|gettext}</em></a></li>
                <li><a href="#tab2"><em>{'Publish'|gettext}</em></a></li>
                {if $config.filedisplay}
                    <li><a href="#tab3"><em>{'Files'|gettext}</em></a></li>
                {/if}
                <li><a href="#tab4"><em>{'SEO'|gettext}</em></a></li>
            </ul>
            <div class="yui-content yui3-skin-sam">
                <div id="tab1">
                    <h2>{'Blog Entry'|gettext}</h2>
                    {control type=text name=title label="Title"|gettext value=$record->title}
                    {control type=html name=body label="Body Content"|gettext value=$record->body}
                    {control type="checkbox" name="private" label="Save as draft/private"|gettext value=1 checked=$record->private}
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
                </div>
                <div id="tab2">
                    {control type="yuidatetimecontrol" name="publish" label="Publish Date"|gettext edit_text="Publish Immediately" value=$record->publish}
                </div>
                {if $config.filedisplay}
                    <div id="tab3">
                        {control type="files" name="files" label="Files"|gettext value=$record->expFile}
                    </div>
                {/if}
                <div id="tab4">
                    <h2>{'SEO Settings'|gettext}</h2>
                    {control type="text" name="sef_url" label="SEF URL"|gettext value=$record->sef_url}
                    {control type="text" name="canonical" label="Canonical URL"|gettext value=$record->canonical}
                    {control type="text" name="meta_title" label="Meta Title"|gettext value=$record->meta_title}
                    {control type="textarea" name="meta_description" label="Meta Description"|gettext rows=5 cols=35 value=$record->meta_description}
                    {control type="textarea" name="meta_keywords" label="Meta Keywords"|gettext rows=5 cols=35 value=$record->meta_keywords}
                </div>
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Blog Item"|gettext}</div>
        {control type=buttongroup submit="Save Text"|gettext cancel="Cancel"|gettext}
    {/form}   
</div>

{script unique="blogtabs" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('autocomplete','autocomplete-filters','autocomplete-highlighters','tabview', function(Y) {
	    var tabview = new Y.TabView({srcNode:'#editblog-tabs'});
	    tabview.render();
		Y.one('#editblog-tabs').removeClass('hide');
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
