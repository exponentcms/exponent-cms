<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

global $user;

$active = ECOM;
if (!$user->isAdmin() || empty($active))
    return false;

$ecom = array(
    'text'      => gt('E-commerce'),
    'icon'      => 'fa-dollar',
    'icon5'      => 'fas fa-dollar-sign',
    'iconbs'      => 'bi-currency-dollar',
    'classname' => 'ecom',
    'submenu'   => array(
        'id'       => 'ecomm',
        'itemdata' => array(
            array(
                'text'      => gt("Dashboard"),
                'icon'      => 'fa-dashboard',
                'icon5'      => 'fas fa-tachometer-alt',
                'iconbs'      => 'bi-speedometer',
                'classname' => 'dashboard',
                'url'       => makeLink(
                    array(
                        'controller' => 'report',
                        'action'     => 'dashboard'
                    )
                ),
            ),
            array(
                'text'      => gt('Customers'),
                'icon'      => 'fa-user',
                'icon5'      => 'fas fa-user',
                'iconbs'      => 'bi-person',
                'classname' => 'euser',
                'url'       => makeLink(
                    array(
                        'controller' => 'users',
                        'action'     => 'manage'
                    )
                ),
            ),
            array(
                'text'      => gt("Orders"),
                'icon'      => 'fa-list-ul',
                'icon5'      => 'fas fa-list-ul',
                'iconbs'      => 'bi-list-ul',
                'classname' => 'orders',
                'submenu'   => array(
                    'id'       => 'ordermenu',
                    'itemdata' => array(
                        array(
                            'text'      => gt("Manage Orders"),
                            'icon'      => 'fa-search',
                            'icon5'      => 'fas fa-search',
                            'iconbs'      => 'bi-search',
                            'classname' => 'search',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'order',
                                    'action'     => 'showall'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Batch Process Orders"),
                            'icon'      => 'fa-cogs',
                            'icon5'      => 'fas fa-cogs',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'config',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'batch_process'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Order Status Codes"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'order_status',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Order Types"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'order_type',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Order Status Email Messages"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'order_status',
                                    'action'     => 'manage_messages'
                                )
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'text'      => gt("Products"),
                'icon'      => 'fa-tag',
                'icon5'      => 'fas fa-tag',
                'iconbs'      => 'bi-tag',
                'classname' => 'products',
                'submenu'   => array(
                    'id'       => 'prodscats',
                    'itemdata' => array(
                        array(
                            'text'      => gt("Manage Products"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Add a Product"),
                            'icon'      => 'fa-plus-circle',
                            'icon5'      => 'fas fa-plus-circle',
                            'iconbs'      => 'bi-plus-circle',
                            'classname' => 'add',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'create'
                                )
                            ),
                        ),
//                        array(
//                            'text'      => gt("Export Products"),
//                            'icon'      => 'fa-download',
//                            'icon5'      => 'fas fa-download',
//                            'classname' => 'export',
////                            'url'       => makeLink(array('controller' => 'importexport', 'action' => 'manage')),
//                            'url'       => makeLink(
//                                array(
//                                    'controller' => 'store',
//                                    'action'     => 'export'
//                                )
//                            ),
//                        ),
                        array(
                            'text'      => gt("Import Products"),
                            'icon'      => 'fa-upload',
                            'icon5'      => 'fas fa-upload',
                            'iconbs'      => 'bi-upload',
                            'classname' => 'import',
//                            'url'       => makeLink(array('controller' => 'importexport', 'action' => 'manage')),
                            'url'       => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'import'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Store Categories"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'storeCategory',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Manufacturers"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
//                            'url'=>makeLink(array('controller'=>'company','action'=>'manage')),
                            'url'       => makeLink(
                                array(
                                    'controller' => 'company',
                                    'action'     => 'showall'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Product Statuses"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'product_status',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Product Options"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'ecomconfig',
                                    'action'     => 'options'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt('Manage Definable Fields'),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'expDefinableField',
                                    'action'     => 'manage'
                                )
                            )
                        ),
                    ),
                ),
            ),
            array(
                'text'      => gt("Reports"),
                'icon'      => 'fa-bar-chart-o',
                'icon5'      => 'far fa-chart-bar',
                'iconbs'      => 'bi-file-bar-graph',
                'classname' => 'reports',
                'submenu'   => array(
                    'id'       => 'reports',
                    'itemdata' => array(
                        array(
                            'text' => gt("View Uncategorized Products"),
                            'icon' => 'fa-search',
                            'icon5' => 'fas fa-search',
                            'iconbs' => 'bi-search',
                            'url'  => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'showallUncategorized'
                                )
                            ),
                        ),
                        array(
                            'text' => gt("View Improperly Categorized Products"),
                            'icon' => 'fa-search',
                            'icon5' => 'fas fa-search',
                            'iconbs' => 'bi-search',
                            'url'  => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'showallImpropercategorized'
                                )
                            ),
                        ),
                        array(
                            'text' => gt("View Products with Data Issues"),
                            'icon' => 'fa-search',
                            'icon5' => 'fas fa-search',
                            'iconbs' => 'bi-search',
                            'url'  => makeLink(
                                array(
                                    'controller' => 'store',
                                    'action'     => 'nonUnicodeProducts'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Build an Order Report"),
                            'icon'      => 'fa-cogs',
                            'icon5'      => 'fas fa-cogs',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'development',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'report',
                                    'action'     => 'order_report'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Build a Product Report"),
                            'icon'      => 'fa-cogs',
                            'icon5'      => 'fas fa-cogs',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'development',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'report',
                                    'action'     => 'product_report'

                                )
                            ),
                        ),
                    ),
                ),
            ),
//            array(
//                'text'    => gt("Purchase Orders"),
//                'classname' => 'purchase',
//                'submenu' => array(
//                    'id'       => 'purchase-order',
//                    'itemdata' => array(
//                        array(
//                            'text' => gt("Create Purchase Order"),
//                            'classname' => 'add',
//                            'url'  => makeLink(array('controller' => 'purchaseOrder', 'action' => 'edit')),
//                        ),
//                        array(
//                            'text'      => gt("Manage Purchase Orders"),
//                            'classname' => 'manage',
//                            'url'       => makeLink(array('controller' => 'purchaseOrder', 'action' => 'manage')),
//                        ),
//                        array(
//                            'text'      => gt("Manage Vendors"),
//                            'classname' => 'manage',
//                            'url'       => makeLink(array('controller' => 'purchaseOrder', 'action' => 'manage_vendors')),
//                        ),
//                    ),
//                ),
//            ),
            array(
                'text'      => gt("Store Setup"),
                'icon'      => 'fa-gear',
                'icon5'      => 'fas fa-cog',
                'iconbs'      => 'bi-gear',
                'classname' => 'configure',
                'submenu'   => array(
                    'id'       => 'store',
                    'itemdata' => array(
                        array(
                            'text'      => gt("Store General Settings"),
                            'icon'      => 'fa-cogs',
                            'icon5'      => 'fas fa-cogs',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'configure',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'ecomconfig',
                                    'action'     => 'configure'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Locations"),
                            'icon'      => 'fa-cogs',
                            'icon5'      => 'fas fa-cogs',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'address',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Location Up-Charge Rates"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'ecomconfig',
                                    'action'     => 'manage_upcharge'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Taxes"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'tax',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Sales Reps"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'sales_rep',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Payment Options"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'billing',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Shipping Options"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'shipping',
                                    'action'     => 'manage'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Manage Discounts"),
                            'icon'      => 'fa-cog',
                            'icon5'      => 'fas fa-cog',
                            'iconbs'      => 'bi-gear',
                            'classname' => 'manage',
                            'url'       => makeLink(
                                array(
                                    'controller' => 'ecomconfig',
                                    'action'     => 'manage_discounts'
                                )
                            ),
                        ),
                        array(
                            'text'      => gt("Import External Addresses"),
                            'icon'      => 'fa-upload',
                            'icon5'      => 'fas fa-upload',
                            'iconbs'      => 'bi-upload',
                            'classname' => 'import',
//                            'url'       => makeLink(array('controller' => 'store', 'action' => 'import_external_addresses')),
                            'url'       => makeLink(
                                array(
                                    'controller' => 'address',
                                    'action'     => 'import'
                                )
                            ),
                        ),
                    ),
                ),
            ),
        ),
    )
);

return $ecom;
?>
