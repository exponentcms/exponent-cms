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

<div id="icalpullControl" class="control">
    <div class="form_header">
    	<div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("iCal Pull Settings"|gettext) module="ical-pull"}
    		</div>
            <h2>{"iCal Pull Settings"|gettext}</h2>
    	</div>
    </div>
    <h2>{"Add External iCal/ics Feeds"|gettext}</h2>
    {control type="text" id="icalfeedmaker" name="icalfeedmaker" label="iCal Feed URL"|gettext}
    <a class="addtoicallist add" href="#">{'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current iCal Feeds"|gettext}</h4>
    <ul id="icalpull-feeds">
        {foreach from=$config.pull_ical item=feed}
            {if $feed!=""}<li>{control type="hidden" name="pull_ical[]" value=$feed}{$feed} <a class="delete removeical" href="#">{"Remove"|gettext}</a></li>{/if}
        {foreachelse}
            <li id="noicalfeeds">{'You don\'t have any iCal feeds configured'|gettext}</li>
        {/foreach}
    </ul>

    {script unique="icalfeedpicker" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event', function(Y) {
        var YAHOO=Y.YUI2;
        var add = YAHOO.util.Dom.getElementsByClassName('addtoicallist', 'a');
        YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Dom.setStyle('noicalfeeds', 'display', 'none');
            YAHOO.util.Event.stopEvent(e);
            var feedtoadd = YAHOO.util.Dom.get("icalfeedmaker");
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = feedtoadd.value + '    <input type="hidden" name="pull_ical[]" value="'+feedtoadd.value+'" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "delete removeical";
            newRemove.innerHTML = " {/literal}{'Remove'|gettext}{literal}";
            newli.appendChild(newLabel);
            newli.appendChild(newRemove);
            var list = YAHOO.util.Dom.get('icalpull-feeds');
            list.appendChild(newli);
            YAHOO.util.Event.on(newRemove, 'click', function(e,o){
                if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                    var list = YAHOO.util.Dom.get('icalpull-feeds');
                    list.removeChild(this)
                } else return false;
            },newli,true);
            feedtoadd.value = '';
            //alert(feedtoadd);
        });
    
        var existingRems = YAHOO.util.Dom.getElementsByClassName('removeical', 'a');
        YAHOO.util.Event.on(existingRems, 'click', function(e,o){
           if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                YAHOO.util.Event.stopEvent(e);
                var targ = YAHOO.util.Event.getTarget(e);
                var lItem = YAHOO.util.Dom. getAncestorByTagName(targ,'li');
                var list = YAHOO.util.Dom.get('icalpull-feeds');
                list.removeChild(lItem);
           } else return false;
        });
    });
    {/literal}
    {/script}
</div>
