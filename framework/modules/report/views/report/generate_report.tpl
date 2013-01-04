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

{css unique="ecom-report1" link="`$asset_path`/css/ecom.css" corecss="button,tables"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`/css/generate-report.css"}

{/css}

<div class="module report generate-report">
    {$page->links}
    {form id="batch" controller=batch}
    <div class="actions-to-apply">
        {control type="dropdown" name="action" label="Select Action"|gettext items="Add Notes,Delete,Apply Discount,Refund"|gettxtlist values="Add Notes,Delete,Apply Discount,Refund"}
        {control type="checkbox" name="applytoall" label="Apply to all pages"|gettext class="applytoall" value=1}
        <button type="submit" class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}">{"Apply Batch Action"|gettext}</button>
    </div>
    <div class="exp-skin-table">
        {$page->table}
    </div>
    {/form}
	{$page->links}
</div>