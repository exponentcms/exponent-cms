{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

{css unique="formmod" corecss="forms"}

{/css}

<div class="formmodule default"> 
    {messagequeue}
	{permissions}
		<div class="module-actions">
			{if $permissions.viewdata == 1 && $form->is_saved == 1}<a class="addnew mngmntlink" href="{link action=view_data module=formbuilder id=$form->id}">{'View Data'|gettext} ({$count})</a>&nbsp;&nbsp;{/if}
			{if $permissions.viewdata == 1 && $form->is_saved == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=export_csv module=formbuilder id=$form->id}">{"Export CSV"|gettext}</a>&nbsp;&nbsp;
				{if $permissions.editformsettings == 1}|&nbsp;&nbsp;
				{/if}
			{/if}
			{if $permissions.editformsettings == 1}<a class="addnew mngmntlink" href="{link action=edit_form module=formbuilder id=$form->id}">{'Form Settings'|gettext}</a>&nbsp;&nbsp;{/if}
			{if $permissions.editform == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=view_form module=formbuilder id=$form->id}">{'Edit Form'|gettext}</a>&nbsp;&nbsp;{/if}
			{if $permissions.editreport == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=edit_report module=formbuilder id=$form->id}">{'Report Settings'|gettext}</a>&nbsp;&nbsp;{/if}
		</div>
	{/permissions}
	{if $moduletitle != ""}<h2>{$moduletitle}</h2>{/if}
	 <div class="bodycopy">
    	{if $description != ""}
    		{$description}
    	{/if}
		{$form_html}
	</div>
</div>