<?php

##################################################
#
# Copyright (c) 2004-2008 OIC Group, Inc.
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
                'text'=>"Store Settings",
                'submenu'=>array(
                    'id'=>'store',
                    'itemdata'=>array(                        
                        array(
                            'text'=>"General Store Settings",
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'configure')),
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
                    ),                        
                ),
            ),
            array(
                'text'=>"Products & Categories",
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
                            'text'=>"Product Options",
                            'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'options')),
                        ),
                        array(
                            'text'=>"Manage Store Categories",
                            'url'=>makeLink(array('controller'=>'storeCategoryController','action'=>'manage')),
                        ),
                        array(
                            'text'=>"View Uncategories Products",
                            'url'=>makeLink(array('controller'=>'store','action'=>'showallUncategorized')),
                        ),
                    ),                        
                ),
            ),
            array(
                'text'=>"Payments & Shipping Settings",
                'submenu'=>array(
                    'id'=>'pay',
                    'itemdata'=>array(
                        array(
                            'text'=>"Payment Options",
                            'url'=>makeLink(array('controller'=>'billing','action'=>'manage')),
                        ),
                        array(
                            'text'=>"Shipping Options",
                            'url'=>makeLink(array('controller'=>'shipping','action'=>'manage')),
                        ),
                    ),                        
                ),
            ),
            // array(
            //     'text'=>"Discounts & Promos",
            //     'submenu'=>array(
            //         'id'=>'discount',
            //         'itemdata'=>array(
            //             array(
            //                 'text'=>"Discount Rules",
            //                 'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_discounts')),
            //             ),
            //             array(
            //                 'text'=>"Promo Codes",
            //                 'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_promocodes')),
            //             ),
            //             array(
            //                 'text'=>"Group Discounts",
            //                 'url'=>makeLink(array('controller'=>'ecomconfig','action'=>'manage_groupdiscounts')),
            //             ),
            //         ),                        
            //     ),
            // ),
        ),
    )
);

?>
