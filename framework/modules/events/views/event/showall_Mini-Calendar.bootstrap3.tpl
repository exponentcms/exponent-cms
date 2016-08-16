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

{script unique=$name jquery="moment,bootstrap-datetimepicker"}
{literal}
    $(document).ready(function() {
        var minical_{/literal}{$name}{literal} = $('#mini-{/literal}{$name}{literal}');

        // ajax load new month
        minical_{/literal}{$name}{literal}.delegate('a.evnav', 'click', function(e){
            e.preventDefault();
            // moving to a new month
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Month'},
                url: EXPONENT.PATH_RELATIVE + "index.php?controller=event&action=showall&view=minical&ajax_action=1&src={/literal}{$__loc->src}{literal}",
                data: "time=" + $(this)[0].rel,
                success: function(o, ioId){
                    if(o){
                        minical_{/literal}{$name}{literal}.html(o);
                        minical_{/literal}{$name}{literal}.find('script').each(function(k, n){
                            if(!$(n).attr('src')){
                                eval($(n).html);
                            } else {
                                $.getScript($(n).attr('src'));
                            };
                        });
                        minical_{/literal}{$name}{literal}.find('link').each(function(k, n){
                            $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                        });
                    } else {
                        $('#mini-{/literal}{$name}{literal}.loadingdiv').remove();
                        minical_{/literal}{$name}{literal}.html('Unable to load content');
                        minical_{/literal}{$name}{literal}.css('opacity', 1);
                    }
                }
            });
            minical_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Month"|gettext}{literal}'));
        });
    });
{/literal}
{/script}
