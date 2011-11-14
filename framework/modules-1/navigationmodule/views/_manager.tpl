{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div id="navmanager" class="navigationmodule manager">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help"|gettext|cat:" "|cat:("Managing Pages"|gettext) module="manage-all-pages"}
			</div>
			<h1>{'Manage Pages'|gettext}</h1>
		</div>
	</div>
	<div id="navmanager-tabs" class="yui-navset yui3-skin-sam hide">
	    <ul class="yui-nav">
        	<li class="selected"><a href="#tab1"><em>{'Hierarchy'|gettext}</em></a></li>
	        {if $canManageStandalones}<li><a href="#tab2"><em>{'Standalone'|gettext}</em></a></li>{/if}
        	{*if $canManagePagesets}<li><a href="#tab3"><em>Page Sets</em></a></li>{/if*}
	    </ul>            
	    <div class="yui-content">
        	<div id="tab1">{include file="`$smarty.const.BASE`framework/modules-1/navigationmodule/views/_manager_hierarchy.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2">{chain module=navigationmodule action=manage_standalone}</div>{/if}
        	{*if $canManagePagesets}<div id="tab3">{chain module=navigationmodule action=manage_pagesets}</div>{/if*}
	    </div>
	</div>
	<div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{script unique="editform" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('history','tabview', function(Y) {
		var history = new Y.HistoryHash(),
	        tabview = new Y.TabView({srcNode:'#navmanager-tabs'});
	    tabview.render();
	    Y.one('#navmanager-tabs').removeClass('hide');
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

