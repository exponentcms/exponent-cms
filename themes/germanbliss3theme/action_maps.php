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

// the module may be a wildcard (meaning all modules) by using an asterisk '*'
// the action may be a wildcard (meaning all methods) by using an asterisk '*'
return array(
   'cart'=>array(                    // the 'cartController' module
       '*'=>'Default'                // ANY method will use the 'Default' subtheme
   ),
   'order'=>array(                   // the 'orderController' module
       '*'=>'Default'                // ANY method will use the 'Default' subtheme
   ),
   'search'=>array(                  // the 'searchController' module
       '*'=>'Default'                // ANY method will use the 'Default' subtheme
   ),
   'store'=>array(                   // the 'storeController' module
       '*'=>'Default'                // ANY method will use the 'Default' subtheme
   ),
   'storeCategory'=>array(           // the 'storeCategoryController' module
       '*'=>'Category Landing'       // ANY method will use the 'Category Landing' subtheme
   ),
//    'report'=>array(                    // the 'reportController' module
//        '*'=>'Full Size'                // ANY method will use the 'Full Size' subtheme
//    ),
//    '*'=>array(                         // ANY module
//        'showall_by_tags'=>'Tags View'  // showall_by_tags method will use the 'Tags View' subtheme
//    ),
//    'news'=>array(
//        'show'=>'Large Banner'
//    ),
//    'search'=>array(
//        'search'=>'_Search Results'
//    ),
);

?>
