{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{css unique="general-ecom" link="`$smarty.const.PATH_RELATIVE`framework/modules/ecommerce/assets/css/ecom-bs3.css" corecss="button,tables"}

{/css}
{css unique="ecom-report2" link="`$asset_path`css/generate-report-bs3.css"}

{/css}

<div class="module report generate-report">
    <h1>{'Order Reports'|gettext}</h1>
    {$page->links}
    {form id="batch" controller=report}
        <div class="row actions-to-apply">
            {control type="dropdown" class="col-sm-4" name="action" label="Select Action"|gettext items=$action_items}
            <div class="col-sm-offset-1 col-sm-3">{control type="checkbox" class="applytoall" name="applytoall" label="Apply to all pages"|gettext value=1}</div>
            <div class="col-sm-4">{control type="buttongroup" submit="Apply Batch Action"|gettext}</div>
        </div>
        <div class="row exp-ecom-table">
            <div class="col-sm-12">
                {$page->table}
            </div>
        </div>
    {/form}
	{$page->links}
</div>