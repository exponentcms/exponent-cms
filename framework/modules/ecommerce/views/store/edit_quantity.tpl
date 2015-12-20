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

{control type="hidden" name="tab_loaded[quantity]" value=1}
{control type="text" name="quantity[quantity]" label="Quantity in stock"|gettext value=$record->quantity}
{control type="text" name="quantity[minimum_order_quantity]" label="Minimum order quantity"|gettext value=$record->minimum_order_quantity|default:1} {* FIXME not in child product*}
{control type="text" name="quantity[multiple_order_quantity]" label="Must be ordered in multiples of"|gettext value=$record->multiple_order_quantity|default:1} {* FIXME not in child product*}
{*{control type="checkbox"  name="quantity[allow_partial]" label="Allow partial quantities?"|gettext value=1 checked=$record->allow_partial postfalse=1}*}
{*{control type="checkbox" name="quantity[is_hidden]" label="Hide Product"|gettext value=1 checked=$record->is_hidden postfalse=1}*}
{br}
<div id="availability_type">
{control type="radiogroup" name="quantity[availability_type]" label="Display based on Quantity"|gettext items=$record->quantity_display columns=1 default=$record->availability_type|default:0}
{control type="textarea" name="quantity[availability_note]" id=availability_note label="* "|cat:("Note to display per above selection"|gettext) rows=5 cols=45 value=$record->availability_note}
</div>

{script unique="quantitydisplay" yui3mods="node,node-event-simulate"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var radioSwitchers = Y.all('#availability_type input[type="radio"]');
    radioSwitchers.on('click',function(e){
        var curval = e.target.get('value');
        var availnote = Y.one("#availability_noteControl");
        if (curval == 1 || curval == 2) {
            availnote.setStyle('display','block');
        } else {
            availnote.setStyle('display','none');
        }
    });

    radioSwitchers.each(function(node,k){
        if(node.get('checked')==true){
            node.simulate('click');
        }
    });
});
{/literal}
{/script}
