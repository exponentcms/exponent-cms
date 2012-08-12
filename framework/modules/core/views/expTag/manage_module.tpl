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

<div class="module expTags manage yui-content yui3-skin-sam">
    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help"|gettext|cat:" "|cat:("Managing Tags"|gettext) module="manage-tags"}
        </div>
        <h1>{"Manage Module Tags"|gettext}</h1>
    </div>
	{permissions}
    	{if $permissions.create == 1}
    		{*<a class="add" href="{link controller=$model_name action=create}">{"Create a new Tag"|gettext}</a>*}
    	{/if}
    {/permissions}
    {$page->links}
    {form action=change_tags}
    {control type=hidden name=mod value=$page->model}
    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                    <input type='checkbox' name='checkallp' title="{'Select All/None'|gettext}" onChange="selectAllp(this.checked)">
                </th>
                <th>
                    {"Item"|gettext}
                </th>
                <th>
                    {"Tags"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$page->records item=record}
                <tr class="{cycle values="odd,even"}">
                    <td>
                        {control type="checkbox" name="change_tag[]" label=" " value=$record->id}
                    </td>
                    <td>
                        {$record->title|truncate:50:"..."}
                    </td>
                    <td>
                        {foreach from=$record->expTag item=tag name=tags}
                            {$tag->title},
                        {/foreach}
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    {$page->links}
    <p>{'Select the item(s) to change, then enter the tags below'|gettext}</p>
    {control type="text" id="addTag" name="addTag" label="Add these Tags (comma separated)"|gettext size=45 value=''}
    {control type="text" id="removeTag" name="removeTag" label="Remove these Tags (comma separated)"|gettext size=45 value=''}
    {control type=buttongroup submit="Change Tags on Selected Items"|gettext cancel="Cancel"|gettext returntype="viewable"}
    {/form}
</div>

{script unique="edittags" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('autocomplete','autocomplete-filters','autocomplete-highlighters', function(Y) {
		var inputNode = Y.one('#addTag');
        var inputNode2 = Y.one('#removeTag');
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

        inputNode2.plug(Y.Plugin.AutoComplete, {
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
            var selected = inputNode2.ac.get('value').split(/\s*,\s*/);

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
            inputNode2.on('focus', function () {
            inputNode2.ac.sendRequest('');
        });

        // After a tag is selected, send an empty query to update the list of tags.
        inputNode2.ac.after('select', function () {
            inputNode2.ac.sendRequest('');
            inputNode2.ac.show();
        });
    });
{/literal}
    function selectAllp(val) {
        var checks = document.getElementsByName("change_tag[]");
        for (var i = 0; i < checks.length; i++) {
          checks[i].checked = val;
        }
    }
{/script}
