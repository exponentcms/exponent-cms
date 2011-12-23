<h2>{'Notes'|gettext}</h2>
{control type="hidden" name="tab_loaded[notes]" value=1} 
{simplenote content_type="product" content_id=$record->id require_login="1" require_approval="0" require_notification="0" tab="notes"}
