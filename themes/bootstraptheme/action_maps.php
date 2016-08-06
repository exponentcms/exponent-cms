<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
    'cart'=>array(                      // the 'cart' module
        'checkout'=>'Full Size',        // 'checkout' action will use the 'Full Size' subtheme
        'confirm'=>'Full Size',         // 'confirm' action will use the 'Full Size' subtheme
    ),
    'order'=>array(                     // the 'order' module
        '*'=>'Full Size'                // ANY action will use the 'Full Size' subtheme
    ),
    'report'=>array(                    // the 'report' module
        '*'=>'Full Size'                // ANY action will use the 'Full Size' subtheme
    ),
    'store'=>array(                     // the 'store' module
        'manage'=>'Full Size'           // 'manage' action will use the 'Full Size' subtheme
    ),
//    'blog'=>array(                      // the 'blog' module
//        'show'=>'Blog Sidebar',         // 'show' action will use the 'Blog Sidebar' subtheme
//        'showall'=>'Blog Sidebar',      // 'showall' action will use the 'Blog Sidebar' subtheme
//    ),
//    '*'=>array(                         // ANY module
//        'showall_by_tags'=>'Tags View'  // showall_by_tags action will use the 'Tags View' subtheme
//    ),
//    'news'=>array(
//        'show'=>'Large Banner'
//    ),
//    'search'=>array(
//        'search'=>'_Search Results'
//    ),
);

?>