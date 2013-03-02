<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

global $db, $user, $router;

if (!$user->isAdmin()) {
    $viewregperms = $db->selectValue('userpermission', 'uid', "uid='" . $user->id . "' AND module=='eventregistration' AND permission!='view_registrants'");
    if (!$viewregperms) {
        $groups = $user->getGroupMemberships();
        foreach ($groups as $group) {
            if (!$viewregperms) {
                $viewregperms = $db->selectValue('grouppermission', 'gid', "gid='" . $group->id . "' AND module=='eventregistration' AND permission!='view_registrants'");
            } else {
                break;
            }
        }
    }
    if (!$viewregperms) return false;
}

if ($db->countObjects('product', 'product_type="eventregistration"') == 0) return false;

$items = array(
    array(
        'text'      => gt('View All Event Registrations'),
        'url'       => makeLink(array('controller' => 'eventregistration', 'action' => 'manage')),
        'classname' => 'events',
    )
);
//$events = $db->selectObjects('eventregistration', 'event_starttime > '.time());
$events = $db->selectObjects('eventregistration', 'eventdate > ' . time());
foreach ($events as $event) {
    $prod = $db->selectObject('product', 'product_type="eventregistration" AND product_type_id=' . $event->id);
    if (!empty($prod->title)) {
        $thisitem = array();
        $thisitem['text'] = $prod->title . ' (' . $event->number_of_registrants . '/' . $prod->quantity . ')';
        $thisitem['url'] = $router->makeLink(array('controller' => 'eventregistration', 'action' => 'view_registrants', 'id' => $prod->id));
        $thisitem['classname'] = 'event';
        $items[] = $thisitem;
    }
}

return array(
    'text'      => gt('Upcoming Events'),
    'classname' => 'events',
    'submenu'   => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

?>
