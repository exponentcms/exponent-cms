<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

if (!defined('EXPONENT'))
    exit('');

$active = ECOM;
if (empty($active))
    return false;

$new_orders = order::getOrdersCount('new');
$open_orders = order::getOrdersCount('open');
if ($new_orders > 0) {
    $newo = '<em class="newalert">' . $new_orders . ' ' . gt('new order') . ($new_orders>1?'s':'') . '</em>';
} else {
    $newo = '';
};
// get latest 5 new orders
$new_status = order::getDefaultOrderStatus();
$order = new order();
$recent_orders = $order->find('all', 'purchased !=0 AND order_status_id = ' . $new_status, 'purchased DESC', 5);

/////////////////////////////////////////////////////////////////////////
// BUILD THE MENU
/////////////////////////////////////////////////////////////////////////

$items1 = array(
    array(
        'text' => $newo . '<form role="form" id="orderQuickfinder" method="POST" action="' . PATH_RELATIVE . 'index.php" enctype="multipart/form-data"><input type="hidden" name="controller" value="order"><input type="hidden" name="action" value="quickfinder"><input class="form-control" type="text" name="ordernum" id="ordernum" aria-label="'.gt('order number').'" size="25" placeholder="' . gt(
                "Order Quickfinder"
            ) . '"></form>',
        'info' => '1',
        'classname' => 'order-qf',
    ),
);
$items1[] = array(
    'text' => gt("Manage Orders") . " <em>(" . $open_orders . "  " . gt(
            "Open Orders"
        ) . ")</em>",
    'icon' => 'fa-search',
    'icon5' => 'fas fa-search',
    'iconbs' => 'bi-search',
    'classname' => 'search',
    'url' => makeLink(
        array(
            'controller' => 'order',
            'action' => 'showall'
        )
    ),
);
$items1[] = array(
    'text' => gt("Create an Order"),
    'icon' => 'fa-plus-circle',
    'icon5' => 'fas fa-plus-circle',
    'iconbs' => 'bi-plus-circle',
    'classname' => 'add',
    'url' => makeLink(
        array(
            'controller' => 'order',
            'action' => 'create_new_order'
        )
    ),
    'divider' => count($recent_orders),
);

$items2 = array();
foreach ($recent_orders as $ord) {
    $state = strtolower($ord->billingmethod[0]->transaction_state);
    if ($state === 'complete' || $state === 'paid') {
        if (bs4() || bs5()) {
            $badge = 'badge-success">';
        } else {
            $badge = 'alert-success">';
        }
    } else {
        if (bs4() || bs5()) {
            $badge = 'badge-secondary">';
        } else {
            $badge = '">';
        }
    }
    $items2[] = array(
        'text' => count($ord->orderitem) . ' ' . gt('item') . (count($ord->orderitem) > 1 ? 's' : '') . ' ' . gt('ordered on') . ' ' . expDateTime::format_date($ord->purchased) .
            ' <span class="badge ' . $badge . expCore::getCurrency($ord->grand_total) . '</span>',
        'icon' => 'fa-file text-success',
        'icon5' => 'fas fa-file text-success',
        'iconbs' => 'bi-file-earmark text-success',
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

if (bs3() || bs4() || bs5()) {
    $items = array_merge($items1, $items2);
} else {
    $items = array($items1, $items2);
}
return array(
    'text' => ' <span class="orders '.(bs4()?'badge badge':'label label').'-success">' . $new_orders . '</span>',
    'icon' => 'fa-list-ul',
    'icon5' => 'fas fa-list-ul',
    'iconbs' => 'bi-card-list',
    'classname' => 'order',
    'submenu' => array(
        'id' => 'orders2',
        'itemdata' => $items,
    )
);

?>
