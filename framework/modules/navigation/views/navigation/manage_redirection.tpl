{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{css unique="standalone" corecss="tables"}

{/css}

<div class="module navigation manager-redirection">
	<div class="form_header">
		<blockquote>
            {'Normally requests for non-existent pages result in a Not Found response.'|gettext}
            {'However, in some cases you may want to handle those requests by redirecting the user to another page.'|gettext}
            {'This would especially be desired if a page is renamed.'|gettext}
        </blockquote>
		{icon class="add" action=edit_redirection text='Create a New Page Redirection'|gettext}
        {icon class="manage" action=manage_redirection_log text='Display Redirected Pages'|gettext}
	</div>
    <table cellpadding="2" cellspacing="0" border="0" width="100%" class="exp-skin-table">
        <thead>
            <tr>
                <th><strong>{'Redirect From'|gettext}</strong></th>
                <th><strong>{'Redirect To'|gettext}</strong></th>
                <th><strong>{'HTTP Status Code'|gettext}</strong></th>
                <th><strong>{'Actions'|gettext}</strong></th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$redirects item=section}
                <tr class="{cycle values='odd,even'}"><td>
                    {$section->old_sef_name}
                </td><td>
                    {$section->new_sef_name}
                </td><td>
                    {$section->type}
                </td><td>
                    {icon class=edit action=edit_redirection record=$section title='Edit'|gettext}
                    {icon class=delete action=delete_redirection record=$section title='Delete'|gettext onclick="return confirm('"|cat:("Delete this redirection?"|gettext)|cat:"');"}
                </td></tr>
            {foreachelse}
                <tr><td colspan=4><em>{'No redirections found'|gettext}</em></td></tr>
            {/foreach}
        </tbody>
    </table>
</div>
