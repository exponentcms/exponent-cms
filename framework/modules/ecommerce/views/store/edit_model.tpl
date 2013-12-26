{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{control type="hidden" name="tab_loaded[model]" value=1}
<h2>{'Product SKUS / Model'|gettext}</h2>
{if !$record->id}
    <h4>{'You must save this product before you may create model aliases'|gettext}</h4>
{else}
    {icon class="add" controller="store" action="edit_model_alias" product_id=$record->id text='Add Model Alias'|gettext}
{/if}
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
	<thead>
		<tr>
			<th style="width:50px">
				&#160;
			</th>
			<th>
				{'Alias'|gettext}
			</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$record->model_alias item=model_alias key=key name=model_aliases}
            <tr class="{cycle values='odd,even'}">
                <td>
                    {icon action=edit_model_alias record=$model_alias img="edit.png"}
                    {icon action=delete_model_alias record=$model_alias img="delete.png"}
                </td>
                <td>
                    {$model_alias->model}
                </td>
            </tr>
		{/foreach}
	</tbody>
</table>
