{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{uniqueid prepend="cal" assign="name"}

{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events mini-cal">
    <div id="mini-{$name}">
        {exp_include file='minical.tpl'}
    </div>
    {if !$config.disable_links}
        {icon class="monthviewlink" action=showall time=$now text='View Calendar'|gettext}
    	{br}
    {/if}
	{permissions}
		{if $permissions.create}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>

{script unique=$name yui3mods="node,io,node-event-delegate"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var minical_{/literal}{$name}{literal} = Y.one('#mini-{/literal}{$name}{literal}');

	var handleSuccess = function(ioId, o){
        if(o.responseText){
            minical_{/literal}{$name}{literal}.setContent(o.responseText);
            minical_{/literal}{$name}{literal}.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            minical_{/literal}{$name}{literal}.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
        } else {
            Y.one('#mini-{/literal}{$name}{literal}.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "minical nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    minical_{/literal}{$name}{literal}.delegate('click', function(e){
        e.halt();
        var cfg = {
            method: "POST",
            headers: { 'X-Transaction': 'Load Minical'},
            arguments : { 'X-Transaction': 'Load Minical'},
            data : "time="+e.currentTarget.get('rel'),
        };
        var request = Y.io(EXPONENT.PATH_RELATIVE+"index.php?controller=event&action=showall&view=minical&ajax_action=1&src={/literal}{$__loc->src}{literal}", cfg);
        minical_{/literal}{$name}{literal}.setContent(Y.Node.create('{/literal}{loading title="Loading Month"|gettext}{literal}'));
    }, 'a.evnav');
});
{/literal}
{/script}
