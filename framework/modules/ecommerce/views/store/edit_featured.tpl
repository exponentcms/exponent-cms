{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{control type="hidden" name="tab_loaded[featured]" value=1}
{control type="checkbox" name="featured[is_featured]" id=is_featured label="Feature this product?"|gettext value=1 checked=$record->is_featured postfalse=1}
<span id=featured_body>
{control type="editor" name="featured[featured_body]" label="Featured Product Description"|gettext height=450 value=$record->featured_body}
</span>

{script unique="general" yui3mods=1}
{literal}
    Y.Global.fire('lazyload:cke');
{/literal}
{/script}

{script unique="editshipping2" jquery=1}
{literal}
$('#is_featured').change(function() {
    if ($('#is_featured').is(':checked') == false)
        $("#featured_body").hide("slow");
    else {
        $("#featured_body").show("slow");
    }
});
if ($('#is_featured').is(':checked') == false)
    $("#featured_body").hide("slow");
{/literal}
{/script}
