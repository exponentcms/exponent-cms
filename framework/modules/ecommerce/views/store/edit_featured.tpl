{control type="hidden" name="tab_loaded[featured]" value=1} 
{control type="checkbox" name="featured[is_featured]" label="Feature this product?"|gettext value=1 checked=$record->is_featured postfalse=1}
{control type="textarea" name="featured[featured_body]" label="Featured Description"|gettext height=450 value=$record->featured_body}