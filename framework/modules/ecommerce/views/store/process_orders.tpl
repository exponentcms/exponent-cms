<table class="exp-skin-table">
<thead>
<th colspan="2">{'Successful EOD Import Results'|gettext}</th>
</thead>
{foreach from=$successSet item=ss key=skey}
<tr>
<td style="border-top: 1px solid black;">
{'Row:'|gettext}{$skey}{br}
{'Order Id:'|gettext}{$ss.order_id}{br}
{if isset($ss.request_id)}{'Amount Charged:'|gettext}${$ss.amount|number_format:2}{br}{/if}
{'Shipping Via:'|gettext}{$ss.carrier}
</td>
<td style="border-top: 1px solid black;">
{if isset($ss.request_id)}{'Request Id:'|gettext}{$ss.request_id}{br}{/if}
{if isset($ss.reference_id)}{'Reference Id:'|gettext}{$ss.reference_id}{br}{/if}
{if isset($ss.authorization_code)}{'Authorization Code:'|gettext}{$ss.authorization_code}{br}{/if}
{'Tracking Number:'|gettext}{$ss.shipping_tracking_number}
</td>
</tr>
{if isset($ss.message)}    
    <tr>
        <td colspan="2">{$ss.message}</td>
    </tr>
{/if}
{/foreach}
</table>

<table class="exp-skin-table" style="color: red;">
<thead>
<th colspan="2">{'FAILED EOD Import Results'|gettext}</th>
</thead>
{foreach from=$errorSet item=es key=ekey}
   <tr>
<td style="border-top: 1px solid black;">
{'Row:'|gettext}{$ekey}{br}
{'Order Id:'|gettext}{$es.order_id}{br}
{if isset($es.shipping_tracking_number)}{'Tracking Number:'|gettext}{$es.shipping_tracking_number}{/if}
</td>
<td style="border-top: 1px solid black;">
{if isset($es.amount)}{'Amount:'|gettext}${$es.amount|number_format:2}{br}{/if}
{if isset($es.error_code)}{'Error Number:'|gettext}{$es.error_code}{/if}
</td>
<tr>
<td colspan="2">{$es.message}</td>
</tr>
{/foreach}
</table>