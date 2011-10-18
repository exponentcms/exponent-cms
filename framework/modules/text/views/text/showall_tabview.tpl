{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
 
{uniqueid assign="id"}

<div class="module text showall-tabview yui3-skin-sam">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.create == 1}
                {icon class=add action=edit rank=1 title="Add Tab" text="Add Tab"}
            {/if}
            {if $permissions.manage == 1}
                {ddrerank items=$items model="text" label="Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    <div id="{$id}" class="hide">
        <ul>
            {foreach from=$items item=tab name=tabs}
                <li><a href="#tab{$smarty.foreach.items.iteration}">{$tab->title}</a></li>
            {/foreach}
        </ul>
        <div>
            {foreach from=$items item=text name=items}
                <div id="tab{$smarty.foreach.items.iteration}">
                    {permissions}
						<div class="item-actions">
						   {if $permissions.edit == 1}
								{icon action=edit class="edit" record=$text title="Edit this `$modelname`"}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$text title="Delete this Text Item" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
							{/if}
						</div>
                    {/permissions}
                    <div class="bodycopy">
                        {filedisplayer view="`$config.filedisplay`" files=$text->expFile id=$text->id}
                        {$text->body}
                    </div>
					{permissions}
						<div class="module-actions">
							{if $permissions.create == 1}
								{icon class=add action=edit rank=$text->rank+1 title="Add tab" text="Add another tab after this one"}
							{/if}
						</div>
					{/permissions}
                </div>
            {/foreach}
        </div>
    </div>
</div>
<div class="loadingdiv">Loading</div>

{script unique="`$id`" yui3mods="1"}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('history','tabview', function(Y) {
		var history = new Y.HistoryHash(),
	        tabview = new Y.TabView({srcNode:'#{/literal}{$id}{literal}'});
	    tabview.render();
		Y.one('#{/literal}{$id}{literal}').removeClass('hide');
		Y.one('.loadingdiv').remove();

		// Set the selected tab to the bookmarked history state, or to
		// the first tab if there's no bookmarked state.
		tabview.selectChild(history.get('tab') || 0);

		// Store a new history state when the user selects a tab.
		tabview.after('selectionChange', function (e) {
		  // If the new tab index is greater than 0, set the "tab"
		  // state value to the index. Otherwise, remove the "tab"
		  // state value by setting it to null (this reverts to the
		  // default state of selecting the first tab).
		  history.addValue('tab', e.newVal.get('index') || null);
		});

		// Listen for history changes from back/forward navigation or
		// URL changes, and update the tab selection when necessary.
		Y.on('history:change', function (e) {
		  // Ignore changes we make ourselves, since we don't need
		  // to update the selection state for those. We're only
		  // interested in outside changes, such as the ones generated
		  // when the user clicks the browser's back or forward buttons.
		  if (e.src === Y.HistoryHash.SRC_HASH) {

		    if (e.changed.tab) {
		      // The new state contains a different tab selection, so
		      // change the selected tab.
		      tabview.selectChild(e.changed.tab.newVal);
		    } else if (e.removed.tab) {
		      // The tab selection was removed in the new state, so
		      // select the first tab by default.
		      tabview.selectChild(0);
		    }

		  }
		});
	});
{/literal}
{/script}
