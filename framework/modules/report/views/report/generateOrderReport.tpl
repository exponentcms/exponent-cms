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

{css unique="ecom-report1" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css" corecss="button,tables"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`css/generate-report.css"}

{/css}

<div class="module report generate-report">
    {$page->links}
    {form id="batch" controller=report}
        <div class="actions-to-apply">
            {control type="dropdown" name="action" label="Select Action"|gettext items=$action_items}
            {control type="checkbox" name="applytoall" label="Apply to all pages"|gettext class="applytoall" value=1}
            {*<button type="submit" class="{button_style}">{"Apply Batch Action"|gettext}</button>*}
            {control type="buttongroup" submit="Apply Batch Action"|gettext}
        </div>
        <div class="exp-skin-table">
            {$page->table}
        </div>
    {/form}
	{$page->links}
</div>