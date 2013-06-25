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

{messagequeue}
{if $success == 1}
	<h2>{'Data was restored successfully from backup.'|gettext}</h2>
    <blockquote>{'If an upgrade notice is displayed above, please upgrade your restored database.'|gettext}</blockquote>
    <a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{$smarty.const.URL_FULL}">{'Take me to my home page'|gettext}</a>
{else}
	<h2>{'Some Errors were encountered trying to restore the database from the EQL file'|gettext}</h2>
    <ul style="list-style:disc;">
        {foreach from=$errors item=error}
            <li>{$error}</li>
        {/foreach}
    </ul>
    <h3>{'If some tables were not restored, most likely those tables were deprecated and no longer exist'|gettext}</h3>
    <blockquote>{'If we were unable to recreate them, here\'s what we recommend'|gettext}:</blockquote>
    <ol style="list-style:decimal;">
        <li>
            {'Download and extract the Exponent package corresponding to the \'from\' version in the upgrade notice above'|gettext}
            {br}
            <a target="_blank" href="https://sourceforge.net/projects/exponentcms/files/" class="download">{'Package Downloads'|gettext}</a>
        </li>
        <li>{'Run the installation/upgrade procedure'|gettext}</li>
        <li>{'Import the EQL file once again to restore the database'|gettext}</li>
        <li>{'Download and extract the most current package, as recommended by the upgrade notice'|gettext}</li>
        <li>{'Run the installation/upgrade procedure, as recommended by the upgrade notice'|gettext}</li>
        <li>{'Run the \'Export Data - EQL Database Exporter\' to save a copy of the freshly upgraded database'|gettext}</li>
    </ol>
{/if}
<hr>
<blockquote>{'If you have not yet imported your saved files (graphics, etc...), you may do so now.'|gettext}</blockquote>
<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link controller=file action=import_files}">{'Import Files Archive'|gettext}</a>
