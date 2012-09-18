<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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

if (!defined('EXPONENT')) exit('');

if (!$user->isAdmin()) return false;

global $db;
// hide the menu if the store controller isn't activated
$active = $db->selectValue('modstate', 'active', 'module="storeController"');
if (empty($active)) return false;

$new_orders = $db->countObjects('orders', 'purchased !=0 AND order_status_id = 1');
// $new_orders = 420; // for testing
if ($new_orders>0) {
    $newo = '<em class="newalert">'.$new_orders.' new</em>';
}else{
    $newo = '';
};

$ecom = array(
    'text'=>gt('Ecommerce').$newo.'<form id="orderQuickfinder" method="POST" action="'.PATH_RELATIVE.'index.php" enctype="multipart/form-data"><input type="hidden" name="controller" value="order"><input type="hidden" name="action" value="quickfinder"><input style="padding-top: 3px;" type="text" name="ordernum" id="ordernum" size="25" value="'.gt("Order Quickfinder").'" onclick="this.value=\'\';"></form>',
    'classname'=>'ecom',
    'submenu'=>array(
        'id'=>'ecomm',
        'itemdata'=>array(
            array(
                'text'=>gt("Dashboard"),
                'url'=>makeLink(array('controller'=>'report','action'=>'dashboard')),
            ),
            array(
                'text'=>gt("View Orders")." <em>(".$new_orders."  ".gt("New Orders").")",
                'url'=>makeLink(array('controller'=>'order','action'=>'showall')),
            ),
            array(
                'text'=>gt("Create Order"),
                'url'=>makeLink(array('controller'=>'order','action'=>'create_new_order')),
            ),            
            array(
                'text'=>gt("Vendors & Purchase Orders"),
                'submenu'=>array(
                    'id'=>'purchase-order',
                    'itemdata'=>array(                        
                        array(
                            'text'=>gt("Create Purchase Order"),
                            'url'=>makeLink(array('controller'=>'purchaseOrder','action'=>'edit')),
                        ),
                        array(
                            'text'=>gt("Manage Purchase Orders"),
                            'url'=>makeLink(array('controller'=>'purchaseOrder','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Vendors"),
                            'url'=>makeLink(array('controller'=>'purchaseOrder','action'=>'manage_vendors')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>gt("Store Settings"),
                'submenu'=>array(
                    'id'=>'store',
                    'itemdata'=>array(                        
                        array(
                            'text'=>gt("General Store Settings"),
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'configure')),
                        ),
                        array(
                            'text'=>gt("General Cart Settings"),
                            'url'=>makeLink(array('controller'=>'cart','action'=>'configure')),
                        ),
                        array(
                            'text'=>gt("Address/Geo Settings"),
                            'url'=>makeLink(array('controller'=>'address','action'=>'manage')),
                        ),
						array(
                            'text'=>gt("Manage Up Charge Rate"),
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_upcharge')),
                        ),
                        array(
                            'text'=>gt("Manage Tax Classes"),
                            'url'=>makeLink(array('controller'=>'tax','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Status Codes"),
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Status Messages"),
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage_messages')),
                        ),
                        array(
                            'text'=>gt("Manage Order Types"),
                            'url'=>makeLink(array('controller'=>'order_type','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Sales Reps"),
                            'url'=>makeLink(array('controller'=>'sales_rep','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>gt("Products, Categories, & Manufacturers"),
                'submenu'=>array(
                    'id'=>'prodscats',
                    'itemdata'=>array(
                        array(
                            'text'=>gt("Add a Product"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'create')),
                        ),
                        array(
                            'text'=>gt("Manage Products"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Import Products"),
                            'url'=>makeLink(array('controller'=>'importexport','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Product Statuses"),
                            'url'=>makeLink(array('controller'=>'product_status','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Product Options"),
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'options')),
                        ),
                        array(
                            'text'=>gt("Manage Store Categories"),
                            'url'=>makeLink(array('controller'=>'storeCategoryController','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Manufacturers"),
//                            'url'=>makeLink(array('controller'=>'companyController','action'=>'manage')),
                            'url'=>makeLink(array('controller'=>'companyController','action'=>'showall')),
                        ),
                        
                    ),                        
                ),
            ),
            array(
                'text'=>gt("Payment & Shipping Settings"),
                'submenu'=>array(
                    'id'=>'pay',
                    'itemdata'=>array(
                        array(
                            'text'=>gt("Manage Payment Options"),
                            'url'=>makeLink(array('controller'=>'billing','action'=>'manage')),
                        ),
                        array(
                            'text'=>gt("Manage Shipping Options"),
                            'url'=>makeLink(array('controller'=>'shipping','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
			array(
                'text'=>gt("Email Messages"),
                'submenu'=>array(
                    'id'=>'emailmessages',
                    'itemdata'=>array(
                        array(
                            'text'=>gt("Manage Email"),
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage_messages')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>gt("Discounts & Promos"),
                'submenu'=>array(
                    'id'=>'discount',
                    'itemdata'=>array(
                        array(
                            'text'=>gt("Manage Discounts"),
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_discounts')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>gt("Reports & Activities"),
                'submenu'=>array(
                    'id'=>'reports',
                    'itemdata'=>array(
                        array(
                            'text'=>gt("View Uncategorized Products"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'showallUncategorized')),
                        ),
                        array(
                            'text'=>gt("View Improperly Categorized Products"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'showallImpropercategorized')),
                        ),
                        array(
                            'text'=>gt("View Products with Data Issues"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'nonUnicodeProducts')),
                        ),
                        array(
                            'text'=>gt("Build an Order Report"),
                            'url'=>makeLink(array('controller'=>'report','action'=>'order_report')),
                        ),
                        array(
                            'text'=>gt("Build a Product Report"),
                            'url'=>makeLink(array('controller'=>'report','action'=>'product_report')),
                        ),
                        array(
                            'text'=>gt("Batch Process Orders"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'batch_process')),
                        ),
                         array(
                            'text'=>gt("Import External Addresses"),
                            'url'=>makeLink(array('controller'=>'store','action'=>'import_external_addresses')),
                        ),
						 array(
                            'text'=>gt("View All Search Queries"),
                            'url'=>makeLink(array('controller'=>'search','action'=>'searchQueryreport')),
                        ),
						 array(
                            'text'=>gt("Top Search Queries Report"),
                            'url'=>makeLink(array('controller'=>'search','action'=>'topSearchReport')),
                        ),
                    ),                        
                ),
            ),
        ),
    )
);
// $ecom[] = array(
//     'text'=>'<form id="orderQuickfinder" method="POST" action="/index.php" enctype="multipart/form-data"><input type="hidden" name="controller" value="order"><input type="hidden" name="action" value="quickfinder"><input style="padding-top: 3px;" type="text" name="ordernum" id="ordernum" size="25" value="Order Quickfinder" onclick="this.value=\'\';"></form>',
//     'classname'=>'order',    
// );
return $ecom;
?>
