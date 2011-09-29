{control type="hidden" name="featured_tab_loaded" value=1} 
{control type="checkbox" name="is_featured" label="Feature this product?" value=1 checked=$record->is_featured}
{control type="textarea" name="featured_body" label="Featured Description" height=450 value=$record->featured_body}          
{control type=files name="featured_image" label="Featured Product Images" subtype="featured_image" value=$record->expFile}
