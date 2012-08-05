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

<div class="module faq edit yui3-skin-sam">
    <h1>{if $record->id}{'Edit'|gettext} {$record->question}{else}{'Create new FAQ'|gettext}{/if}</h1>

    {form action="update"}
        {control type="hidden" name="id" value=$record->id}
        {control type="text" name="submitter_name" label="Name of submitter"|gettext value=$record->submitter_name|default:$user->username}
        {control type="text" name="submitter_email" label="Submitter's Email"|gettext value=$record->submitter_email|default:$user->email}
        {control type="checkbox" name="send_email" label="Send email to user"|gettext|cat:"?" value=1}
        {control type="textarea" name="question" label="Question"|gettext value=$record->question}
        {control type="html" name="answer" label="Answer"|gettext value=$record->answer}
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
        {control type="checkbox" name="include_in_faq" label="Post to FAQs"|gettext|cat:"?" value=1 checked=$record->include_in_faq}
        {control type="buttongroup" submit="Save FAQ"|gettext cancel="Cancel"|gettext}
    {/form} 
</div>

{script unique="blogtabs" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('autocomplete','autocomplete-filters','autocomplete-highlighters', function(Y) {
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
