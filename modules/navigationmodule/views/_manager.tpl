{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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

<div id="navmanager" class="navigationmodule manager hide exp-skin-tabview">
	<div id="nav-tabs" class="yui-navset">
	    <ul class="yui-nav">
        	<li class="selected"><a href="#tab1"><em>Hierarchy</em></a></li>
	        {if $canManageStandalones}<li><a href="#tab2"><em>Standalone</em></a></li>{/if}
        	{*if $canManagePagesets}<li><a href="#tab3"><em>Page Sets</em></a></li>{/if*}
	    </ul>            
	    <div class="yui-content">
        	<div id="tab1">{include file="`$smarty.const.BASE`modules/navigationmodule/views/_manager_hierarchy.tpl"}</div>
	        {if $canManageStandalones}<div id="tab2">{chain module=navigationmodule action=manage_standalone}</div>{/if}
        	{*if $canManagePagesets}<div id="tab3">{chain module=navigationmodule action=manage_pagesets}</div>{/if*}
	    </div>
	</div>
</div>
<div class="loadingdiv">Loading</div>

{script unique="managenavtabs" yuimodules="tabview"}
{literal}
    var tabView = new YAHOO.widget.TabView('nav-tabs');
    YAHOO.util.Dom.removeClass("navmanager", 'hide');
    var loading = YAHOO.util.Dom.getElementsByClassName('loadingdiv', 'div');
    YAHOO.util.Dom.setStyle(loading, 'display', 'none');
{/literal}
{/script}
