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

<div class="module-actions">
	{permissions}
		{if $permissions.viewdata == 1 && $form->is_saved == 1}<a class="addnew mngmntlink" href="{link _common=1 view='Default' action='show_view' module=formmodule id=$form->id}">{$_TR.enter_data}</a>&nbsp;&nbsp;{/if}
		{if $permissions.viewdata == 1 && $form->is_saved == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=export_csv module=formbuilder id=$form->id}">{"Export CSV"|gettext}</a>&nbsp;&nbsp;
			{if $permissions.editformsettings == 1}|&nbsp;&nbsp;
			{/if}
		{/if}
		{if $permissions.editformsettings == 1}<a class="addnew mngmntlink" href="{link action=edit_form module=formbuilder id=$form->id}">{$_TR.edit_settings}</a>&nbsp;&nbsp;{/if}
		{if $permissions.editform == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=view_form module=formbuilder id=$form->id}">{$_TR.edit_form}</a>&nbsp;&nbsp;{/if}
		{if $permissions.editreport == 1}|&nbsp;&nbsp;<a class="addnew mngmntlink" href="{link action=edit_report module=formbuilder id=$form->id}">{$_TR.edit_report}</a>&nbsp;&nbsp;{/if}
	{/permissions}
</div>

{chain module="formbuilder" action="view_data" params="array('id'=>`$form->id`)"}
