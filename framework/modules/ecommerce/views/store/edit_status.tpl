<h2>Active/Inactive</h2>
{control type="hidden" name="status_tab_loaded" value=1} 
{control type="radiogroup" name="active_type" label=" " items=$record->active_display default=$record->active_type|default:0}
<h2>Status</h2>
{control type="dropdown" name="product_status_id" label=" " frommodel=product_status items=$status_display value=$record->product_status_id}
