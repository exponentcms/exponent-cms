{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="shippingtable" corecss="tables"}
{literal}
.exp-skin-table td {
    white-space:nowrap;
    text-align:left;
}
.exp-skin-table th {
    padding-left: 30px;
}
/*.amount .form-control{*/
    /*display: inherit;*/
/*}*/
input.form-control,
#shippingtable label {
    display: inline;
}
{/literal}
{/css}

<div id="tablebasedcalculator" class="module shipping configure">
    <div id="table-tabs" class="">
        <ul class="nav nav-tabs" role="tablist">
	        <li role="presentation" class="nav-item"><a href="#tab1" class="nav-link active" role="tab" data-bs-toggle="tab"><em>{'General Settings'|gettext}</em></a></li>
        </ul>
        <div class="tab-content">
	        <div id="tab1" role="tabpanel" class="tab-pane fade show active">
                <blockquote>{'Shipping cost is based on order total and shipping speed'|gettext}</blockquote>
                <h4>{"Shipping Speeds"|gettext}</h4>
                {icon class="add" action="editspeed/id/`$calculator->id`" text="Create new Shipping Speed"|gettext}
                <ul id="shippingspeeds">
                    {foreach from=$calculator->shippingspeeds item=calc}
                        <li>
                            {$calc->speed}
                            {* icon controller=shipping action=editspeed id=$calc->id title="Edit `$calc->speed`" *}
                            {icon class=delete controller=shipping action=deleteSpeed id=$calc->id}
                        </li>
                    {/foreach}
                </ul>
                <div class="{if !$calculator->shippingspeeds}hide{/if}">
                    <hr>

                    <div>
                        {* <a href="#" id="newrange">{"Add Range Set"|gettext}</a> *}
                        {icon class="add" action=scriptaction name="newrange" text="Add Range Set"|gettext}
                    </div>
                    {br}
                    <table id="shippingtable" border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
                        <thead>
                            <tr>
                                <th colspan="4" style="width:50%">
                                    {"Price Range"|gettext}
                                </th>
                                {foreach from=$calculator->shippingspeeds item=calc}
                                <th>
                                   {$calc->speed}
                                </th>
                                {/foreach}
                            </tr>
                        </thead>
                        <tbody>
                            <!-- loop me -->
                            {section name=i loop=$calculator->configdata.from}
                                {if !($smarty.section.i.last)}
                                <tr class="line row-{$smarty.section.i.index+1} {cycle values='odd,even'}">
                                    <td>
                                        <a class="{expTheme::buttonStyle('red')} delete" href="#"><i class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-x-circle{else}fas fa-times-circle{/if}"></i> {'Remove'|gettext}</a>
                                    </td>
                                    <td class="from amount">
                                        <label for="from-{$smarty.section.i.index}">{currency_symbol}</label><input class="form-control" type="text" size="10" id="from-{$smarty.section.i.index}" value="{$calculator->configdata.from[i]}" name="from[]}">
                                    </td>
                                    <td>
                                        {'to'|gettext}
                                    </td>
                                    <td class="to amount">
                                        <label for="to-{$smarty.section.i.index}">{currency_symbol}</label><input class="form-control" type="text" size="10" id="to-{$smarty.section.i.index}" value="{$calculator->configdata.to[i]}" name="to[]">
                                    </td>
                                    {foreach from=$calculator->shippingspeeds item=calc}
                                    <td class="amount">
                                        <label for="rate-{$calc->speed|remove_space}-{$smarty.section.i.index}">{currency_symbol}</label><input class="form-control" type="text" size="10" id="rate-{$calc->speed|remove_space}-{$smarty.section.i.index}" value="{$calc->speed|remove_space|array_lookup:$calculator->configdata:$smarty.section.i.index}" name="{$calc->speed|remove_space}[]">
                                    </td>
                                    {/foreach}
                                </tr>
                                {else}
                                    {$lastcharge=$smarty.section.i.index}
                                {/if}
                            {/section}
                            <!-- stop looping me loop me -->

                            <tr class="row-{$smarty.section.i.index+1} even last">
                                <td>
                                    &#160;
                                </td>
                                <td class="from amount">
                                    <label for="from-{$smarty.section.i.index+1}">{currency_symbol}</label><input class="form-control" type="text" id="from-{$smarty.section.i.index+1}" value="{$calculator->configdata.from[$lastcharge]}" name="from[]" size="10">
                                </td>
                                <td>
                                    {'and up'|gettext}
                                </td>
                                <td class="to">
                                    &#160;
                                </td>
                                {foreach from=$calculator->shippingspeeds item=calc}
                                <td class="amount">
                                    <label for="rate-{$calc->speed|remove_space}-{$smarty.section.i.index}">{currency_symbol}</label><input class="form-control" type="text" name="{$calc->speed|remove_space}[]" value="{$calc->speed|remove_space|array_lookup:$calculator->configdata:$lastcharge}" id="rate-{$calc->speed|remove_space}-{$smarty.section.i.index}" size="10">
                                </td>
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>
                    {*{control type="text" name="handling" label="Handling Charge"|gettext size=5 filter=money value=$calculator->configdata.handling description='Charge added to each shipment regardless of total cost'|gettext}*}
                    {control type="text" name="shipping_service_name" label="Default Name for Shipping Service"|gettext value=$calculator->configdata.shipping_service_name|default:'Simple'|gettext}
                </div>
                {br}
            </div>
        </div>
    </div>
    {loading}
</div>

{script unique="shipping-table" yui3mods="node"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var stb = Y.one('#shippingtable tbody');
    var andup = stb.one('.last');
    var addrange = Y.one('#newrange');
    var definc = 5.00;
    var odev = "odd";

    var rowTemplate = function (i){
    return '<tr class="line row-'+i+' '+odev+'">'+
            '<td class="from">'+
                '<a class="{/literal}{expTheme::buttonStyle('red')}{literal} delete" href="#"><i class="fas fa-times-circle"></i> Remove</a>'+
            '</td>'+
            '<td class="from">'+
                '<label for="from-'+i+'">{/literal}{currency_symbol}{literal}</label>'+
                '<input class="form-control" type="text" name="from[]" value="" id="from-'+i+'" size="10">'+
            '</td>'+
            '<td>to</td>'+
            '<td class="to">'+
                '<label for="to-'+i+'">{/literal}{currency_symbol}{literal}</label>'+
                '<input class="form-control" type="text" name="to[]" value="" id="to-'+i+'" size="10">'+
            '</td>'+
			{/literal}
			{foreach from=$calculator->shippingspeeds item=calc}
				{literal}
					'<td><label for="{/literal}{$calc->speed}{literal}-1">{/literal}{currency_symbol}{literal}</label>'+
					'<input class="form-control" type="text" name="{/literal}{$calc->speed|remove_space}[]{literal}" value="" id="{$calc->speed}-' + i + '" size="10">' +
					'</td>'+
				{/literal}
			{/foreach}
			{literal}
			'</tr>';
    }

    var updateUpRange = function (rc){
        // stb.one('#to-'+rc).on({
        //     'key':function(e){
        //         Y.log(e.keyCode);
        //     }
        // });
    }

    var setRemover = function (rem){
        stb.one('.row-'+rem+' .delete').on('click',function(e){
            e.halt();
            andup.toggleClass('odd');
            andup.toggleClass('even');
            var prevdel = stb.one('.row-'+(rem-1)+' .delete');
            if (!Y.Lang.isNull(prevdel)) prevdel.setStyle('display','inline');
            e.target.ancestor('.line').remove ();
        });
    }

    if (stb.all('tr.line')._nodes.length>=1) {
        stb.all('tr.line').each(function(n,k){
            setRemover(k+1);
        });
    };

    var addNewRow = function(e) {
        e.halt();
        odev=(odev=="odd")?"even":"odd";
        andup.toggleClass('odd');
        andup.toggleClass('even');
        var rows = stb.all('tr');
        var rowcount = rows._nodes.length;
        stb.one('.last').insert(rowTemplate(rowcount),'before');
        stb.one('#from-{/literal}{$calc->id}{literal}-'+rowcount).focus();
        var oldrowdelete = stb.one('.row-'+(rowcount-1)+' .delete');
        if (!Y.Lang.isNull(oldrowdelete)) oldrowdelete.setStyle('display','none');
        setRemover(rowcount)
    }

    addrange.on({'click':addNewRow});
});
{/literal}
{/script}
