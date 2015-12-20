{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[featured]" value=1}
    {if count($record->childProduct)}
        <h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
    {control type="checkbox" name="featured[is_featured]" id=is_featured label="Feature this product?"|gettext value=1 checked=$record->is_featured postfalse=1}
    <span id=featured_body>
        {control type=files name="featured_image" label="Featured Product Image"|gettext subtype="featured_image" accept="image/*" value=$record->expFile limit=1 folder=$config.upload_folder description="Image to use if this item is a featured product"|gettext}
        {control type="editor" name="featured[featured_body]" label="Featured Product Description"|gettext height=450 value=$record->featured_body}
    </span>
{else}
	<h4><em>({'Featured Details'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}

{script unique="general" yui3mods="node,event-custom"}
{literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.Global.fire('lazyload:cke');
    });
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
