{control type="hidden" name="parent_id" value=$record->parent_id}   
{control type="text" name="model" label="Model # / SKU" value=$record->model}
{control type="text" class="title" name="title" label="Product Name" value=$record->title}
{control type="dropdown" name="companies_id" label="Manufacturer" includeblank=true frommodel=company value=$record->companies_id}<a href='{link controller="company" action="manage"}'>Manage Manufacturers</a>{br}
{control type="textarea" name="summary" label="Product Summary" rows=5 cols=85 value=$record->summary}
{control type="editor" name="body" label="Product Description" height=450 value=$record->body}
{control type="text" class="title" name="feed_title" label="Product Title for Data Feeds" value=$record->feed_title}
{control type="textarea" name="feed_body" label="Product Description for Data Feeds (Description ONLY! - no HTML, no promotional language, no email addresses, phone numbers, or references to this website.)" rows=5 cols=85 value=$record->feed_body}
{control type="text" class="title" name="google_product_type" label="Google Product Type" value=$record->google_product_type}