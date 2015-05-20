<?php

##################################################
#
# Copyright (c) 2004-2015 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

if (!defined('EXPONENT')) {
    exit('');
}

$active = ECOM;
if (empty($active)) {
    return false;
}

$new_orders = order::getOrdersCount('new');
$open_orders = order::getOrdersCount('open');
if ($new_orders > 0) {
    $newo = '<em class="newalert">' . $new_orders . ' ' . gt('new orders') . '</em>';
} else {
    $newo = '';
};
// get latest 5 orders
$new_status = order::getDefaultOrderStatus();
$order = new order();
$recent_orders = $order->find('all', 'purchased !=0 AND order_status_id = ' . $new_status, 'purchased DESC', 5);

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items = array(
    array(
        'text' => $newo . '<form role="form" id="orderQuickfinder" method="POST" action="' . PATH_RELATIVE . 'index.php" enctype="multipart/form-data"><input type="hidden" name="controller" value="order"><input type="hidden" name="action" value="quickfinder"><input class="form-control" style="padding-top: 3px;" type="text" name="ordernum" id="ordernum" size="25" placeholder="' . gt(
                "Order Quickfinder"
            ) . '"></form>',
        'info' => '1',
        'classname' => 'order-qf',
    ),
);
$items[] = array(
    'text' => gt("Manage Orders") . " <em>(" . $open_orders . "  " . gt(
            "Open Orders"
        ) . ")</em>",
    'icon' => 'fa-search',
    'classname' => 'search',
    'url' => makeLink(
        array(
            'controller' => 'order',
            'action' => 'showall'
        )
    ),
);
$items[] = array(
    'text' => gt("Create Order"),
    'icon' => 'fa-plus-circle',
    'classname' => 'add',
    'url' => makeLink(
        array(
            'controller' => 'order',
            'action' => 'create_new_order'
        )
    ),
    'divider' => true,
);

foreach ($recent_orders as $ord) {
    $items[] = array(
        'text' => count($ord->orderitem) . ' ' . gt('item') . (count($ord->orderitem) > 1 ? 's' : '') . ' ' . gt(
                'ordered on'
            ) . ' ' . expDateTime::format_date($ord->purchased) . ' <span class="badge ' . ((strtolower(
                    $ord->billingmethod[0]->transaction_state
                ) == 'complete' || strtolower(
                    $ord->billingmethod[0]->transaction_state
                ) == 'paid') ? 'alert-success">' : '">') . expCore::getCurrencySymbol() . $ord->grand_total . '</span>',
        'icon' => 'fa-file text-success',
        'classname' => 'search',
        'url' => makeLink(
            array(
                'controller' => 'order',
                'action' => 'show',
                'id' => $ord->id,
            )
        ),
    );
}

expCSS::pushToHead(
    array(
        "unique" => 'order_note',
        "css" => '
   .navbar .nav>li>a>.orders.label {
       position: absolute;
       top: 9px;
       text-align: center;
       font-size: 9px;
       padding: 2px 3px;
       line-height: .9;
   }
    .exp-skin .navbar-inverse .badge.alert-success {
      background-color: #dff0d8;
      border-color: #d6e9c6;
      color: #468847;
    }',
    )
);

return array(
    'text' => ' <span class="orders label label-success">' . $new_orders . '</span>',
    'icon' => 'fa-list-ul',
    'classname' => 'order',
    'submenu' => array(
        'id' => 'orders',
        'itemdata' => $items,
    )
);

?>
