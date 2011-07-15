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
a.switchtheme.current {
    font-weight:bold;
    background:url({/literal}{$smarty.const.URL_FULL}{literal}framework/core/assets/images/exp-admin-sprite.png) no-repeat 5px -610px;
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
                {"Preview"|gettext}
                </th>
                <th>
                {"Description"|gettext}
                </th>
                <th>
                {"Style Variations"|gettext}
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
                <td>
                    {if $theme->style_variations}
                        {foreach from=$theme->style_variations item=sv key=svkey name=styles}
        				    <a class="switchtheme{if $smarty.const.DISPLAY_THEME_REAL == $class && $smarty.const.THEME_STYLE == $sv} current{/if}" href="{link action=switch_themes theme=$class sv=$sv}">{$sv}</a>
                        {/foreach}
                    {else}
        				<a class="switchtheme{if $smarty.const.DISPLAY_THEME_REAL == $class} current{/if}" href="{link action=switch_themes theme=$class}">Default</a>
        			{/if}
                </td>
            </tr>
        	{/foreach}
        </tbody>
    </table>
</div>
