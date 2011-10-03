<h2>Add options to your product.</h2>
{control type="hidden" name="tab_loaded[options]" value=1} 
By simply selecting the checkbox in front of an option in an option group (the LABEL column), that option group and option will be added to the checkout process for this product.
By default, the user is NOT required to make a selection.  However, if you select the Required checkbox, the user will be forced to make a selection from that option group. {br}
Select Single presents the option group as a dropdown field where they may select one and only option.{br}
Select Multiple presents the options as a checkbox group where the user may select multiple options.{br}
Selecting the Default radio button for an option will cause that option to be selected by default. {br}{br}
{include file="`$smarty.const.BASE`framework/modules/ecommerce/products/views/product/options_partial.tpl"}
