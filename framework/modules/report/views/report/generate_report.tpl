{css unique="ecom-report1" link="`$asset_path`/css/ecom.css"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`/css/generate-report.css"}

{/css}

<div class="module report generate-report">
    {pagelinks paginate=$page top=1}
    {form id="batch" controller=batch}
    <div class="actions-to-apply">
        {control type="dropdown" name="action" label="Select Action" items="Add Notes,Delete,Apply Discount,Refund"}
        {control type="checkbox" name="applytoall" label="Apply to all pages" class="applytoall" value=1}
        <a href="javascript:document.getElementById('batch').submit();" class="exp-ecom-link"><strong><em>Apply Batch Action</em></strong></a>
    </div>
    <div class="exp-ecom-table">
        {$page->table}
    </div>
    {/form}
    {pagelinks paginate=$page bottom=1}
</div>