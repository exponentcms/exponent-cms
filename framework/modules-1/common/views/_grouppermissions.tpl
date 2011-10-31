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

<div class="module common group-permissions">
    <div class="form_header">
		<div class="info-header">
			<div class="related-actions">
				{help text="Get Help"|gettext|cat:" "|cat:("Managing Group Permissions"|gettext) module="manage-group-permissions"}
			</div>
			<h1>{'Assign Group Permissions'|gettext}</h1>
		</div>
        <p>{'This form allows you to assign permissions to an entire group of users.'|gettext}</p>
    </div>
	{capture assign="file"}{$smarty.const.BASE}framework/modules-1/common/views/_permissions.tpl{/capture}
	{include file=$file}
</div>
