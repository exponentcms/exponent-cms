<h2>{'Miscellaneous Information'|gettext}</h2>
{control type="hidden" name="tab_loaded[misc]" value=1} 
{control type="text" name="misc[warehouse_location]" label="Warehouse Location"|gettext value=$record->warehouse_location}
<hr>
{control type="text" name="misc[previous_id]" label="Previous Product ID"|gettext value=$record->previous_id}
