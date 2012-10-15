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
 
{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events cal-admin">
	<a href="{link action=showall}">{'Month View'|gettext}</a>{br}
	<h1>
        {ical_link}
        {if $moduletitle && !$config->hidemoduletitle}{$moduletitle}{/if}
	</h1>
    {if $config->moduledescription != ""}
        {$config->moduledescription}
    {/if}
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		{foreach from=$items item=item}
			{if $item->is_featured == 1}
				<tr>
					<td width="80" style="padding:3px 0px 3px 0px;">
						{if $item->image_path}
							<img src="{$item->image_path}" border="0" width="80" height="80"/>
						{else}
							&#160;
						{/if}
					</td>
					<td style="padding:3px 10px 3px 10px;">
						<table width=100% cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td>
                                    <a class="mngmntlink calendar_mngmntlink"
                                        {if $item->location_data != null}
                                            href="{if $item->location_data != 'event_registration'}{link action=show id=$item->eventdate->id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                                        {/if}
                                        >{$item->title}
                                    </a>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									{$item->body|summarize:html:paralinks}
								</td>
							</tr>
							<tr>
								<td align="right">
									{permissions}
                                        {if $item->location_data != null}
                                            <div class="item-actions">
                                                {if $permissions.edit == 1}
                                                    {icon action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
                                                {/if}
                                                {if $permissions.delete == 1}
                                                    {if $item->is_recurring == 0}
                                                        {icon action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
                                                    {else}
                                                        {icon action=delete_form class=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
                                                    {/if}
                                                {/if}
                                            </div>
                                        {/if}
									{/permissions}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
		{foreachelse}
			<table cellspacing="0" cellpadding="4" border="1" width="100%">
				<tr><td align="center"><em>{'No Events'|gettext}</em></td></tr>
			</table>
		{/foreach}
	</table>
	{permissions}
		{if $permissions.create == 1}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
		{br}
	{/permissions}
</div>