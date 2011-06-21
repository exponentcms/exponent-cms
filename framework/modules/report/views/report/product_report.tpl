{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css"}

{/css}
{css unique="report-builder" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/report-builder.css" corecss="panels,tree,button"}

{/css}

{form controller="report" action="generateProductReport" id="reportform" name="reportform"}
<div id="create-prod-report" class="module report build-report yui-skin-sam">
    <div id="report-form" class="exp-ecom-table">
    <table border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>
                    <h1>{gettext str="Build a Product Report"}</h1>
                </th>
            </tr>
        </thead>
        <tbody>  
            <tr class="even">
                  <td>
                    {control type="dropdown" name="product_type" label="A Product Type Of" size=4 multiple=true includeblank="--Any--" items=$product_types}    
                </td>
            </tr>              
            <tr class="odd">
                  <td>
                    {control type="dropdown" name="product_status" label="A Product Status Of" includeblank="--Any--" size=4 multiple=true frommodel=product_status}    
                </td>
            </tr>
            <tr class="even">
                <td>
                    <div class="control">
                        <span class="label">Select Categories</span><a href="#create-prod-report" id="showcats" class="awesome small green">{"Show Categories"|gettext}</a>
                    </div>
                    <div id="catpicker" class="hide">
                        <div class="hd">Select Categories</div>
                        <div class="bd">
                            <div style="overflow-y:scroll;height:300px;">
                            {control type="tagtree" addable=false id="managecats" name="managecats" controller=storeCategory draggable=false menu=false expandonstart=false checkable=true}
                            </div>
                        </div>
                    </div>
                    {script unique="pickerpopper" yui2mods="container"}
                    {literal}
                    YAHOO.util.Event.onDOMReady(function(){
                        var panel = new YAHOO.widget.Panel("catpicker", { width:"500px", zIndex:10, visible:false, draggable:false, close:true, context:['showcats','tl','tr'] } ); 
                        panel.render('create-prod-report');
                        YAHOO.util.Event.on('showcats', 'click', panel.show, panel, true);
                        YAHOO.util.Dom.removeClass('catpicker', 'hide');
                        
                    });
                    {/literal}
                    {/script}
                </td>
            </tr>
            <tr class="odd">
                <td>
                    {control type="dropdown" name="product-range-op" label="A Product ID..." items="Equal to,Less than,Greater than" values="e,l,g"}
                    {control type="text" name="product-range-num" value=$record->prn class="collapse prn"}
                </td>
            </tr>
            <tr class="even">
                 <td>
                    {control type="dropdown" name="product-price-op" label="Product Price..." items="Equal to,Less than,Greater than" values="e,l,g"}
                    {control type="text" name="product-price-num" value=$record->ppn class="collapse ppn"}
                </td> 
            </tr>
            <tr class="odd">
                <td>
                    {control type="text" name="pnam" label="Product Name Like" value=$record->product}
                </td>
                
            </tr>
            <tr class="even">
                <td>
                    {control type="text" name="sku" label="Product SKU Like" value=$record->sku}
                </td>
                
            </tr>
            
            <tr class="odd">
                <td>
                    <button id="submit-report" href="javascript:document.reportform.submit(); return false;" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"Generate Report"|gettext}</button>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
{/form}
