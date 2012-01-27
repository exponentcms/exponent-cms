{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

<div id="rsspullControl" class="control">
    <h2>{"Add RSS Feeds"|gettext}</h2> 
    {control type="text" id="feedmaker" name="feedmaker" label="RSS Feed URL"|gettext}
    <a class="addtolist add" href="#">{'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current Feeds"|gettext}</h4>
    <ul id="rsspull-feeds">
        {foreach from=$config.pull_rss item=feed}
            {if $feed!=""}<li>{control type="hidden" name="pull_rss[]" value=$feed}{$feed} - <a class="delete removerss" href="#">{"Remove"|gettext}</a></li>{/if}
        {foreachelse}
            <li id="norssfeeds">{'You don\'t have any RSS feeds configured'|gettext}</li>
        {/foreach}
    </ul>

    {script unique="rssfeedpicker" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
        var YAHOO=Y.YUI2;
        var add = YAHOO.util.Dom.getElementsByClassName('addtolist', 'a');
        YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Dom.setStyle('norssfeeds', 'display', 'none');
            YAHOO.util.Event.stopEvent(e);
            var feedtoadd = YAHOO.util.Dom.get("feedmaker");
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = feedtoadd.value + '    <input type="hidden" name="pull_rss[]" value="'+feedtoadd.value+'" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "delete removerss";
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
    });
    {/literal}
    {/script}
</div>
