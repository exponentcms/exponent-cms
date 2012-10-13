{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
<a href='{link controller="store" action="edit_model_alias" product_id=$record->id}' class="add">{'Add Model Alias'|gettext}</a>
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
