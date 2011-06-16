<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

$i18n = exponent_lang_loadFile('modules/administrationmodule/tasks/coretasks.php');

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

return array(
    'text'=>'Ecommerce'.$newo,
    'classname'=>'ecom',
    'submenu'=>array(
        'id'=>'ecomm',
        'itemdata'=>array(
            array(
                'text'=>"View Orders <em>(".$new_orders."  New Orders)",
                'url'=>makeLink(array('controller'=>'order','action'=>'showall')),
            ),
            array(
                'text'=>"Create Order",
                'url'=>makeLink(array('controller'=>'order','action'=>'create_new_order')),
            ),            
            array(
                'text'=>"Dashboard",
                'url'=>makeLink(array('controller'=>'report','action'=>'dashboard')),
            ),
            array(
                'text'=>"Store Settings",
                'submenu'=>array(
                    'id'=>'store',
                    'itemdata'=>array(                        
                        array(
                            'text'=>"General Store Settings",
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'configure')),
                        ),
                        array(
                            'text'=>"General Cart Settings",
                            'url'=>makeLink(array('controller'=>'cart','action'=>'configure')),
                        ),
                        array(
                            'text'=>"Manage Status Codes",
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Status Messages",
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage_messages')),
                        ),
                        array(
                            'text'=>"Manage Order Types",
                            'url'=>makeLink(array('controller'=>'order_type','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Sales Reps",
                            'url'=>makeLink(array('controller'=>'sales_rep','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>"Products, Categories, & Manufacturers",
                'submenu'=>array(
                    'id'=>'prodscats',
                    'itemdata'=>array(
                        array(
                            'text'=>"Add a Product",
                            'url'=>makeLink(array('controller'=>'store','action'=>'create')),
                        ),
                        array(
                            'text'=>"Manage Products",
                            'url'=>makeLink(array('controller'=>'store','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Import Products",
                            'url'=>makeLink(array('controller'=>'importexport','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Product Statuses",
                            'url'=>makeLink(array('controller'=>'product_status','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Product Options",
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'options')),
                        ),
                        array(
                            'text'=>"Manage Store Categories",
                            'url'=>makeLink(array('controller'=>'storeCategoryController','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Manufacturers",
                            'url'=>makeLink(array('controller'=>'companyController','action'=>'manage')),
                        ),
                        
                    ),                        
                ),
            ),
            array(
                'text'=>"Payment & Shipping Settings",
                'submenu'=>array(
                    'id'=>'pay',
                    'itemdata'=>array(
                        array(
                            'text'=>"Manage Payment Options",
                            'url'=>makeLink(array('controller'=>'billing','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Manage Shipping Options",
                            'url'=>makeLink(array('controller'=>'shipping','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
			array(
                'text'=>"Email Messages",
                'submenu'=>array(
                    'id'=>'discount',
                    'itemdata'=>array(
                        array(
                            'text'=>"Manage Email",
                            'url'=>makeLink(array('controller'=>'order_status','action'=>'manage_messages')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>"Discounts & Promos",
                'submenu'=>array(
                    'id'=>'discount',
                    'itemdata'=>array(
                        array(
                            'text'=>"Manage Discounts",
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_discounts')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>"Reports & Activities",
                'submenu'=>array(
                    'id'=>'reports',
                    'itemdata'=>array(
                        array(
                            'text'=>"View Uncategorized Products",
                            'url'=>makeLink(array('controller'=>'store','action'=>'showallUncategorized')),
                        ),
                        array(
                            'text'=>"View Improperly Categorized Products",
                            'url'=>makeLink(array('controller'=>'store','action'=>'showallImpropercategorized')),
                        ),
                        array(
                            'text'=>"Build an Order Report",
                            'url'=>makeLink(array('controller'=>'report','action'=>'order_report')),
                        ),
                        array(
                            'text'=>"Build a Product Report",
                            'url'=>makeLink(array('controller'=>'report','action'=>'product_report')),
                        ),
                        array(
                            'text'=>"Batch Process Orders",
                            'url'=>makeLink(array('controller'=>'store','action'=>'batch_process')),
                        ),
                         array(
                            'text'=>"Import External Addresses",
                            'url'=>makeLink(array('controller'=>'store','action'=>'import_external_addresses')),
                        ),
                    ),                        
                ),
            ),
        ),
    )
);

?>
