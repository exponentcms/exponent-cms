{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom.css" corecss="button,tables"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`css/generate-report-bs3.css"}

{/css}

<div class="module report generate-report">
    {$page->links}
    {form id="batch" controller=batch}
        <div class="row actions-to-apply">
            {control type="dropdown" class="col-sm-4" name="action" label="Select Action"|gettext items="Add Notes,Delete,Apply Discount,Refund"|gettxtlist values="Add Notes,Delete,Apply Discount,Refund"}
            <div class="col-sm-offset-1 col-sm-3">{control type="checkbox" name="applytoall" label="Apply to all pages"|gettext class="applytoall" value=1}</div>
            <div class="col-sm-4">{control type="buttongroup" submit="Apply Batch Action"|gettext}</div>
        </div>
        <div class="row exp-skin-table">
            <div class="col-sm-12">
                {$page->table}
            </div>
        </div>
    {/form}
	{$page->links}
</div>