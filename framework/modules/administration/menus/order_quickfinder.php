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

return array(
    'text'=>'<form id="orderQuickfinder" method="POST" action="/index.php" enctype="multipart/form-data"><input type="hidden" name="controller" value="order"><input type="hidden" name="action" value="quickfinder"><input style="padding-top: 3px;" type="text" name="ordernum" id="ordernum" size="25" value="Order Quickfinder" onclick="this.value=\'\';"></form>',
    //'text'=>'{control type="autocomplete" controller="store" action="search" name="related_items" label="Add a new item" value="Search title or SKU to add an item" schema="title,id,sef_url,expFile,model" searchmodel="product" searchoncol="title,model" jsinject=$callbacks}',
    'classname'=>'order',    
);

?>
