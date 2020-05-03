{*
 * Copyright (c) 2004-2020 OIC Group, Inc.
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
    		    {help text="Get Help with"|gettext|cat:" "|cat:("RSS Pull Settings"|gettext) module="news"}
    		</div>
            <h2>{"RSS Pull Settings"|gettext}</h2>
    	</div>
    </div>
    <h3>{"Add RSS Feeds"|gettext}</h3>
    {*{control type="text" id="feedmaker" name="feedmaker" label="RSS Feed URL"|gettext}*}
    {control type=url id="feedmaker" name="feedmaker" label="RSS Feed URL"|gettext}
    {if $smarty.const.BTN_SIZE == 'large'}
        {$btn_size = 'btn-lg'}
        {$icon_size = 'fa-lg'}
    {elseif $smarty.const.BTN_SIZE == 'small' || $smarty.const.BTN_SIZE == 'extrasmall'}
        {$btn_size = 'btn-sm'}
        {$icon_size = ''}
    {else}
        {$btn_size = ''}
        {$icon_size = 'fa-lg'}
    {/if}
    <a id="addtolist" class="btn btn-success {$btn_size}" href="#"><i class="fas fa-plus-circle {$icon_size}"></i> {'Add to list'|gettext}</a>{br}{br}
    <h4>{"Current Feeds"|gettext}</h4>
    <ul id="rsspull-feeds">
        {foreach from=$config.pull_rss item=feed}
            {if $feed!=""}<li>{control type="hidden" name="pull_rss[]" value=$feed}{$feed} <a class="removerss btn {$btn_size} btn-danger" href="#"><i class="fas fa-times-circle {$icon_size}"></i> {"Remove"|gettext}</a></li>{/if}
        {/foreach}
        <li id="norssfeeds">{'You don\'t have any RSS feeds configured'|gettext}</li>
    </ul>

    {script unique="rssfeedpicker3" yui3mods="node"}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        if (Y.one('#rsspull-feeds').get('children').size() > 1) Y.one('#norssfeeds').setStyle('display','none');
        Y.one('#addtolist').on('click', function(e){
            e.halt();
            var feedtoadd = Y.one("#feedmaker").get('value');
            if (feedtoadd == '') return;
            Y.one('#norssfeeds').setStyle('display', 'none');
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = feedtoadd + '    <input type="hidden" name="pull_rss[]" value="'+feedtoadd+'" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.className = "removerss btn {/literal}{$btn_size}{literal} btn-danger";
            newRemove.innerHTML = " {/literal}<i class='fas fa-times-circle {$icon_size}'></i> {'Remove'|gettext}{literal}";
            newli.appendChild(newLabel);
            newli.appendChild(newRemove);
            var list = Y.one('#rsspull-feeds');
            list.appendChild(newli);
            feedtoadd = '';
        });

        var remClick = function(e){
           if (confirm("{/literal}{'Are you sure you want to delete this url?'|gettext}{literal}")) {
                e.halt();
                var lItem = e.target.ancestor('li');
                var list = Y.one('#rsspull-feeds');
                list.removeChild(lItem);
                if (list.get('children').size() == 1) Y.one('#norssfeeds').setStyle('display', '');
           } else return false;
        };

        Y.one('#config').delegate('click',remClick,'a.removerss');
    });
    {/literal}
    {/script}
</div>
