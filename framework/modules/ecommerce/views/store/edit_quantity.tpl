{control type="hidden" name="quantity_tab_loaded" value=1} 
{control type="text" name="quantity" label="Quantity" value=$record->quantity}
{control type="text" name="minimum_order_quantity" label="Minimum order quantity" value=$record->minimum_order_quantity|default:1}
{control type="checkbox"  name="allow_partial" label="Allow partial quantities?" value=1 checked=$record->allow_partial}
{control type="checkbox" name="is_hidden" label="Hide Product" value=$record->is_hidden} 
{control type="radiogroup" name="availability_type" label="Quantity Display" items=$record->quantity_display default=$record->availability_type|default:0}
{control type="textarea" name="availability_note" label="* Note to display per above selection" rows=5 cols=45 value=$record->availability_note}
