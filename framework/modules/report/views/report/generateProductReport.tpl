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

{css unique="ecom-report1" link="`$asset_path`css/ecom.css"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`css/generate-report.css"}

{/css}

<div class="module report generate-report">
    {$page->links}
    {form id="batch" controller=report}
    <div class="actions-to-apply">
        {control type="dropdown" name="action" label="Select Action"|gettext items=$action_items}
        {control type="checkbox" name="applytoall" label="Apply to all pages"|gettext class="applytoall" value=1}
        <a href="javascript:document.getElementById('batch').submit();" class="exp-ecom-link"><strong><em>Apply Batch Action</em></strong></a>
    </div>
    <div class="exp-ecom-table">
        {$page->table}
    </div>
    {/form}
	{$page->links}
</div>