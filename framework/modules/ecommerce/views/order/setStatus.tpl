{'An order you placed on the'|gettext} {$storename} {'website has been updated.  The status of your order has been changed to'|gettext} {$to_status}.{br}
<hr>
{'Invoice'|gettext}: {$order->invoice_id}{br}
{'Update Date/Time'|gettext}: {$date} {br}
{if $comment != ''}
<hr>  
{'Notes'|gettext}: {br}
{$comment}
{/if}
{br}
{if $include_shipping == true}
<hr>
{'Your order was shipped on'|gettext} {$order->shipped|format_date}.{br}
{'Carrier'|gettext}: {$carrier}{br}
{'Tracking Number'|gettext}: {$order->shipping_tracking_number}{br}{br}
{if $tracking_link != ''}
{'You may visit the shipping providers website to track your order by following this link'|gettext}:{br}
{$tracking_link}
{br}{br}
{'If your email client does not allow you to click on the link above, simply copy and paste the link into your web browsers address bar.'|gettext} {br}{br}
* {'TRACKING NOTE: Your tracking information may take a few hours to be available on the carriers website.  In addition, if your order is shipped over the weekend, your tracking information may not be available until Monday.'|gettext}
{/if}
{/if}
{br}{br}