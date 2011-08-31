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

<div class="administrationmodule thememanager">
	<div class="form_header">
		<h1>{'Manage Themes'|gettext}</h1>
		<p>{'This page lists all installed themes that are recognized by Exponent.  When you click the "Preview" link, the site layout will be switched to that theme for the duration of your session.  Other uses will still see the configured theme.  If you log out or close your browser window, the previewing will stop.'|gettext}</p>
		<a class="mngmntlink administration_mngmntlink" href="{link action=upload_extension}">{'Upload New Theme'|gettext}</a>.
	</div>

	{foreach name=t from=$themes key=class item=theme}
	<div class="item {cycle values="odd,even"}">
	    {img class="themepreview" src=$theme->preview w=100}

		<div class="themeinfo">
			<h2>
			{$theme->name}
			{if $smarty.const.DISPLAY_THEME_REAL == $class}<span class="current">{'Active Theme'|gettext}</span>{/if}
			{if $smarty.const.DISPLAY_THEME == $class and $smarty.const.DISPLAY_THEME != $smarty.const.DISPLAY_THEME_REAL}
				<em class="previewing"> {'Previewing'|gettext} </em>
			{/if}
			</h2>
			
			<h3 class="author">{'by'|gettext} {$theme->author}</h3>

			<p>
				{$theme->description}		
			</p>

			{if $class != $smarty.const.DISPLAY_THEME}
				<a class="previewtheme" href="{link action=theme_preview theme=$class}">{'Preview Theme'|gettext}</a>
			{/if}
			{if $smarty.const.DISPLAY_THEME_REAL != $class}
				<a class="switchtheme" href="{link action=switchtheme theme=$class}">{'Use This Theme'|gettext}</a>
			{/if}
		</div>

	</div>
	{/foreach}
</div>
