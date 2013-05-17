{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    <div class="form_header">
    	<div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help with"|gettext|cat:" "|cat:("RSS Pull Settings"|gettext) module="flickrfeed"}
    		</div>
            <h2>{"RSS Pull Settings"|gettext}</h2>
    	</div>
    </div>
    <h2>{"Add RSS Feeds"|gettext}</h2>
    {*{control type="text" id="feedmaker" name="feedmaker" label="RSS Feed URL"|gettext}*}
    {control type=url id="feedmaker" name="feedmaker" label="RSS Feed URL"|gettext}
    <a class="addtolist add" href="#">{'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current Feeds"|gettext}</h4>
    <ul id="rsspull-feeds">
        {foreach from=$config.pull_rss item=feed}
            {if $feed!=""}<li>{control type="hidden" name="pull_rss[]" value=$feed}{$feed} <a class="delete removerss" href="#">{"Remove"|gettext}</a></li>{/if}
        {foreachelse}
            <li id="norssfeeds">{'You don\'t have any RSS feeds configured'|gettext}</li>
        {/foreach}
    </ul>

    {*FIXME convert to yui3*}
    {script unique="rssfeedpicker" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
        var YAHOO=Y.YUI2;
        var add = YAHOO.util.Dom.getElementsByClassName('addtolist', 'a');
        YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Event.stopEvent(e);
            var feedtoadd = YAHOO.util.Dom.get("feedmaker");
            if (feedtoadd.value == '') return;
            YAHOO.util.Dom.setStyle('norssfeeds', 'display', 'none');
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = feedtoadd.value + '    <input type="hidden" name="pull_rss[]" value="'+feedtoadd.value+'" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "delete removerss";
            newRemove.innerHTML = " {/literal}{'Remove'|gettext}{literal}";
            newli.appendChild(newLabel);
            newli.appendChild(newRemove);
            var list = YAHOO.util.Dom.get('rsspull-feeds');
            list.appendChild(newli);
            YAHOO.util.Event.on(newRemove, 'click', function(e,o){
                if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                    var list = YAHOO.util.Dom.get('rsspull-feeds');
                    list.removeChild(this)
                    if (list.children.length == 1) YAHOO.util.Dom.setStyle('norssfeeds', 'display', '');;
                } else return false;
            },newli,true);
            feedtoadd.value = '';
        });
    
        var existingRems = YAHOO.util.Dom.getElementsByClassName('removerss', 'a');
        YAHOO.util.Event.on(existingRems, 'click', function(e,o){
            if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                YAHOO.util.Event.stopEvent(e);
                var targ = YAHOO.util.Event.getTarget(e);
                var lItem = YAHOO.util.Dom. getAncestorByTagName(targ,'li');
                var list = YAHOO.util.Dom.get('rsspull-feeds');
                list.removeChild(lItem);
                if (list.children.length == 1) YAHOO.util.Dom.setStyle('norssfeeds', 'display', '');;
            } else return false;
        });
    });
    {/literal}
    {/script}
</div>
