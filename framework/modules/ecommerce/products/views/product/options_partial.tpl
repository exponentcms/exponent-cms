{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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
 

{css unique="option-styles" link="`$asset_path`css/options-edit.css" corecss="tables"}

{/css}

<div class="product options-partial">
	{foreach from=$optiongroups item=group}
    <div class="panel"> 
        <div class="hd">
            <h2>{$group->title}</h2><a href="#" class="expand">{'Expand'|gettext}</a>
        </div>
    
        <div class="bd collapsed">
    	    <table class="options exp-skin-table" summary="{$group->title} {'Product Options'|gettext}">
    	    <thead>
    	    <tr>
    	        <th colspan="5">
    	            <h2>{$group->title}</h2>                    
    	            {control type="hidden" name="optiongroups[`$group->title`][id]" value=$group->id}
    	            {control type="hidden" name="optiongroups[`$group->title`][title]" value=$group->title}
    	            {control type="hidden" name="optiongroups[`$group->title`][optiongroup_master_id]" value=$group->optiongroup_master_id}
                    {control type="text" name="optiongroups[`$group->title`][rank]" label="Rank"|gettext size="3" value=$group->rank}
                    {control type="checkbox" nowrap=true name="optiongroups[`$group->title`][required]" label="Required"|gettext value=1 checked=$group->required}
        	        {control type="radio" nowrap=true name="optiongroups[`$group->title`][allow_multiple]" label="Select Single"|gettext value=0 checked=$group->allow_multiple}
        	        {control type="radio" nowrap=true name="optiongroups[`$group->title`][allow_multiple]" label="Select  Multiple"|gettext value=1 checked=$group->allow_multiple}
        	    </th>
    	    </tr>
            <tr class="column-label">     
                <th>{'Label'|gettext}</th>
                <th>{'Adjustment'|gettext}</th>
                <th>{'Modifier'|gettext}</th>
                <th>{'Amount'|gettext}</th>
                <th>{'Default'|gettext}</th>
            </tr>
    	    </thead>
    	    <tbody>
            {foreach key=key from=$group->options item=option}
            <tr class="{cycle values='odd,even' advance=false}">     
                <td width="100%">       
                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][id]" value=$option->id}             
                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][title]" value=$option->title}  
                    {control type="hidden" name="optiongroups[`$group->title`][options][`$option->title`][option_master_id]" value=$option->option_master_id}                  

                    {control type="checkbox" name="optiongroups[`$group->title`][options][`$option->title`][enable]" label=$option->title value=1 checked=$option->enable}
                    <a rel="mo-{$key}-{$group->title}" class="togglelink" href="#">+{'More...'|gettext}</a>
                </td>
                <td>{control type="dropdown" name="optiongroups[`$group->title`][options][`$option->title`][updown]" items="+,-" values="+,-" label=" " value=$option->updown}</td>
                <td>{control type="dropdown" name="optiongroups[`$group->title`][options][`$option->title`][modtype]" items="$,%" values="$,%" label=" " value=$option->modtype}</td>
                <td>{control type="text" name="optiongroups[`$group->title`][options][`$option->title`][amount]" label=" " size=6 value=$option->amount}</td>
                <td>{control type="radio" name="defaults[`$group->title`]" label="Default" value=$option->title checked=$option->is_default}</td>
            </tr>
            <tr class="{cycle values='odd,even'}" id="mo-{$key}-{$group->title}" style="display:none">  
                <td colspan=5>
                    {control type="text" name="optiongroups[`$group->title`][options][`$option->title`][optionweight]" label="Option Weight"|gettext size=6 value=$option->amount}
                    <hr>
                </td>
            </tr>
            {foreachelse}
                <p>
                 {'This option group does not have any options yet.'|gettext}
                </p>
            {/foreach}
            </tbody>
            </table>
        </div>
    </div>
    {foreachelse}
        {'There are no product options setup yet.'|gettext}
    {/foreach}
</div>

{script unique="expand-panels" yui3mods=1}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
    var YAHOO=Y.YUI2;

        var action = function(e){
            e.halt();

            var pBody = e.target.ancestor('.panel').one('.bd');
            
            if (e.target.getAttribute("class")=="collapse") {
                pBody.replaceClass('expanded','collapsed');
                e.target.replaceClass('collapse','expand');
            } else {
                pBody.replaceClass('collapsed','expanded');
                e.target.replaceClass('expand','collapse');
            }
        }
        Y.all('.options-partial .panel .hd a').on('click',action);

        var toggles = Y.all('a.togglelink');
        toggles.on('click', function(e){
            e.halt();
            var targrel = e.target.get("rel");
           if (Y.one('#'+targrel).getStyle('display')=="none") {
               if (Y.UA.ie > 0) {
                   Y.one('#'+targrel).setStyle('display', 'block');
               } else {
                   Y.one('#'+targrel).setStyle('display', 'table-row');
               }
           } else {
               Y.one('#'+targrel).setStyle('display',"none");
           }
        });
    
    YAHOO.util.Event.onDOMReady(function(){
        var toggles = YAHOO.util.Dom.getElementsByClassName('togglelink', 'a');
        YAHOO.util.Event.on(toggles, 'click', function(e){
            YAHOO.util.Event.stopEvent(e);
           var targ = YAHOO.util.Event.getTarget(e);
           if (YAHOO.util.Dom.getStyle(targ.rel, 'display')=="none") {
               if (YAHOO.env.ua.ie > 0) {
                   YAHOO.util.Dom.setStyle(targ.rel, 'display', 'block');
               } else {
                   YAHOO.util.Dom.setStyle(targ.rel, 'display', 'table-row');
               }
           } else {
               YAHOO.util.Dom.setStyle(targ.rel, 'display',"none");
           }
        });

    });
});
{/literal}
{/script}

