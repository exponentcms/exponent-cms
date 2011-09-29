{control type="hidden" name="images_tab_loaded" value=1} 
<div id="imagefunctionality">              
     The image alt tag will be created dynamically by the system, however you may supply a custom one here:
    {control type="text" name="image_alt_tag" label="Image Alt Tag" value=$record->image_alt_tag}
    {control type=radiogroup columns=2 name="main_image_functionality" label="Main Image Functionality" items="Single Image,Image with Swatches" values="si,iws"  default=$record->main_image_functionality|default:"si"}
    
    <div id="si-div" class="imngfuncbody">
        <h3>Single Image</h3>
        <h4>Main Image</h4>
        {control type=files name=mainimages label="Product Images" subtype="mainimage" value=$record->expFile limit=1}
        <h4>{gettext str="Thumbnail for Main Image"}</h4>
        <p>{gettext str="If no image is provided to use as a thumbnail, one will be generated from the main image. This image will only show if additional images are provided"}</p>
        {control type=files name=mainthumb label="Product Images" subtype="mainthumbnail" value=$record->expFile limit=1}
    </div>
    <div id="iws-div" class="imngfuncbody" style="display:none;">
        <table border="0" cellspacing="0" cellpadding="1" width="100%">
            <tr>
                <th width="50%">Image</th>
                <th width="50%">Color/Pattern Swatch</th>
            </tr>
            <tr>
                <td style="vertical-align:top;">
                    {control type=files name=imagesforswatches label="Images" subtype="imagesforswatches" value=$record->expFile}
                </td>
                <td style="vertical-align:top;">
                    {control type=files name=swatchimages label="Swatches" subtype="swatchimages" value=$record->expFile}
                </td>
            </tr>
        </table>
    </div>
    {br}
    <h4>{gettext str="Additional Images"}</h4>
    <p>{gettext str="Have additional images to show for your product?"}</p>
    
    <div class="additional-images">
        {control type=files name=images label="Additional Images" subtype="images" value=$record->expFile}
    </div>
    {br}
    <h4>{gettext str="Additional File Attachments"}</h4>
    <p>{gettext str="Attach Product Brochures, Docs, Manuals, etc."}</p>
    {control type=files name=brochures label="Additional Files" subtype="brochures" value=$record->expFile}
</div>

{script unique="mainimagefunctionality" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','node-event-simulate', function(Y) {
    var radioSwitchers = Y.all('#main_image_functionalityControl input[name="main_image_functionality"]');
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
