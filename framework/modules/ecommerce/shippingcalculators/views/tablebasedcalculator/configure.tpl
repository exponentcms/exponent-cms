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
 
{css unique="shippingtable" corecss="tables"}
{literal}
.exp-skin-table td {
    white-space:nowrap;
    text-align:left;
}
{/literal}
{/css}
<div id="tablebasedcalculator" class="module shipping  configure hide">
    {"Shipping Speeds"|gettext}
    <ul id="shippingspeeds">
		{foreach from=$calculator->shippingspeeds item=calc}
			<li>
			{$calc->speed}
			{* icon controller=shipping action=editspeed id=$calc->id title="Edit `$calc->speed`" *}
            {icon class=delete controller=shipping action=deleteSpeed id=$calc->id}
			</li>
		{/foreach}
    </ul>
    {icon class="add" action="editspeed/id/`$calculator->id`" text="Create new Shipping Speed"|gettext}
    {br}{br}
	<div class="{if !$calculator->shippingspeeds}hide{/if}">
    <hr>
    {br}
	
    <div>
    <a href="#" id="newrange">{"Add Range Set"|gettext}</a>
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
        <tr class="row row-{$smarty.section.i.index+1} {cycle values='odd,even'}">
        	<td class="from">
        		<a href="#" class="delete">{'Remove'|gettext}</a>
        	</td>
        	<td class="from">
        		<label for="from-{$smarty.section.i.index}">$</label><input type="text" size="10" id="from-{$smarty.section.i.index}" value="{$calculator->configdata.from[i]}" name="from[]}">
        	</td>
        	<td>
        		{'to'|gettext}
        	</td>
        	<td class="to">
        		<label for="to-{$smarty.section.i.index}">$</label><input type="text" size="10" id="to-{$smarty.section.i.index}" value="{$calculator->configdata.to[i]}" name="to[]">
        	</td>
			{foreach from=$calculator->shippingspeeds item=calc}
        	<td>
        		<label for="rate[{$shippingspeed}][]">$</label><input type="text" size="10" id="rate[{$shippingspeed}][]" value="{$calc->speed|remove_space|array_lookup:$calculator->configdata:$smarty.section.i.index}" name="{$calc->speed|remove_space}[]">
        	</td>
			{/foreach}
        </tr>
		{else}
            {$lastcharge=$smarty.section.i.index}
		{/if}
		{/section}
        <!-- stop looping me loop me -->

        <tr class="even last">
            <td class="from">
		
            </td>
            <td class="from">
                <label for="from-1">$</label><input type="text" name="from[]" value="{$calculator->configdata.from[$lastcharge]}" id="from-1" size="10">
            </td>
            <td>
                {'and up'|gettext}
            </td>
            <td class="to">

            </td>
			{foreach from=$calculator->shippingspeeds item=calc}
			
            <td>
				<label for="standard-1">$</label><input type="text" name="{$calc->speed|remove_space}[]" value="{$calc->speed|remove_space|array_lookup:$calculator->configdata:$lastcharge}" id="shipping_rate[1][]" size="10">
            </td>
			{/foreach}
        </tr>
        </tbody>
    </table>
	</div>
    {br}
</div>
<div class="loadingdiv">{'Loading'|gettext}</div>

{script unique="shipping-table" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
    var stb = Y.one('#shippingtable tbody');
    var andup = stb.one('.last');
    var addrange = Y.one('#newrange');
    var definc = 5.00;
    var odev = "odd";
    
    var rowTemplate = function (i){
    return '<tr class="row row-'+i+' '+odev+'">'+
            '<td class="from">'+
                '<a class="delete" href="#">remove</a>'+
            '</td>'+
            '<td class="from">'+
                '<label for="from-'+i+'">$</label>'+
                '<input type="text" name="from[]" value="" id="from-'+i+'" size="10">'+
            '</td>'+
            '<td>to</td>'+
            '<td class="to">'+
                '<label for="to-'+i+'">$</label>'+
                '<input type="text" name="to[]" value="" id="to-'+i+'" size="10">'+
            '</td>'+
			{/literal}
			{foreach from=$calculator->shippingspeeds item=calc}
				{literal}
					'<td><label for="{/literal}{$calc->speed}{literal}-1">$</label>'+
					'<input type="text" name="{/literal}{$calc->speed|remove_space}[]{literal}" value="" id="{$calc->speed}-' + i + '" size="10">' +
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
            e.target.ancestor('.row').remove ();
        });
    }
    
    if (stb.all('tr.row')._nodes.length>1) {
        stb.all('tr.row').each(function(n,k){
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
        var oldrowdelete = stb.one('.row-'+(rowcount-1)+' .delete');
        if (!Y.Lang.isNull(oldrowdelete)) oldrowdelete.setStyle('display','none');
        setRemover(rowcount)
    }
    
    addrange.on({'click':addNewRow});
    Y.one('#tablebasedcalculator').removeClass('hide');
    Y.one('.loadingdiv').remove();
    
});
{/literal}
{/script}
	
