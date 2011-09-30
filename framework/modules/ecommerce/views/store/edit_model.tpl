<h2>Product SKUS / Model</h2>
<a href='{link controller="store" action="edit_model_alias" product_id=`$record->id`}' class="add">Add Model Alias</a>
<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
	<thead>
		<tr>
			<th style="width:50px">
				&nbsp;
			</th>
			<th>
				Alias
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
