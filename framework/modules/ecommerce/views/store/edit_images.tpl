{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{control type="hidden" name="tab_loaded[images]" value=1}
<div id="imagefunctionality">              
    {control type="text" name="images[image_alt_tag]" label="Image Alt Tag"|gettext value=$record->image_alt_tag description="The image alt tag will be created dynamically by the system, however you may supply a custom one here:"|gettext}
    {control type=radiogroup columns=2 name="images[main_image_functionality]" label="Main Image Functionality"|gettext items="Single Image,Image with Swatches"|gettxtlist values="si,iws"  default=$record->main_image_functionality|default:"si"}
    <div id="si-div" class="imngfuncbody">
        {control type=files name=mainimages label="Main Product Image"|gettext subtype="mainimage" value=$record->expFile limit=1}
        {control type=files name=mainthumb label="Product Thumbnail Image"|gettext subtype="mainthumbnail" value=$record->expFile limit=1 description="If no image is provided to use as a thumbnail, one will be generated from the main image. This image will only show if additional images are provided"|gettext}
    </div>
    <div id="iws-div" class="imngfuncbody" style="display:none;">
        <table border="0" cellspacing="0" cellpadding="1" width="100%">
            <tr>
                <th width="50%">{"Image"|gettext}</th>
                <th width="50%">{"Color/Pattern Swatch"|gettext}</th>
            </tr>
            <tr>
                <td style="vertical-align:top;">
                    {control type=files name=imagesforswatches label="Images"|gettext subtype="imagesforswatches" value=$record->expFile}
                </td>
                <td style="vertical-align:top;">
                    {control type=files name=swatchimages label="Swatches"|gettext subtype="swatchimages" value=$record->expFile}
                </td>
            </tr>
        </table>
    </div>
    <div class="additional-images">
        {control type=files name=images label="Additional Images"|gettext subtype="images" value=$record->expFile description="Additional images to show for your product"|gettext}
    </div>
    {control type=files name=brochures label="Additional File Attachments"|gettext subtype="brochures" value=$record->expFile description="Attach Product Brochures, Docs, Manuals, etc."|gettext}
</div>

{script unique="mainimagefunctionality" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
    var radioSwitchers = Y.all('#imagefunctionality input[type="radio"]');
    radioSwitchers.on('click',function(e){
        Y.all(".imngfuncbody").setStyle('display','none');
        var curdiv = Y.one("#" + e.target.get('value') + "-div");
        curdiv.setStyle('display','block');
    });

    radioSwitchers.each(function(node,k){
        if(node.get('checked')==true){
            node.simulate('click');
        }
    });
    
});
{/literal}
{/script}
