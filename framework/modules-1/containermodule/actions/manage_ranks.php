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

if (expPermissions::check('manage',$loc)) {

    $rank = 0;
    foreach($_REQUEST['rerank'] as $key=>$id) {
        $obj = $db->selectObject("container","id=".$id);
        $obj->rank = $rank;
        $db->updateObject($obj,"container");
        $rank++;
    }

    expSession::clearAllUsersSessionCache('containermodule');
    redirect_to($_REQUEST['lastpage']);

} else {
	echo SITE_403_HTML;
}

exit;

?>
