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
 
{if $is_email == 1}
<style type="text/css" media="screen">
    {$css}
</style> 
{else}
	{css unique="default-report" corecss="tables"}

	{/css}
{/if}
 
 <table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th colspan="2">
                <h2>{$title}</h2>
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$fields key=fieldname item=value}
        <tr class="{cycle values="even,odd"}">
            <td>{$captions[$fieldname]}</td>
            <td>{$value}</td>
        </tr>
        {/foreach}
    </tbody>
</table>

{if $is_email == 0}
	{br}
	<a href="{$backlink}">{$_TR.back}</a>
{/if}