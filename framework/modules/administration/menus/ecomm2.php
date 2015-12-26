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

if (!defined('EXPONENT'))
    exit('');

global $db, $user, $router;

$active = ECOM;
if (empty($active))
    return false;

if (!$user->isAdmin()) {
    $viewregperms = $db->selectValue(
        'userpermission',
        'uid',
        "uid='" . $user->id . "' AND module=='eventregistration' AND permission!='view_registrants'"
    );
    if (!$viewregperms) {
        $groups = $user->getGroupMemberships();
        foreach ($groups as $group) {
            if (!$viewregperms) {
                $viewregperms = $db->selectValue(
                    'grouppermission',
                    'gid',
                    "gid='" . $group->id . "' AND module=='eventregistration' AND permission!='view_registrants'"
                );
            } else {
                break;
            }
        }
    }
    if (!$viewregperms)
        return false;
}

if ($db->countObjects('product', 'product_type="eventregistration"') == 0)
    return false;

$items1 = array(
    array(
        'text'      => gt('Manage Event Registrations'),
        'icon'      => 'fa-calendar-o',
        'classname' => 'events',
        'url'       => makeLink(
            array(
                'controller' => 'eventregistration',
                'action' => 'manage'
            )
        ),
    ),
    array(
        'text'      => gt('Add an event'),
        'icon'      => 'fa-plus-circle',
        'classname' => 'add',
        'url'       => makeLink(
            array(
                'controller' => 'store',
                'action' => 'edit',
                'product_type' => 'eventregistration'
            )
        ),
        'divider' => true,
    )
);
$ev = new eventregistration();
$allevents = $ev->find('all', 'product_type="eventregistration" && active_type=0');
$events = array();
foreach ($allevents as $event) {
    if ($event->eventenddate > time()) {
        $events[] = $event;
    }
}
$events = expSorter::sort(array('array' => $events, 'sortby' => 'eventdate', 'order' => 'ASC'));
$items2 = array();
foreach ($events as $event) {
    if (!empty($event->title)) {
        $thisitem = array();
        $thisitem['text'] = $event->title . ' (' . $event->countRegistrants(
            ) . ($event->quantity ? '/' . $event->quantity : '') . ')';
        $thisitem['url'] = $router->makeLink(
            array(
                'controller' => 'eventregistration',
                'action' => 'view_registrants', 'id' => $event->id
            )
        );
        $thisitem['classname'] = 'event';
        $thisitem['icon'] = 'fa-info';
        $items2[] = $thisitem;
    }
}

if (bs3()) {
    $items = array_merge($items1, $items2);
} else {
    $items = array($items1, $items2);
}
return array(
    'text'      => ' <span class="event label label-default">' . count($events) . '</span>',
    'icon'      => 'fa-calendar',
    'classname' => 'events',
    'submenu'   => array(
        'id'       => 'event2',
        'itemdata' => $items,
    )
);

?>
