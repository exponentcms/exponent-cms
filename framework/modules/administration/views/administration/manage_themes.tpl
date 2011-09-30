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
 
 
{css unique="themes" corecss="tables" link="`$asset_path`css/managethemes.css"}

{/css}

<div class="module administration manage-themes">

    <div class="info-header">
        <div class="related-actions">
            {help text="Get Help Managing Themes" module="manage-themes"}
        </div>
		<h1>{"Theme Manager"|gettext}</h1>
    </div>

    <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
        <thead>
            <tr>
                <th>
                </th>
                <th>
                {"Description"|gettext}
                </th>
                <th>
                {"Actions"|gettext}
                </th>
            </tr>
        </thead>
        <tbody>
        	{foreach name=t from=$themes key=class item=theme}
				<tr class="{cycle values='odd,even'}">
					<td>
						{img class="themepreview" src=$theme->preview w=100}
					</td>
					<td>
						<h2>
							{$theme->name}
						</h2>
						<p>
							{$theme->description}
							{if $theme->mobile}{br}<em>(mobile ready theme)</em>{/if}
						</p>
					</td>
					<td class="actions">
						{if $theme->style_variations|@count>0}
							<h6>{"Style Variations"|gettext}</h6>
							{foreach from=$theme->style_variations item=sv key=svkey name=styles}
								{if $smarty.const.DISPLAY_THEME_REAL == $class && ($smarty.const.THEME_STYLE_REAL == $sv || ($smarty.const.THEME_STYLE_REAL == "" && $sv == "Default"))}
									<span class="switchtheme current">{$sv} ({"Current"|gettext})</span>
								{else}
									<a class="switchtheme add" href="{link action=switch_themes theme=$class sv=$sv}" title='Select this Style'>{$sv}</a>
								{/if}
								{if $smarty.const.DISPLAY_THEME == $class && $smarty.const.DISPLAY_THEME == $smarty.const.DISPLAY_THEME_REAL && $smarty.const.THEME_STYLE == $smarty.const.THEME_STYLE_REAL &&
									($smarty.const.THEME_STYLE == $sv || ($smarty.const.THEME_STYLE == "" && $sv == "Default"))}
								{elseif $smarty.const.DISPLAY_THEME == $class && ($smarty.const.THEME_STYLE == $sv || ($smarty.const.THEME_STYLE == "" && $sv == "Default"))}
									(<em>{"Previewing"|gettext}</em>)
								{else}
									{icon img="view.png" action=preview_theme theme=$class sv=$sv title="Preview this Theme"}
								{/if}
								{br}
							{/foreach}
						{else}
							{if $smarty.const.DISPLAY_THEME_REAL != $class}
								<a class="switchtheme add" href="{link action=switch_themes theme=$class}" title='Select this Theme'>{"Use"|gettext}</a>
							{else}
								<span class="switchtheme current">({"Current"|gettext})</span>
							{/if}
							{if $smarty.const.DISPLAY_THEME != $class}
								{icon img="view.png" action=preview_theme theme=$class title="Preview this Theme"}
							{elseif $smarty.const.DISPLAY_THEME_REAL != $smarty.const.DISPLAY_THEME}
								(<em>{"Previewing"|gettext}</em>)
							{/if}
						{/if}
						{if ($theme->user_configured)}
							{if $smarty.const.THEME_STYLE == ""}
								{br}{icon class=configure action=configure_theme theme=$class title="Configure this Theme" text="Configure"}
							{else}
								{br}{icon class=configure action=configure_theme theme=$class sv=$smarty.const.THEME_STYLE title="Configure this Theme" text="Configure"}
							{/if}
						{/if}
					</td>
				</tr>
        	{/foreach}
        </tbody>
    </table>
</div>
