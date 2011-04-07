<?php

##################################################
#
# Copyright (c) 2004-2006 OIC Group, Inc.
# Written and Designed by James Hunt
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

function smarty_function_pagelinks($params,&$smarty) {
    $config = $smarty->_tpl_vars['config'];
    if (!$config['pagelinks'] || $config['pagelinks']=="Top and Bottom") {
        echo $params['paginate']->links;
    } else if ($params['top'] && $config['pagelinks']=="Top Only") {
        echo $params['paginate']->links;
    } else if ($params['bottom'] && $config['pagelinks']=="Bottom Only") {
        echo $params['paginate']->links;
    }
}

?>

