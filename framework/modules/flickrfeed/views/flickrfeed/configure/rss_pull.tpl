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

<div id="rsspullControl" class="control">
    {control type="text" id="feedmaker" name="feedmaker" label="Add RSS Feed"|gettext}
    <a class="addtolist" href="#">{'Add to list'|gettext}</a>
    <h2>RSS Feeds</h2> 
    <ul id="rsspull-feeds">
        {foreach from=$config.pull_rss item=feed}
            {if $feed!=""}<li>{control type="hidden" name="pull_rss[]" value=$feed}{$feed} <a class="removerss" href="#">{'remove'|gettext}?</a></li>{/if}
        {foreachelse}
            <h2 id="norssfeeds">{'You don\'t have any RSS feeds configured'|gettext}</h2>
        {/foreach}
    </ul>
    {script unique="rssfeedpicker"}
    {literal}
    var add = YAHOO.util.Dom.getElementsByClassName('addtolist', 'a');
    YAHOO.util.Event.on(add, 'click', function(e,o){
        YAHOO.util.Dom.setStyle('norssfeeds', 'display', 'none');
        YAHOO.util.Event.stopEvent(e);
        var feedtoadd = YAHOO.util.Dom.get("feedmaker");
        var newli = document.createElement('li');
        var newLabel = document.createElement('span');
        newLabel.innerHTML = feedtoadd.value + '<input type="hidden" name="pull_rss[]" value="'+feedtoadd.value+'" />';
        var newRemove = document.createElement('a');
        newRemove.setAttribute('href','#');
        newRemove.innerHTML = " Remove?";
        newli.appendChild(newLabel);
        newli.appendChild(newRemove);
        var list = YAHOO.util.Dom.get('rsspull-feeds');
        list.appendChild(newli);
        YAHOO.util.Event.on(newRemove, 'click', function(e,o){
            var list = YAHOO.util.Dom.get('rsspull-feeds');
            list.removeChild(this)
        },newli,true);
        feedtoadd.value = '';
        //alert(feedtoadd);
    });
    
    var existingRems = YAHOO.util.Dom.getElementsByClassName('removerss', 'a');
    YAHOO.util.Event.on(existingRems, 'click', function(e,o){
        YAHOO.util.Event.stopEvent(e);
        var targ = YAHOO.util.Event.getTarget(e);
        var lItem = YAHOO.util.Dom. getAncestorByTagName(targ,'li');
        var list = YAHOO.util.Dom.get('rsspull-feeds');
        list.removeChild(lItem);
    });
    
    {/literal}
    {/script}
</div>
