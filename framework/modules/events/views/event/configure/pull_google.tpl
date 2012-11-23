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

<div id="googlepullControl" class="control">
    <div class="form_header">
    	<div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Google Calendar Pull Settings"|gettext) module="google-pull"}
    		</div>
            <h2>{"Google Calendar Pull Settings"|gettext}</h2>
    	</div>
    </div>
    <h2>{"Add External Google Calendar Feeds"|gettext}</h2>
    {control type="text" id="googlefeedmaker" name="googlefeedmaker" label="Google Calendar Feed URL"|gettext}
    <a class="addtogooglelist add" href="#">{'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current Google Calendar Feeds"|gettext}</h4>
    <ul id="googlepull-feeds">
        {foreach from=$config.pull_gcal item=feed name=feed}
            {*{if $feed!=""}<li>{control type="hidden" name="pull_google[]" value=$feed}{$feed} <a class="delete removegoogle" href="#">{"Remove"|gettext}</a></li>{/if}*}
            {if $feed!=""}<li>{control type="hidden" name="pull_gcal[]" value=$feed}{control type=color label=$feed name="pull_gcal_color[]" id="pull_gcal_color`$smarty.foreach.feed.index`" value=$config.pull_gcal_color[$smarty.foreach.feed.index] hide=1 flip=1}<a class="delete removegoogle" href="#">{"Remove"|gettext}</a></li>{/if}
        {foreachelse}
            <li id="nogooglefeeds">{'You don\'t have any Google Calendar feeds configured'|gettext}</li>
        {/foreach}
    </ul>

    {script unique="googlefeedpicker" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
        var YAHOO=Y.YUI2;
        var add = YAHOO.util.Dom.getElementsByClassName('addtogooglelist', 'a');
        YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Event.stopEvent(e);
            var feedtoadd = YAHOO.util.Dom.get("googlefeedmaker");
            if (feedtoadd.value == '') return;
            YAHOO.util.Dom.setStyle('nogooglefeeds', 'display', 'none');
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = feedtoadd.value + '    <input type="hidden" name="pull_gcal[]" value="'+feedtoadd.value+'" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "delete removegoogle";
            newRemove.innerHTML = " {/literal}{'Remove'|gettext}{literal}";
            newli.appendChild(newLabel);
            newli.appendChild(newRemove);
            var list = YAHOO.util.Dom.get('googlepull-feeds');
            list.appendChild(newli);
            YAHOO.util.Event.on(newRemove, 'click', function(e,o){
                if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                    var list = YAHOO.util.Dom.get('googlepull-feeds');
                    list.removeChild(this)
                    if (list.children.length == 1) YAHOO.util.Dom.setStyle('nogooglefeeds', 'display', '');;
                } else return false;
            },newli,true);
            feedtoadd.value = '';
        });
    
        var existingRems = YAHOO.util.Dom.getElementsByClassName('removegoogle', 'a');
        YAHOO.util.Event.on(existingRems, 'click', function(e,o){
            if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                YAHOO.util.Event.stopEvent(e);
                var targ = YAHOO.util.Event.getTarget(e);
                var lItem = YAHOO.util.Dom. getAncestorByTagName(targ,'li');
                var list = YAHOO.util.Dom.get('googlepull-feeds');
                list.removeChild(lItem);
                if (list.children.length == 1) YAHOO.util.Dom.setStyle('nogooglefeeds', 'display', '');;
            } else return false;
        });
    });
    {/literal}
    {/script}
</div>
