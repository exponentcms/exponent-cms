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

global $db, $router;
if ($db->countObjects('product', 'product_type="eventregistration"') == 0) return false;

$events = $db->selectObjects('eventregistration', 'event_starttime > '.time());

$items = array();
$items[] = array(
    'text'=>"<strong><u>".gt('View All Event Registrations')."</u><strong>",
    'url'=>makeLink(array('controller'=>'eventregistration','action'=>'showall')),
);

foreach ($events as $event) {
    $prod = $db->selectObject('product', 'product_type="eventregistration" AND product_type_id='.$event->id);
    if (!empty($prod->title)) {
        $thisitem = array();
        $thisitem['text'] = $prod->title.' ('.$event->number_of_registrants.'/'.$prod->quantity.')';
        $thisitem['url'] = $router->makeLink(array('controller'=>'eventregistration','action'=>'view_registrants', 'id'=>$prod->id));
        $items[] = $thisitem;
    }
}

return array(
    'text'=>gt('Upcoming Events'),
    'classname'=>'events',
    'submenu'=>array(
        'id'=>'events',
        'itemdata'=>$items,
    )
);

?>
