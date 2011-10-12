{if $record->parent_id == 0}
	{icon class="manage" controller="storeCategory" action="manage"}
	{control type="hidden" name="tab_loaded[categories]" value=1}    
	{br}
	{control type="tagtree" name="managecats" id="managecats" controller="store" model="storeCategory" draggable=false addable=false menu=true checkable=true values=$record->storeCategory expandonstart=true }
{else}
	<a href='{link controller="storeCategory" action="manage"}'>Manage Categories</a>{br}{br}
	<h2>Category is inherited from this product's parent.</h2>
{/if}