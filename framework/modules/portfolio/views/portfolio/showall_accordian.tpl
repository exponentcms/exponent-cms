{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
 * Written and Designed by James Hunt
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

{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}

<div class="module portfolio showall-accordian">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon class="manage" controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
			{if $permissions.manage == 1 && $rank == 1}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}

	{* Assign the expanding div an ID based again of the $textitem ID so we know what to look for *}
    <div>
        {foreach name=items from=$page->cats key=catid item=cat}
            {* here, we're setting an ID based on the id of the $textitem *}
            {* We're also giving it a classname, which is what YUI will pick up and listen for *}
            <h2 id="expand{$catid}" class="expandable down" style="cursor:pointer" title="{"Expand this item"|gettext}">{if $cat->name ==""}{'The List'|gettext}{else}{$cat->name}{/if}</h2>
            <div id="expandcont{$catid}" class="piece">
                 {foreach from=$cat->records item=record}
                    {include 'portfolioitem.tpl'}
                {/foreach}
            </div>
        {/foreach}
    </div>
</div>

{* all we need is Annimation for the yuimods *}
{script unique="expanding-content" yuimodules="animation" yui3mods="1"}
{literal}
//wait for the DOM to load
YAHOO.util.Event.onDOMReady(function(){
    // gather all elements with a class name of expandable 
    var triggers =  YAHOO.util.Dom.getElementsByClassName('expandable');
    
    // listen for any triggers to be clicked, and execute the anonymous function when they do
    YAHOO.util.Event.on(triggers, 'click', function(e){
        
        //grab the HTML element from the click event
        var target = YAHOO.util.Event.getTarget(e);
        
        // get and parse out the numeric ID from the html node
        var eid = target.id.replace("expand","");
        
        // grab the element to expand based on our new ID
        var dvToExpand = YAHOO.util.Dom.get('expandcont'+eid);

        //the rest is your code :)
        var to_height = (dvToExpand.offsetHeight == 0) ? dvToExpand.scrollHeight : 0;
        var from_height = (dvToExpand.offsetHeight == 0) ? 0 : dvToExpand.scrollHeight;
        var ease_type = (from_height == 0) ? YAHOO.util.Easing.easeOut : YAHOO.util.Easing.easeIn;
        var new_status = (from_height == 0) ? "Collapse" : "expand";
        var h2ToExpand = YAHOO.util.Dom.get('expand'+eid);
        if (from_height == 0) {
            h2ToExpand.title="{/literal}{'Collapse this item'|gettext}{literal}";
            h2ToExpand.className='expandable up';
        } else {
            h2ToExpand.title="{/literal}{'Expand this item'|gettext}{literal}";
            h2ToExpand.className='expandable down';
        }
        var anim = new YAHOO.util.Anim(dvToExpand, { height: {to: to_height, from: from_height} }, 0.5, ease_type);
        anim.animate();
    });
});
{/literal}
{/script}
