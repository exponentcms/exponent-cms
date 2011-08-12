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
<div class="navigationmodule add-whichtype">
<div class="form_header">
        <h1>{'Add New Page to Site Navigation'|gettext}</h1>
        <p>
		{if $parent->id == 0}{'You are adding a new top-level page.'|gettext}{else}{'You are adding a new sub page to "%s".'|gettext|sprintf:$parent->name}{/if}
		{'Please select the type of page you would like to add.'|gettext}
	</p>
</div>
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_contentpage parent=$parent->id}">{'Content Page'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{'Content Pages are regular pages on the site that allow you to add modules to them.  With content pages, you are able to override the global Site Title, Site Description and Site Keywords settings.'|gettext}</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_externalalias parent=$parent->id}">{'External Website Link'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{'If you need or want a link in your site hiearchy to link to some off-site webpage, create an External Link.'|gettext}</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_internalalias parent=$parent->id}">{'Internal Page Alias'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{'If you need or want a link to another page in your site hierarchy, use an internal page alias.'|gettext}
</div>

{if $havePagesets != 0}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=add_pagesetpage parent=$parent->id}">{'Pageset'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{'Pagesets are powerful tools that allow you to create sections with default content and subsections by adding a single pageset.'|gettext}</div>
{/if}

{if $haveStandalone != 0}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=move_standalone parent=$parent->id}">{'Move Standalone Page'|gettext}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{'Use this if you want to move a standalone page into the navigation hierarchy.'|gettext}</div>
{/if}
</div>
