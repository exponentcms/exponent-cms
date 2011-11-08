{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

{css unique="migratefiles-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{css unique="migratefiles" corecss="tables"}

{/css}

<div class="module migration migrate-files">
    <div class="info-header">
        <div class="related-actions">
			{help text="Tips to Follow after Migrating Files"|gettext module="post-file-migration"}
        </div>
		<h1>{"File Migration Report"|gettext}</h1>	    
    </div>

    <p> 
		{'Note: this only properly copied over the records from the old database into the Exponent v2 database.
		Make sure you manually copy the "files" directory over to this installation.'|gettext}
    </p>
        <ul>
			<li class=\"mig-msg\">
				{'Emptied the expFiles table before the file import'|gettext}
			</li>
			<li class=\"mig-msg\">
				{$count} {'file records were imported'|gettext}
			</li>
			<li class=\"mig-msg\">
				{'You should now see all files from your previous system listed in your file manager'|gettext}
			</li>
			<li class=\"mig-msg\">
				{'HOWEVER, you must manually copy the \'files\' directory over to this installation'|gettext}
			</li>
        </ul>
    <p> 
		{'The following is a list of all file records migrated into the database.
		A checkmark indicates the file already resides in the destination \'files\' folder.'|gettext}
    </p>		
	<table class="exp-skin-table">
		<thead>
			<tr>
				<th width=5%>&nbsp;</th>
				<th width=30%>{"File Name"|gettext}</th>
				<th width=65%>{"Directory"|gettext}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$files item=file name=files}
				<tr class="{cycle values="even,odd"}">            
					<td width=5%>{if $file->exists}{img src=$smarty.const.ICON_RELATIVE|cat:'clean.png'}{else}<span style="color:red"><b>??</b></span>{/if}</td>
					<td width=30%>{$file->filename}</td>
					<td width=65%>{$file->directory}</td>
				</tr>
			{foreachelse}
				<tr><td colspan=3>{'No files found in the database'|gettext} {$config.database}</td></tr>
			{/foreach}
		</tbody>
	</table>
	<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=migration action=manage_content}"><b>{'Next Step -> Migrate Content'|gettext}</b></a>
</div>
