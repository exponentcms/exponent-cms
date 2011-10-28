<h2>Active/Inactive</h2>
{control type="hidden" name="tab_loaded[status]" value=1} 
{control type="radiogroup" name="status[active_type]" label=" " items=$record->active_display default=$record->active_type|default:0}
<h2>Status</h2>
{control type="dropdown" name="status[product_status_id]" label=" " frommodel=product_status items=$status_display value=$record->product_status_id}
