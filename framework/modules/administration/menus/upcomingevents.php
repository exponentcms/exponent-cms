<?php

##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
        'icon' => 'fa-calendar-o',
        'url'       => makeLink(array('controller' => 'eventregistration', 'action' => 'manage')),
        'classname' => 'events',
    ),
    array(
        'text'      => gt('Add an event'),
        'icon' => 'fa-plus',
        'url'       => makeLink(array('controller' => 'store', 'action' => 'edit', 'product_type' => 'eventregistration')),
        'classname' => 'add',
    )
);
$ev = new eventregistration();
$allevents = $ev->find('all', 'product_type="eventregistration" && active_type=0');
$events = array();
foreach ($allevents as $event) {
    if ($event->eventdate > time()) {
        $events[] = $event;
    }
}
foreach ($events as $event) {
    if (!empty($event->title)) {
        $thisitem = array();
        $thisitem['text'] = $event->title . ' (' . $event->countRegistrants() . '/' . $event->quantity . ')';
        $thisitem['url'] = $router->makeLink(array('controller' => 'eventregistration', 'action' => 'view_registrants', 'id' => $event->id));
        $thisitem['classname'] = 'event';
        $thisitem['icon'] = 'fa-info';
        $items[] = $thisitem;
    }
}

return array(
    'text'      => gt('Upcoming Events'),
    'icon' => 'fa-calendar',
    'classname' => 'events',
    'submenu'   => array(
        'id'       => 'events',
        'itemdata' => $items,
    )
);

?>
