An order you placed on the {$storename} has been updated.  The status of your order has been changed from {$from_status} to {$to_status}{br}
<hr>
Invoice: {$order->invoice_id}{br}
Update Date/Time: {$date} {br}
<hr>
Notes: {br}
{$comment}

{if $include_shipping == true}
<hr>
Your order was shipped on {$order->shipped|format_date:$smarty.const.DISPLAY_DATE_FORMAT}.   The tracking number is {$order->shipping_tracking_number}.
{/if}

