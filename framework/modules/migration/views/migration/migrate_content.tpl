{css unique="migcont" corecss="tables"}

{/css}

<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
    <thead>
        <tr>
            <th>
                {"Migration Report"|gettext}
                {if $msg.clearedcontent}
                    {br} After clearing the database of content:
                {/if}
            </th>
        </tr>
    </thead>
    <tbody>
		<tr><td>Migrated {$msg.locationref} total locations and {$msg.container} total containers which included:</td></tr>
        {foreach from=$msg.migrated item=val key=key}
        <tr class="{cycle values="odd,even"}">
            <td>
				{if $key == $val.name}
					<strong>{$val.count}</strong> record{if $val.count==1}s{/if} from <strong>{$key}</strong> {if $val.count==1}have{else}has{/if} been migrated as is</strong>
				{else}
					<strong>{$val.count}</strong> record{if $val.count==1}s{/if} from <strong>{$key}</strong> {if $val.count==1}have{else}has{/if} been migrated to <strong>{$val.name}</strong>
				{/if}
			</td>
        </tr>
        {/foreach}
	</tbody>
</table>