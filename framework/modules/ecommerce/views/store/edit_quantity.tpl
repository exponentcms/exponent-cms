{control type="hidden" name="tab_loaded[quantity]" value=1} 
{control type="text" name="quantity[quantity]" label="Quantity" value=$record->quantity}
{control type="text" name="quantity[minimum_order_quantity]" label="Minimum order quantity" value=$record->minimum_order_quantity|default:1}
{control type="checkbox"  name="quantity[allow_partial]" label="Allow partial quantities?" value=1 checked=$record->allow_partial postfalse=1}
{control type="checkbox" name="quantity[is_hidden]" label="Hide Product" value=1 checked=$record->is_hidden postfalse=1} 
{control type="radiogroup" name="quantity[availability_type]" label="Quantity Display" items=$record->quantity_display default=$record->availability_type|default:0}
{control type="textarea" name="quantity[availability_note]" label="* Note to display per above selection" rows=5 cols=45 value=$record->availability_note}
