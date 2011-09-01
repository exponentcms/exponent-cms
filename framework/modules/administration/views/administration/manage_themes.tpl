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
 
 
{css unique="themes" corecss="tables"}
{literal}
a.switchtheme {
    display:block;
    text-transform:capitalize;
    padding:3px 0 3px 20px
}
span.switchtheme.current {
    font-weight:bold;
    background:url({/literal}{$smarty.const.URL_FULL}{literal}framework/core/assets/images/exp-admin-sprite.png) no-repeat 5px -610px;
	padding:5px 22px 0
}
{/literal}
{/css}

<div class="administrationmodule thememanager">
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
            <tr>
                <td>
                    {img class="themepreview" src=$theme->preview w=100}
                </td>
                <td>
        			<h2>
        			    {$theme->name}
        			</h2>
        			<p>
        				{$theme->description}		
        			</p>
                </td>
				{if $theme->style_variations}
					<td colspan="2">
					<table border-bottom-style="transparent">
						{foreach from=$theme->style_variations item=sv key=svkey name=styles}
							<tr>
								<td>
									<a class="switchtheme{if $smarty.const.DISPLAY_THEME_REAL == $class && $smarty.const.THEME_STYLE == $sv} current{/if}" href="{link action=switch_themes theme=$class sv=$sv}" title='Select this Style'>{$sv}</a>
								</td>
								<td>
									{if $smarty.const.DISPLAY_THEME != $class}
										{icon img=view.png action=preview_theme theme=$class sv=$sv title="Preview this Style"}
									{elseif $smarty.const.DISPLAY_THEME_REAL != $smarty.const.DISPLAY_THEME}
										(<em><b>Previewing</b></em>)
									{/if}
								</td>
							</tr>
						{/foreach}
					</table>
					</td>
				{else}
					<td>
						{if $smarty.const.DISPLAY_THEME_REAL != $class}
							<a class="switchtheme current" href="{link action=switch_themes theme=$class}" title='Select this Theme'>Select</a>
						{else}
							<span class="switchtheme current"><b>Current</b>{br}</span>
						{/if}
						{if ($theme->user_configured)}
							{icon class=configure action=configure_theme theme=$class title="Configure this Theme" text="Configure"}{br}
						{/if}
						{if $smarty.const.DISPLAY_THEME != $class}
							{icon class=view action=preview_theme theme=$class title="Preview this Theme" text="Preview"}
						{elseif $smarty.const.DISPLAY_THEME_REAL != $smarty.const.DISPLAY_THEME}
							(<em>Previewing</em>)
						{/if}
					</td>
				{/if}
            </tr>
        	{/foreach}
        </tbody>
    </table>
</div>
