{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="list_bad_files" corecss="tables"}

{/css}

<div class="module files list-bad-files">
    <div class="info-header">
        <h2>{"Missing and Orphan Files"|gettext}</h2>
        <blockquote>
            {'This page shows all of the files in the database which should be on the server, but are missing.'|gettext}&#160;&#160;
            {'It also shows files found on the server which are not in the database.'|gettext}&#160;&#160;
            {'This report does NOT account for files referenced in WYSIWYG text since they were embedded by url and not by database file id.'|gettext}
        </blockquote>
    </div>
    <p>
        <strong>{'Database Files Missing on the Server'|gettext}: {$missing|count}</strong>
    </p>
    <table cellpadding="4" cellspacing="0" border="0" width="100%">
	    {foreach from=$missing item=file}
	    <tr>
		    <td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
			    {$file}
		    </td>
	    </tr>
        {foreachelse}
            <tr>
       		    <td>
                    <blockquote>
                    <h3>{'There seem to be no files in the database which are missing on the server!'|gettext}</h3>
                    </blockquote>
                </td>
       	    </tr>
	    {/foreach}
    </table>
    <p>
        <strong>{'Server Files Missing in the Database'|gettext}: {$orphan|count}</strong>
    </p>
    <table cellpadding="4" cellspacing="0" border="0" width="100%">
	    {foreach from=$orphan item=file}
	    <tr>
		    <td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
			    {$file}
		    </td>
	    </tr>
        {foreachelse}
            <tr>
       		    <td>
                    <blockquote>
                    <h3>{'There seem to be no files on the server which are missing in the database!'|gettext}</h3>
                    </blockquote>
                </td>
       	    </tr>
	    {/foreach}
    </table>
</div>
