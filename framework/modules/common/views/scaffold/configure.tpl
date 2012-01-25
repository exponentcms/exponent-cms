{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div id="config" class="module scaffold configure exp-skin-tabview">
	{form action=saveconfig}
		<div id="config-tabs" class="yui-navset hide">
			<ul class="yui-nav">
			    {foreach from=$views item=tab name=tabs}
			        <li{if $smarty.foreach.tabs.first} class="selected"{/if}>
			            <a href="#tab{$smarty.foreach.tabs.iteration}"><em>{$tab.name}</em></a>
			        </li>
			    {/foreach}
			</ul>            
            <div class="yui-content">
                {foreach from=$views item=body name=body}
                    <div id="tab{$smarty.foreach.body.iteration}">
                        {include file=$body.file}
                    </div>
                {/foreach}
			</div>
		</div>
		<div class="loadingdiv">{"Loading Settings"|gettext}</div>
		{control type=buttongroup submit="Save Config"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

{script unique="conf" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('history','tabview', function(Y) {
		var history = new Y.HistoryHash(),
	        tabview = new Y.TabView({srcNode:'#config-tabs'});
	    tabview.render();
	    Y.one('#config-tabs').removeClass('hide');
	    Y.one('.loadingdiv').remove();

		// Set the selected tab to the bookmarked history state, or to
		// the first tab if there's no bookmarked state.
var test= history.get('tab');
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