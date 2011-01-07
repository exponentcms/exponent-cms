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
<div class="navigationmodule add-whichtype">
<div class="form_header">
        <h1>{$_TR.form_title}</h1>
        <p>
		{if $parent->id == 0}{$_TR.new_top_level}{else}{$_TR.new_sub_level|sprintf:$parent->name}{/if}
		{$_TR.form_header}
	</p>
</div>
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_contentpage parent=$parent->id}">{$_TR.content_page}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{$_TR.content_page_desc}</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_externalalias parent=$parent->id}">{$_TR.ext_link}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{$_TR.ext_link_desc}</div>

<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=edit_internalalias parent=$parent->id}">{$_TR.int_link}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{$_TR.int_link_desc}
</div>

{if $havePagesets != 0}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=add_pagesetpage parent=$parent->id}">{$_TR.pageset}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{$_TR.pageset_desc}</div>
{/if}

{if $haveStandalone != 0}
<div style="background-color: #CCC; padding: 5px;">
<a class="mngmntlink navigation_mngmntlink" href="{link action=move_standalone parent=$parent->id}">{$_TR.standalone}</a>
</div>
<div style="padding: .5em; padding-bottom: 1.5em;">{$_TR.standalone_desc}</div>
{/if}
</div>
