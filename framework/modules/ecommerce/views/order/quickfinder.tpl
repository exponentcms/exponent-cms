{css unique="ecom-report1" link="`$asset_path`/css/ecom.css"}

{/css} 
{css unique="ecom-report2" link="`$asset_path`/css/generate-report.css"}

{/css}

<div class="module report generate-report">
    {$page->links}
    {form id="batch" controller=report}
    <div class="exp-ecom-table">
        {$page->table}
    </div>
    {/form}
	{$page->links}
</div>