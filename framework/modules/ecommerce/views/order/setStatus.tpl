An order you placed on the {$storename} website has been updated.  The status of your order has been changed to {$to_status}.{br}
<hr>
Invoice: {$order->invoice_id}{br}
Update Date/Time: {$date} {br}
{if $comment != ''}
<hr>  
Notes: {br}
{$comment}
{/if}
{br}
{if $include_shipping == true}
<hr>
Your order was shipped on {$order->shipped|format_date:$smarty.const.DISPLAY_DATE_FORMAT}.{br}   
Carrier: {$carrier}{br}
Tracking Number: {$order->shipping_tracking_number}{br}{br}
{if $tracking_link != ''}
You may visit the shipping providers website to track your order by following this link:{br}
{$tracking_link}
{br}{br}
If your email client does not allow you to click on the link above, simply copy and paste the link into your web browsers address bar. {br}{br}
* TRACKING NOTE: Your tracking information may take a few hours to be available on the carriers website.  In addition, if your order is shipped over the weekend, your tracking information may not be available until Monday.
{/if}
{/if}
{br}{br}