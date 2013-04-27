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

{css unique="exporteql" corecss="forms,tables"}

{/css}

<div class="importer usercsv-add">
	<div class="form_header">
		<h2>{'Import Users - Users Imported'|gettext}</h2>
		<blockquote>{'The following users were added to the database.  If the user info is highlighted green, then the user was updated in the database.  If the user info is highlighted in red, that user record could not be added to the database due to errors.'|gettext}</blockquote>
	</div>
	<table cellspacing="0" cellpadding="2" border="0" width="100%" class="exp-skin-table">
        <thead>
            <th class="header importer_header">{'Status'|gettext}</th>
            <th class="header importer_header">{'Username'|gettext}</th>
            <th class="header importer_header">{'Password'|gettext}</th>
            <th class="header importer_header">{'First Name'|gettext}</th>
            <th class="header importer_header">{'Last Name'|gettext}</th>
            <th class="header importer_header">{'Email'|gettext}</th>
        </thead>
        <tbody>
            {foreach from=$userarray item=user}
                <tr class="row {cycle values='even,odd'}">
                    <td>
                        {if $user.changed == 1}<span style="color:green;">{'Updated'|gettext}</span>
                        {elseif $user.changed == "skipped"}<span style="color:red;">{'Ignored&#160;(Line&#160;%s)'|sprintf:$user.linenum})</span>
                        {else}<span style="color:black;">{'Added'|gettext}</span>
                        {/if}
                    </td>
                    <td>{$user.username}</td>
                    <td>{$user.clearpassword}</td>
                    <td>{$user.firstname}</td>
                    <td>{$user.lastname}</td>
                    <td>{$user.email}</td>
                </tr>
            {/foreach}
        </tbody>
	</table>
</div>
