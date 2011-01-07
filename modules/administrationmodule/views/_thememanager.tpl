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

<div class="administrationmodule thememanager">
	<div class="form_header">
		<h1>{$_TR.form_title}</h1>
		<p>{$_TR.form_header}</p>
		<a class="mngmntlink administration_mngmntlink" href="{link action=upload_extension}">{$_TR.new_theme}</a>.
	</div>

	{foreach name=t from=$themes key=class item=theme}
	<div class="item {cycle values="odd,even"}">
		<img class="previewimage" src="{$smarty.const.URL_FULL}thumb.php?file={$theme->preview}&constraint=1&width=100&height=100" {$smarty.const.XHTML_CLOSING}>

		<div class="themeinfo">
			<h2>
			{$theme->name}
			{if $smarty.const.DISPLAY_THEME_REAL == $class}<span class="current">{$_TR.current}</span>{/if}
			{if $smarty.const.DISPLAY_THEME == $class and $smarty.const.DISPLAY_THEME != $smarty.const.DISPLAY_THEME_REAL}
				<em class="previewing"> {$_TR.previewing} </em>
			{/if}
			</h2>
			
			<h3 class="author">{$_TR.by} {$theme->author}</h3>

			<p>
				{$theme->description}		
			</p>

			{if $class != $smarty.const.DISPLAY_THEME}
				<a class="previewtheme" href="{link action=theme_preview theme=$class}">{$_TR.preview}</a>
			{/if}
			{if $smarty.const.DISPLAY_THEME_REAL != $class}
				<a class="switchtheme" href="{link action=switchtheme theme=$class}">{$_TR.switch_theme}</a>
			{/if}
		</div>

	</div>
	{/foreach}
</div>
