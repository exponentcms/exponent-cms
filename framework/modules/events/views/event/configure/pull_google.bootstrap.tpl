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

<div id="googlepullControl" class="control">
    <div class="form_header">
    	<div class="info-header">
    		<div class="related-actions">
    		    {help text="Get Help with"|gettext|cat:" "|cat:("Google Calendar Pull Settings"|gettext) module="google-pull"}
    		</div>
            <h2>{"Google Calendar Pull Settings"|gettext}</h2>
    	</div>
    </div>
    <h2>{"Add External Google Calendar Feeds"|gettext}</h2>
    {*{control type="text" id="googlefeedmaker" name="googlefeedmaker" label="Google Calendar XML Feed Link/URL"|gettext}*}
    {control type=url id="googlefeedmaker" name="googlefeedmaker" label="Google Calendar XML Feed Link/URL"|gettext}
    {if (BTN_SIZE == 'large')}
        {$btn_size = 'btn-small'}
        {$icon_size = 'icon-large'}
    {else}
        {$btn_size = 'btn-mini'}
        {$icon_size = ''}
    {/if}
    <a class="addtogooglelist btn {$btn_size}" href="#"><i class="icon-plus-sign {$icon_size}"></i> {'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current Google Calendar Feeds"|gettext}</h4>
    <ul id="googlepull-feeds">
        {foreach from=$config.pull_gcal item=feed name=feed}
            {*{if $feed!=""}<li>{control type="hidden" name="pull_google[]" value=$feed}{$feed} <a class="delete removegoogle" href="#">{"Remove"|gettext}</a></li>{/if}*}
            {if $feed!=""}
                <li>
                    {control type="hidden" name="pull_gcal[]" value=$feed}{control type=color label=$feed name="pull_gcal_color[]" id="pull_gcal_color`$smarty.foreach.feed.index`" value=$config.pull_gcal_color[$smarty.foreach.feed.index] hide=1 flip=1}
                    <a class="removegoogle btn {$btn_size} btn-danger" href="#"><i class="icon-remove-sign {$icon_size}"></i> {"Remove"|gettext}</a>
                </li>
            {/if}
        {foreachelse}
            <li id="nogooglefeeds">{'You don\'t have any Google Calendar feeds configured'|gettext}</li>
        {/foreach}
    </ul>

    {*FIXME convert to yui3*}
    {script unique="googlefeedpicker" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-connectioncore','yui2-json','yui2-selector','yui2-get', function(Y) {
        var YAHOO=Y.YUI2;
        var add = YAHOO.util.Dom.getElementsByClassName('addtogooglelist', 'a');
        YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Event.stopEvent(e);
            var feedtoadd = YAHOO.util.Dom.get("googlefeedmaker");
            if (feedtoadd.value == '') return;
            YAHOO.util.Dom.setStyle('nogooglefeeds', 'display', 'none');
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = '<input type="hidden" name="pull_gcal[]" value="'+feedtoadd.value+'" />';
            newLabel.innerHTML = newLabel.innerHTML + '<span id="placeholder" style="display:inline-block"></span>';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "removegoogle btn {/literal}{$btn_size}{literal} btn-danger";
            newRemove.innerHTML = " {/literal}<i class='icon-remove-sign {$icon_size}'></i> {'Remove'|gettext}{literal}";
            newli.innerHTML = newLabel.innerHTML;
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
            var sUrl = eXp.PATH_RELATIVE+"index.php?ajax_action=1&json=1&controller=event&action=buildControl&label="+encodeURIComponent(feedtoadd.value)+"&name=pull_gcal_color[]&id=pull_gcal_color"+list.children.length+"&hide=1&flip=1&value=000";
            var callback = {
                success: function(oResponse) {
                    placeholder = YAHOO.util.Dom.get("placeholder");
                    placeholder.innerHTML = oResponse.responseText;
                    var scripts = placeholder.getElementsByTagName('script');
                    for (var scrpt, i = scripts.length; i-- && (scrpt = scripts[i]);) {
                        if(!YAHOO.util.Dom.getAttribute (scrpt,'src')){
                            eval(scrpt.innerHTML);
                        } else {
                            var url = scrpt.get('src');
                            if (url.indexOf("ckeditor")) {
                                YAHOO.util.Get.script(url);
                            };
                        };
                    };
                    var csslinks = placeholder.getElementsByTagName('link');
                    for (var link, i = csslinks.length; i-- && (link = csslinks[i]);) {
                        var url = YAHOO.util.Dom.getAttribute (link,'href');
                        YAHOO.util.Get.css(url);
                    };
                    YAHOO.util.Dom.setAttribute(placeholder,'id','inplace');
                },
                timeout: 7000,
                scope: callback,
            };
            YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
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
