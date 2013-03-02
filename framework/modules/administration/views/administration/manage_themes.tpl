{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
            {help text="Get Help with"|gettext|cat:" "|cat:("Managing Themes"|gettext) module="manage-themes"}
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
				<tr class="{cycle values='odd,even'}{if $smarty.const.DISPLAY_THEME_REAL == $class} current{/if}">
					<td>
						{img class="themepreview" src=$theme->preview w=100}
					</td>
					<td>
						<h2>
							{$theme->name}
						</h2>
						<p>
							{$theme->description}
							{if $theme->mobile}{br}<em>({'mobile ready theme'|gettext})</em>{/if}
						</p>
					</td>
					<td class="actions">
						{if $theme->style_variations|@count>0}
							{*<h6>{"Style Variations"|gettext}</h6>*}
							{foreach from=$theme->style_variations item=sv key=svkey name=styles}
                                {group label=$sv|cat:' '|cat:'Style'|gettext}
                                    {if $smarty.const.DISPLAY_THEME == $class && $smarty.const.DISPLAY_THEME == $smarty.const.DISPLAY_THEME_REAL && $smarty.const.THEME_STYLE == $smarty.const.THEME_STYLE_REAL &&
                                        ($smarty.const.THEME_STYLE == $sv || ($smarty.const.THEME_STYLE == "" && $sv == "Default"))}
                                    {elseif $smarty.const.DISPLAY_THEME == $class && ($smarty.const.THEME_STYLE == $sv || ($smarty.const.THEME_STYLE == "" && $sv == "Default"))}
                                        (<em>{"Previewing"|gettext}</em>)
                                    {else}
                                        {*{icon img="view.png" action=theme_preview theme=$class sv=$sv title="Preview this Theme"|gettext}*}
                                        {icon class="view" action=theme_preview theme=$class sv=$sv title="Preview this Theme Style"|gettext text="Preview"|gettext}
                                    {/if}
                                    {if $smarty.const.DISPLAY_THEME_REAL == $class && ($smarty.const.THEME_STYLE_REAL == $sv || ($smarty.const.THEME_STYLE_REAL == "" && $sv == "Default"))}
                                        <span class="switchtheme current"> ({"Current"|gettext})</span>
                                    {else}
                                        {*<a class="switchtheme add" href="{link action=theme_switch theme=$class sv=$sv}" title={'Select this Style'|gettext}>{$sv}</a>*}
                                        {icon class="switchtheme add" action=theme_switch theme=$class sv=$sv title='Select this Theme Style'|gettext text='Use'|gettext}
                                    {/if}
                                    {if ($theme->user_configured)}
                                        {if $sv == "Default"}
                                            {icon class=configure action=configure_theme theme=$class title="Configure Default Theme Style"|gettext}
                                        {else}
                                            {icon class=configure action=configure_theme theme=$class sv=$sv title="Configure this Theme Style"|gettext}
                                        {/if}
                                    {/if}
                                {/group}
							{/foreach}
						{else}
                            {group label='Default Style'|gettext}
                                {if $smarty.const.DISPLAY_THEME != $class}
                                    {*{icon img="view.png" action=theme_preview theme=$class title="Preview this Theme"|gettext}*}
                                    {icon class="view" action=theme_preview theme=$class title="Preview this Theme"|gettext text="Preview"|gettext}
                                {elseif $smarty.const.DISPLAY_THEME_REAL != $smarty.const.DISPLAY_THEME}
                                    (<em>{"Previewing"|gettext}</em>)
                                {/if}
                                {if $smarty.const.DISPLAY_THEME_REAL != $class}
                                    {*<a class="switchtheme add" href="{link action=theme_switch theme=$class}" title={'Select this Theme'|gettext}>{"Use"|gettext}</a>*}
                                    {icon class="switchtheme add" action=theme_switch theme=$class title='Select this Theme'|gettext text="Use"|gettext}
                                {else}
                                    <span class="switchtheme current">({"Current"|gettext})</span>
                                {/if}
                                {if ($theme->user_configured)}
                                    {icon class=configure action=configure_theme theme=$class title="Configure this Theme"|gettext}
        						{/if}
                            {/group}
						{/if}
                        {if (!$theme->stock_theme)}
                            {icon class=export action=export_theme theme=$class title="Export this Theme"|gettext}
                        {/if}
					</td>
				</tr>
        	{/foreach}
        </tbody>
    </table>
</div>
