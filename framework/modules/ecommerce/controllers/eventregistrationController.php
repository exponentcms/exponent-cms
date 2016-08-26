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

/**
 * @subpackage Controllers
 * @package    Modules
 */
function compare($x, $y) {
    if ($x->eventdate == $y->eventdate)
        return 0;
    else if ($x->eventdate < $y->eventdate)
        return -1;
    else
        return 1;
}

class eventregistrationController extends expController {
    public $basemodel_name = 'eventregistration';

    public $useractions = array(
        'showall'     => 'Show all events',
        'eventsCalendar'                  => 'Calendar View',
        'upcomingEvents'                  => 'Upcoming Events',
//        'showByTitle' => "Show events by title",
    );

    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
        'rss',
        'tags',
        'twitter',
    );  // all options: ('aggregation','categories','comments','ealerts','facebook','files','module_title','pagination','rss','tags','twitter',)

    protected $add_permissions = array(
        'view_registrants'=> 'View Registrants',
        'emailRegistrants'=> 'Email Registrants',
    );

    static function displayname() {
        return gt("e-Commerce Online Event Registration");
    }

    static function description() {
        return gt("Manage event registrations on your website");
    }

    function showall() {
        global $user;

        expHistory::set('viewable', $this->params);
        $limit = (!empty($this->config['limit'])) ? $this->config['limit'] : 10;

        $pass_events = array();
        if (!empty($this->params['past']) && $user->isAdmin()) {
            $events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
            foreach ($events as $event) {
               // $this->signup_cutoff > time()
               if ($event->eventdate <= time() && $event->eventenddate <= time()) {
                   $pass_events[] = $event;
               }
               // eDebug($event->signup_cutoff, true);
           }
        } else {
            if ($user->isAdmin()) {
                $events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
            } else {
                $events = $this->eventregistration->find('all', 'product_type="eventregistration" && active_type=0', "title ASC", $limit);
            }
            foreach ($events as $event) {
                if ($user->isAdmin()) {
                    $endtime = $event->eventenddate;
                } else {
                    $endtime = $event->signup_cutoff;
                }
                // $this->signup_cutoff > time()
                if ($event->eventdate > time() && $endtime > time()) {
                    $pass_events[] = $event;
                }
                // eDebug($event->signup_cutoff, true);
            }
        }
        // echo "<pre>";
        // print_r($pass_events);
        // exit();
        // uasort($pass_events,'compare');
        //eDebug($this->config['limit'], true);
        $page = new expPaginator(array(
            'records'=>$pass_events,
            'limit'=>$limit,
            'order'=>"eventdate ASC",
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'view'=>empty($this->params['view']) ? null : $this->params['view'],
            'columns'=>array(
                gt('Event')=>'title',
                gt('Date')=>'eventdate',
                gt('Seats')=>'quantity'
            ),
        ));
        assign_to_template(array(
            'page'=> $page,
            'admin'=> $user->isAdmin(),
            'past'=> !empty($this->params['past'])
        ));
    }

    function eventsCalendar() {
        global $user;

        expHistory::set('viewable', $this->params);

        $time = isset($this->params['time']) ? $this->params['time'] : time();
        assign_to_template(array(
            'time' => $time
        ));

//        $monthly = array();
//        $counts  = array();

        $info = getdate($time);
        $nowinfo = getdate(time());
        if ($info['mon'] != $nowinfo['mon']) $nowinfo['mday'] = -10;
        // Grab non-day numbers only (before end of month)
//        $week        = 0;
        $currentweek = -1;

        $timefirst = mktime(0, 0, 0, $info['mon'], 1, $info['year']);
        $week = intval(date('W', $timefirst));
        if ($week >= 52 && $info['mon'] == 1) $week = 1;
        $infofirst = getdate($timefirst);

//        if ($infofirst['wday'] == 0) {
//            $monthly[$week] = array(); // initialize for non days
//            $counts[$week]  = array();
//        }
//        for ($i = 1 - $infofirst['wday']; $i < 1; $i++) {
//            $monthly[$week][$i] = array();
//            $counts[$week][$i]  = -1;
//        }
//        $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
        $monthly[$week] = array(); // initialize for non days
        $counts[$week] = array();
        if (($infofirst['wday'] == 0) && (DISPLAY_START_OF_WEEK == 1)) {
            for ($i = -6; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                $monthly[$week][$i] = array();
                $counts[$week][$i] = -1;
            }
            $weekday = $infofirst['wday'] + 7; // day number in grid.  if 7+, switch weeks
        } else {
            for ($i = 1 - $infofirst['wday']; $i < (1 - DISPLAY_START_OF_WEEK); $i++) {
                $monthly[$week][$i] = array();
                $counts[$week][$i] = -1;
            }
            $weekday = $infofirst['wday']; // day number in grid.  if 7+, switch weeks
        }
        // Grab day counts
        $endofmonth = date('t', $time);

        for ($i = 1; $i <= $endofmonth; $i++) {
            $start = mktime(0, 0, 0, $info['mon'], $i, $info['year']);
            if ($i == $nowinfo['mday']) $currentweek = $week;

//            $dates              = $db->selectObjects("eventregistration", "`eventdate` = $start");
//            $dates = $db->selectObjects("eventregistration", "(eventdate >= " . expDateTime::startOfDayTimestamp($start) . " AND eventdate <= " . expDateTime::endOfDayTimestamp($start) . ")");
            $er = new eventregistration();
//            $dates = $er->find('all', "(eventdate >= " . expDateTime::startOfDayTimestamp($start) . " AND eventdate <= " . expDateTime::endOfDayTimestamp($start) . ")");

            if ($user->isAdmin()) {
                $events = $er->find('all', 'product_type="eventregistration"', "title ASC");
            } else {
                $events = $er->find('all', 'product_type="eventregistration" && active_type=0', "title ASC");
            }
            $dates = array();

            foreach ($events as $event) {
                // $this->signup_cutoff > time()
                if ($event->eventdate >= expDateTime::startOfDayTimestamp($start) && $event->eventdate <= expDateTime::endOfDayTimestamp($start)) {
                    $dates[] = $event;
                }
                // eDebug($event->signup_cutoff, true);
            }

            $monthly[$week][$i] = $this->getEventsForDates($dates);
            $counts[$week][$i] = count($monthly[$week][$i]);
            if ($weekday >= (6 + DISPLAY_START_OF_WEEK)) {
                $week++;
                $monthly[$week] = array(); // allocate an array for the next week
                $counts[$week] = array();
                $weekday = DISPLAY_START_OF_WEEK;
            } else $weekday++;
        }
        // Grab non-day numbers only (after end of month)
        for ($i = 1; $weekday && $i < (8 + DISPLAY_START_OF_WEEK - $weekday); $i++) {
            $monthly[$week][$i + $endofmonth] = array();
            $counts[$week][$i + $endofmonth] = -1;
        }

        $this->params['time'] = $time;
        assign_to_template(array(
            'currentweek' => $currentweek,
            'monthly'     => $monthly,
            'counts'      => $counts,
            "prevmonth3"  => strtotime('-3 months', $timefirst),
            "prevmonth2"  => strtotime('-2 months', $timefirst),
            "prevmonth"   => strtotime('-1 months', $timefirst),
            "nextmonth"   => strtotime('+1 months', $timefirst),
            "nextmonth2"  => strtotime('+2 months', $timefirst),
            "nextmonth3"  => strtotime('+3 months', $timefirst),
            'now'         => $timefirst,
            "today"       => expDateTime::startOfDayTimestamp(time()),
            'params'      => $this->params,
            'daynames'    => event::dayNames(),
        ));
    }

    function upcomingEvents() {
        $sql = 'SELECT DISTINCT p.*, er.eventdate, er.event_starttime, er.signup_cutoff FROM ' . DB_TABLE_PREFIX . '_product p ';
        $sql .= 'JOIN ' . DB_TABLE_PREFIX . '_eventregistration er ON p.product_type_id = er.id ';
        $sql .= 'WHERE er.signup_cutoff > ' . time() . ' AND er.eventdate > ' . time();

        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $order = 'eventdate';
        $dir = 'ASC';

        $page = new expPaginator(array(
            'model_field' => 'product_type',
            'sql'         => $sql,
            'limit'       => $limit,
            'order'       => $order,
            'dir'         => $dir,
            'page'        => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'  => $this->params['controller'],
            'action'      => $this->params['action'],
            'columns'     => array(
                gt('Model #')      => 'model',
                gt('Product Name') => 'title',
                gt('Price')        => 'base_price'
            ),
        ));
        assign_to_template(array(
            'page' => $page
        ));
    }

    function show() {
        global $order, $user;

        expHistory::set('viewable', $this->params);
        if (!empty($this->params['token'])) {
            $record = expSession::get("last_POST_Paypal");
        } else {
            $record = expSession::get("last_POST");
        }
        $id = isset($this->params['title']) ? addslashes($this->params['title']) : $this->params['id'];
        $product = new eventregistration($id);

        //FIXME we only have 0=active & 2=inactive ???
        if ($product->active_type == 1) {
            $product->user_message = "This event is temporarily unavailable for registration.";
        } elseif ($product->active_type == 2) {
            if ($user->isAdmin()) {
                $product->user_message = $product->title . " is currently marked as unavailable for open registration or display.  Normal users will not see this event.";
            } else {
                flash("error", $product->title . " " . gt("registration is currently unavailable for registration."));
                expHistory::back();
            }
        }

        $order_registrations = array();
        $count = 1;
        if (!empty($this->params['orderitem_id'])) {  // editing an event already in the cart?
            $f = new forms($product->forms_id);
            $loc_data = new stdClass();
            $loc_data->order_id = $order->id;
            $loc_data->orderitem_id = $this->params['orderitem_id'];
            $loc_data->event_id = $product->id;
            $locdata = serialize($loc_data);
//            $order_registrations = $db->selectObjects('forms_' . $f->table_name, "location_data='" . $locdata . "'");
            $order_registrations = $f->getRecords("location_data='" . $locdata . "'");

//            $registrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND orderitem_id =" . $this->params['orderitem_id'] . " AND event_id =" . $product->id);
//            if (!empty($registrants)) foreach ($registrants as $registrant) {
//                $order_registrations[] = expUnserialize($registrant->value);
//            }
            $item = $order->isItemInCart($product->id, $product->product_type, $this->params['orderitem_id']);
            if (!empty($item)) {
                $params['options'] = $item->opts;
                assign_to_template(array(
                    'params'=> $params,
                    'orderitem_id'=>$item->id
                ));
                $count = $item->quantity;
            }
        }

        //eDebug($product, true);
        assign_to_template(array(
            'product'=> $product,
//            'record'=> $record,
            'registered' => $order_registrations,
            'count' => $count,
        ));
    }

    function showByTitle() {
        global $order, $template, $user;
        expHistory::set('viewable', $this->params);
        if (!empty($this->params['token'])) {
            $record = expSession::get("last_POST_Paypal");
        } else {
            $record = expSession::get("last_POST");
        }
        $product = new eventregistration(addslashes($this->params['title']));

        //TODO should we pull in an existing reservation already in the cart to edit? e.g., the registrants
         //FIXME we only have 0=active & 2=inactive ???
        if ($product->active_type == 1) {
            $product->user_message = "This event is temporarily unavailable for registration.";
        } elseif ($product->active_type == 2) {
            if ($user->isAdmin()) {
                $product->user_message = $product->title . " is currently marked as unavailable for open registration or display.  Normal users will not see this event.";
            } else {
                flash("error", $product->title . " " . gt("registration is currently unavailable."));
                expHistory::back();
            }
        }

        //eDebug($product, true);
        assign_to_template(array(
            'product'=> $product,
            'record'=> $record
        ));
    }

    function manage() {
        global $user;

        expHistory::set('viewable', $this->params);
        $limit = (!empty($this->config['limit'])) ? $this->config['limit'] : 10;

        $pass_events = array();
        if (!empty($this->params['past']) && $user->isAdmin()) {
            $events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
            foreach ($events as $event) {
               // $this->signup_cutoff > time()
               if ($event->eventdate <= time() && $event->eventenddate <= time()) {
                   $pass_events[] = $event;
               }
               // eDebug($event->signup_cutoff, true);
           }
        } else {
            if ($user->isAdmin()) {
                $events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
            } else {
                $events = $this->eventregistration->find('all', 'product_type="eventregistration" && active_type=0', "title ASC", $limit);
            }
            foreach ($events as $event) {
                // $this->signup_cutoff > time()
                if ($user->isAdmin()) {
                    $endtime = $event->eventenddate;
                } else {
                    $endtime = $event->signup_cutoff;
                }
                if ($event->eventdate > time() && $endtime > time()) {
                    $pass_events[] = $event;
                }
                // eDebug($event->signup_cutoff, true);
            }
        }
        foreach ($pass_events as $key=>$pass_event) {
            $pass_events[$key]->number_of_registrants = $pass_event->countRegistrants();
        }
        // echo "<pre>";
        // print_r($pass_events);
        // exit();
        // uasort($pass_events,'compare');
        //eDebug($this->config['limit'], true);
        $page = new expPaginator(array(
            'records'=>$pass_events,
            'limit'=>$limit,
            'order'=>"eventdate ASC",
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'src'=>$this->loc->src,
            'view'=>empty($this->params['view']) ? null : $this->params['view'],
            'columns'=>array(
                gt('Event')=>'title',
                gt('Date')=>'eventdate',
                gt('Seats')=>'quantity'
            ),
        ));
        assign_to_template(array(
            'page'=> $page,
            'admin'=> $user->isAdmin(),
            'past'=> !empty($this->params['past'])
        ));
    }

    function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;

        // figure out what metadata to pass back based on the action we are in.
        $action   = $router->params['action'];
        $metainfo = array('title' => '', 'keywords' => '', 'description' => '', 'canonical'=> '', 'noindex' => false, 'nofollow' => false);
        $storename = ecomconfig::getConfig('storename');
        switch ($action) {
            case 'showall':
            case 'eventsCalendar':
            case 'upcomingEvents':
                $metainfo['title']       = gt('Event Registration') . ' - ' . $storename;
                $metainfo['keywords']    = gt('event registration online');
                $metainfo['description'] = gt("Make an event registration");
                break;
            case 'show':
            case 'showByTitle':
                if (isset($router->params['id']) || isset($router->params['title'])) {
                    $lookup = isset($router->params['id']) ? $router->params['id'] : $router->params['title'];
                    $object = new eventregistration($lookup);
                    // set the meta info
                    if (!empty($object)) {
                        if (!empty($object->body)) {
                            $desc = str_replace('"',"'",expString::summarize($object->body,'html','para'));
                        } else {
                            $desc = SITE_DESCRIPTION;
                        }
                        if (!empty($object->expTag)) {
                            $keyw = '';
                            foreach ($object->expTag as $tag) {
                                if (!empty($keyw)) $keyw .= ', ';
                                $keyw .= $tag->title;
                            }
                        } else {
                            $keyw = SITE_KEYWORDS;
                        }
                        $metainfo['title'] = empty($object->meta_title) ? $object->title . " - " . $storename : $object->meta_title;
                        $metainfo['keywords'] = empty($object->meta_keywords) ? $keyw : $object->meta_keywords;
                        $metainfo['description'] = empty($object->meta_description) ? $desc : $object->meta_description;
//                        $metainfo['canonical'] = empty($object->canonical) ? URL_FULL.substr($router->sefPath, 1) : $object->canonical;
                        $metainfo['canonical'] = empty($object->canonical) ? $router->plainPath() : $object->canonical;
                        $metainfo['noindex'] = empty($object->meta_noindex) ? false : $object->meta_noindex;
                        $metainfo['nofollow'] = empty($object->meta_nofollow) ? false : $object->meta_nofollow;
                    }
                    break;
                }
            default:
                $metainfo['title']       = self::displayname() . " - " . $storename;
                $metainfo['keywords']    = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
        }

        return $metainfo;
    }

    function eventregistration_process() {  // FIXME only used by the eventregistration_form view (no method)
        global $db, $user, $order;

        //Clear the cart first
        foreach ($order->orderitem as $orderItem) {
            $orderItem->delete();
        }
        $order->refresh();

        eDebug($order, true);

        expHistory::set('viewable', $this->params);
        expSession::set('last_POST_Paypal', $this->params);
        expSession::set('terms_and_conditions', $product->terms_and_condition); //FIXME $product doesn't exist
        expSession::set('paypal_link', makeLink(array('controller'=> 'eventregistration', 'action'=> 'show', 'title'=> $product->sef_url)));

        //Validation for customValidation
        foreach ($this->params['event'] as $key => $value) {
            $expField     = $db->selectObject("expDefinableFields", "name = '{$key}'");
            $expFieldData = expUnserialize($expField->data);
            if (!empty($expFieldData->customvalidation)) {

                $customValidation = "is_valid_" . $expFieldData->customvalidation;
                $fieldname        = $expField->name;
                $obj              = new stdClass();
                $obj->$fieldname  = $value;
                if ($fieldname == "email") { //Change this to much more loose coding
                    $ret = expValidator::$customValidation($fieldname, $this->params['event']['email'], $this->params['event']['email_confirm']);
                } else {
                    $ret = expValidator::$customValidation($fieldname, $obj, $obj);
                }
                if (strlen($ret) > 1) {

                    expValidator::failAndReturnToForm($ret, $this->params);
                }
            }

            if (@$expFieldData->minimum_size > 0 || @$expFieldData->maximum_size > 0) {
                $ret = expValidator::check_size_of($expFieldData->identifier, $value, $expFieldData->minimum_size, $expFieldData->maximum_size);

                if (strlen($ret) > 1) {
                    expValidator::failAndReturnToForm($ret, $this->params);
                }
            }
        }

        $event = new eventregistration();
        //Validation for ticker types
        if (isset($this->params['ticket_types']) && empty($this->params['options'])) {
            expValidator::failAndReturnToForm("Invalid ticket types.", $this->params);
        }

//        if (!empty($this->params['event'])) {
            $sess_id = session_id();
//            $sess_id = expSession::getTicketString();
//            $data    = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND event_id =" . $this->params['eventregistration']['product_id']);
            $data    = $event->getRecords("connector_id ='{$order->id}' AND event_id =" . $this->params['eventregistration']['product_id']);
         //FIXME change this to forms table
            if (!empty($data)) {
                foreach ($data as $item) {
                    if (!empty($this->params['event'][$item->control_name])) {
                        $item->value = $this->params['event'][$item->control_name];
//                        $db->updateObject($item, "eventregistration_registrants");
                        $event->updateRecord($item);
                    }
                }
            } else {
                if (!empty($this->params['event'])) foreach ($this->params['event'] as $key => $value) {
                    $obj                  = new stdClass();
                    $obj->event_id        = $this->params['eventregistration']['product_id'];
                    $obj->control_name    = $key;
                    $obj->value           = $value;
                    $obj->connector_id    = $order->id;
                    $obj->registered_date = time();
//                    $db->insertObject($obj, "eventregistration_registrants");
                    $event->insertRecord($obj);
                } else {
                    $obj                  = new stdClass();
                    $obj->event_id        = $this->params['eventregistration']['product_id'];
                    $obj->connector_id    = $order->id;
                    $obj->registered_date = time();
//                    $db->insertObject($obj, "eventregistration_registrants");
                    $event->insertRecord($obj);
                }
            }
            expSession::set('session_id', $sess_id);
//        }

        //Add to Cart
        $product_id   = $this->params['eventregistration']['product_id'];
        $product_type = "eventregistration";
        $product      = new $product_type($product_id, true, true);

        if ($this->params['options']) {
            $this->params['eventregistration']['options']          = $this->params['options'];
            $this->params['eventregistration']['options_quantity'] = $this->params['options_quantity'];
            $product->addToCart($this->params['eventregistration']);
        } else {
            $this->params['eventregistration']['qtyr'] = $this->params['theValue'] + 1;
            $product->addToCart($this->params['eventregistration']);
        }

        $order->calculateGrandTotal();
        $order->setOrderType($this->params);
        $order->setOrderStatus($this->params);

        $billing = new billing();
        $result  = $billing->calculator->preprocess($billing->billingmethod, $opts, $this->params, $order); //FIXME $opts doesn't exist
        redirect_to(array('controller'=> 'cart', 'action'=> 'preprocess'));
    }

    function delete() {
        redirect_to(array('controller'=> 'eventregistration', 'action'=> 'showall'));
    }

    function view_registrants() {
        expHistory::set('viewable', $this->params);
        $event = new eventregistration($this->params['id']);
        //Get all the registrants in the event
//        $registrants = $event->getRegistrants();
        assign_to_template(array(
            'event'=> $event,
            'registrants'=> $event->getRegistrants(),
            'count'=> $event->countRegistrants(),
//            'header'=> $header,
//            'body'=> $body,
//            'email'=> $email
        ));

//        $order_ids_complete = $db->selectColumn("eventregistration_registrants", "connector_id", "connector_id <> '0' AND event_id = {$event->id}", "registered_date", true);
//        $orders = new order();
//        foreach ($order_ids_complete as $item) {
//            $connector = expUnserialize($item);
////            $odr = $db->selectObject("orders", "id = {$item} and invoice_id <> 0");
////            $odr = $db->selectObject("orders", "id ='{$item}' and invoice_id <> 0");
//            $odr = $orders->find("first", "id ='{$connector->order_id}' and invoice_id <> 0");
//            if (!empty($odr) || strpos($connector->order_id, "admin-created") !== false) {
//                $order_ids[] = $connector->order_id;
//            }
//        }
//
//        $header        = array();
//        $control_names = array();
//        $header[]      = 'Date Registered';
//        //Check if it has ticket types
//        if ($event->hasOptions()) {
//            $header[] = "Types"; //Add some configuration here
//        }
//        //Get the input labels as table headers
//        if (!empty($event->expDefinableField['registrant'])) foreach ($event->expDefinableField['registrant'] as $field) {
//            $data = expUnserialize($field->data);
//            if (!empty($data->caption)) {
//                $header[] = $data->caption;
//            } else {
//                $header[] = $field->name;
//            }
//            $control_names[] = $field->name;
//        }
//
//        //Check if there are guests using expDefinableFields
//        if (!empty($event->num_guest_allowed)) {
//            for ($i = 1; $i <= $event->num_guest_allowed; $i++) {
//                if (!empty($event->expDefinableField['guest'])) foreach ($event->expDefinableField['guest'] as $field) {
//                    $data = expUnserialize($field->data);
//                    if (!empty($data->caption)) {
//                        $header[] = $data->caption . "_$i";
//                    } else {
//                        $header[] = $field->name . "_$i";
//                    }
//                    $control_names[] = $field->name . "_$i";
//                }
//            }
//        }
//
//        // new method to check for guests/registrants in eventregistration_registrants
////        if (!empty($event->num_guest_allowed)) {
//        $registrants = array();
//        if (!empty($event->quantity)) {
//            $registered = array();
//            if (!empty($order_ids)) foreach ($order_ids as $order_id) {
//                $newregistrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order_id}'");
//                $registered = array_merge($registered,$newregistrants);
//            }
//            foreach ($registered as $person) {
//                $registrants[$person->id] = expUnserialize($person->value);
//            }
//        }
//
//        //Get the data and registrant emails
//        $email               = array();
//        $num_of_guest_fields = 0;
//        $num_of_guest        = 0;
//        $num_of_guest_total  = 0;
//
//        $body = array();
//        if (!empty($order_ids)) foreach ($order_ids as $order_id) {
//            $body[$order_id][] = date("M d, Y h:i a", $db->selectValue("eventregistration_registrants", "registered_date", "event_id = {$event->id} AND connector_id = '{$order_id}'"));
//            if ($event->hasOptions()) {
//                $or        = new order($order_id);
//                $orderitem = new orderitem();
//                if (isset($or->orderitem[0])) {
//                    $body[$order_id][] = $orderitem->getOption($or->orderitem[0]->options);
//                } else {
//                    $body[$order_id][] = '';
//                }
//            }
//            foreach ($control_names as $control_name) {
//                $value             = $db->selectValue("eventregistration_registrants", "value", "event_id = {$event->id} AND control_name ='{$control_name}' AND connector_id = '{$order_id}'");
//                $body[$order_id][] = $value;
//                if (expValidator::isValidEmail($value) === true) {
//                    $email[$value] = $value;
//                }
//            }
//
//            if (!empty($order_id)) {
//                $num_of_guest_total += $db->countObjects("eventregistration_registrants", "event_id ={$event->id} AND control_name LIKE 'guest_%' AND connector_id = '{$order_id}'");
//            }
//        } else $order_ids = array();
//
//        // check numbers based on expDefinableFields
//        $num_of_guest_fields = $db->countObjects("content_expDefinableFields", "content_id ={$event->id} AND subtype='guest'");
//        if ($num_of_guest_fields <> 0) {
//            $num_of_guest = $num_of_guest_total / $num_of_guest_fields;
//        } else {
//            $num_of_guest = 0;
//        }
//
//        //Removed duplicate emails
//        $email = array_unique($email);
//
//        $registered = count($order_ids) + $num_of_guest;
//        if (!empty($event->registrants)) {
//            $event->registrants = expUnserialize($event->registrants);
//        } else {
//            $event->registrants = array();
//        }
//
//        $event->number_of_registrants = $registered;
//        assign_to_template(array(
//            'event'=> $event,
//            'registrants'=> $registrants,
////            'header'=> $header,
////            'body'=> $body,
////            'email'=> $email
//        ));
    }

    public function delete_registrant() {
//        global $db;

        $event = new eventregistration($this->params['event_id']);
        $f = new forms($event->forms_id);
        if (!empty($f->is_saved)) {  // is there user input data
//            $db->delete('forms_' . $f->table_name, "id='{$this->params['id']}'");
            $f->deleteRecord($this->params['id']);
        } else {
//            $db->delete('eventregistration_registrants', "id ='{$this->params['id']}'");
            $event->deleteRecord($this->params['id']);
        }
        flash('message', gt("Registrant successfully deleted."));
        expHistory::back();
    }

    public function edit_registrant() {
//        global $db;

//        $event_id     = $this->params['event_id'];
//        $connector_id = @$this->params['connector_id'];
//        if (empty($connector_id)) {
//            $connector_id = "admin-created" . mt_rand() . time(); //Meaning it is been added by admin
//        }
//        $reg_data   = $db->selectObjects("eventregistration_registrants", "connector_id ='{$connector_id}'");

        $registrant = array();
        $event = new eventregistration($this->params['event_id']);
        if (!empty($this->params['id'])) {
//            $reg_data   = $db->selectObject("eventregistration_registrants", "id ='{$this->params['id']}'");
            $f = new forms($event->forms_id);

  //        foreach ($reg_data as $item) {
  //            $registrant[$item->control_name] = $item->value;
  //        }
//            $registrant = expUnserialize($reg_data->value);
            if (!empty($f->is_saved)) {
//                $registrant = $db->selectObject('forms_' . $f->table_name, "id ='{$this->params['id']}'");
                $registrant = $f->getRecord($this->params['id']);
//            $registrant['id'] = $reg_data->id;
//            $eventid = $reg_data->event_id;
//        } else {
//            $eventid = $this->params['event_id'];
            } else {
//                $registrant = $db->selectObject("eventregistration_registrants", "id ='{$this->params['id']}'");
                $registrant = $event->getRecord($this->params['id']);
            }
        }

        // eDebug($registrant, true);
        assign_to_template(array(
            'registrant'=> $registrant,
            'event'=> $event,
//            'connector_id' => $connector_id
        ));
    }

    public function update_registrant() {
        global $db, $user;

        $event = new eventregistration($this->params['event_id']);
        // create a new order/invoice if needed
        if (empty($this->params['id'])) {
            //create new order
            $orderc= expModules::getController('order');
            $orderc->params = array(
                'customer_type'   => 1,  // blank user/address
                'addresses_id'    => 0,
                'order_status_id' => order::getDefaultOrderStatus(),
                'order_type_id'   => order::getDefaultOrderType(),
                'no_redirect'     => true,
            );
            $orderc_id = $orderc->save_new_order();
            //create new order item
            $orderc->params = array(
                'orderid'       => $orderc_id,
                'product_id'    => $event->id,
                'product_type'  => 'eventregistration',
//                'products_price' => $event->getBasePrice(),
//                'products_name'  => $event->title,
                'quantity'      => $this->params['value'],
                'no_redirect'     => true,
            );
            $orderi_id = $orderc->save_order_item();  // will redirect us to the new order view
        }
        $f = new forms($event->forms_id);
        $registrant = new stdClass();
//        if (!empty($this->params['id'])) $registrant = $db->selectObject('forms_' . $f->table_name, "id ='{$this->params['id']}'");
        if (!empty($this->params['id'])) $registrant = $f->getRecord($this->params['id']);
        if (!empty($f->is_saved)) {
            $fc = new forms_control();
            $controls = $fc->find('all', "forms_id=" . $f->id . " AND is_readonly=0",'rank');
            foreach ($controls as $c) {
                $ctl = expUnserialize($c->data);
                $control_type = get_class($ctl);
                $def = call_user_func(array($control_type, "getFieldDefinition"));
                if ($def != null) {
                    $emailValue = htmlspecialchars_decode(call_user_func(array($control_type, 'parseData'), $c->name, $this->params['registrant'], true));
                    $value = stripslashes(expString::escape($emailValue));
                    $varname = $c->name;
                    $registrant->$varname = $value;
                }
            }
            if (!empty($registrant->id)) {
//                $db->updateObject($registrant, 'forms_' . $f->table_name);
                $f->updateRecord($registrant);
            } else {  // create new registrant record
                $loc_data = new stdClass();
//                $loc_data->order_id = 'admin-created';
//                $loc_data->orderitem_id = 'admin-created';
                $loc_data->order_id = $orderc_id;
                $loc_data->orderitem_id = $orderi_id;
                $loc_data->event_id = $this->params['event_id'];
                $locdata = serialize($loc_data);
                $registrant->ip = $_SERVER['REMOTE_ADDR'];
                $registrant->referrer = $this->params['event_id'];
                $registrant->timestamp = time();
                if (expSession::loggedIn()) {
                    $registrant->user_id = $user->id;
                } else {
                    $registrant->user_id = 0;
                }
                $registrant->location_data = $locdata;
//                $db->insertObject($registrant, 'forms_' . $f->table_name);
                $f->insertRecord($registrant);
            }
        } else {
//            $registrant = $db->selectObject("eventregistration_registrants", "id ='{$this->params['id']}'");
            $registrant = $event->getRecord($this->params['id']);
            if (!is_object($registrant)) $registrant = new stdClass();
            $registrant->control_name = $this->params['control_name'];
            //FIXME if $registrant->value != $this->params['value'] update order/invoice w/ new quantity???
            $registrant->value = $this->params['value'];
            if (!empty($registrant->id)) {
//                $db->updateObject($registrant, "eventregistration_registrants");
                $event->updateRecord($registrant);
            } else {  // create new registrant record
                $registrant->event_id = $this->params['event_id'];
//                $registrant->connector_id = 'admin-created';
//                $registrant->orderitem_id = 'admin-created';
                $registrant->registered_date = time();
                $registrant->connector_id = $orderc_id;
                $registrant->orderitem_id = $orderi_id;
//                $db->insertObject($registrant, "eventregistration_registrants");
                $event->insertRecord($registrant);
            }
        }
        redirect_to(array('controller'=> 'eventregistration', 'action'=> 'view_registrants', 'id'=> $this->params['event_id']));
    }

    public function export() {
//        global $db;

        $event              = new eventregistration($this->params['id']);

        $registrants = $event->getRegistrants();
        $f = new forms($event->forms_id);
//        $registrants = array();
//        if (!empty($f->is_saved)) {  // is there user input data
//            $registrants = $db->selectObjects('forms_' . $f->table_name, "referrer = {$event->id}", "timestamp");
//        } else {
//            $registrants = $db->selectObjects('eventregistration_registrants', "connector_id", "connector_id <> '0' AND event_id = {$event->id}", "timestamp");
//        }
//        foreach ($registrants as $key=>$registrant) {
//            $order_data = expUnserialize($registrant->location_data);
//            if (is_numeric($order_data->order_id)) {
//                $order = new order($order_data->order_id);
//                $billingstatus = expUnserialize($order->billingmethod[0]->billing_options);
//                $registrants[$key]->payment = !empty($billingstatus->payment_due) ? gt('payment due') : gt('paid');
//            } else {
//                $registrants[$key]->payment = '???';
//            }
//        }

//        $sql                = "SELECT connector_id FROM " . $db->prefix . "eventregistration_registrants GROUP BY connector_id";
//        $order_ids_complete = $db->selectColumn("eventregistration_registrants", "connector_id", "connector_id <> '0' AND event_id = {$event->id}", "registered_date", true);
//
//        $orders = new order();
//        foreach ($order_ids_complete as $item) {
////            $odr = $db->selectObject("orders", "id = {$item} and invoice_id <> 0");
//            $odr = $orders->find("first", "id ='{$item}' and invoice_id <> 0");
//            if (!empty($odr) || strpos($item, "admin-created") !== false) {
//                $order_ids[] = $item;
//            }
//        }
//
//        $header        = array();
//        $control_names = array();
//        $header[]      = '"Date Registered"';
//        //Check if it has ticket types
//        if ($event->hasOptions()) {
//            $header[] = '"Ticket Types"'; //Add some configuration here
//        }
//
//        if (!empty($event->expDefinableField['registrant'])) foreach ($event->expDefinableField['registrant'] as $field) {
//            $data = expUnserialize($field->data);
//            if (!empty($data->caption)) {
//                $header[] = '"' . $data->caption . '"';
//            } else {
//                $header[] = '"' . $field->name . '"';
//            }
//            $control_names[] = $field->name;
//        }

        //FIXME we don't have a 'guest' definable field type
//        if ($event->num_guest_allowed > 0) {
//            for ($i = 1; $i <= $event->num_guest_allowed; $i++) {
//                if (!empty($event->expDefinableField['guest'])) foreach ($event->expDefinableField['guest'] as $field) {
//                    $data = expUnserialize($field->data);
//                    if (!empty($data->caption)) {
//                        $header[] = $data->caption . "_$i";
//                    } else {
//                        $header[] = $field->name . "_$i";
//                    }
//                    $control_names[] = $field->name . "_$i";
//                }
//
//            }
//        }

        // new method to check for guests/registrants
//        if (!empty($event->num_guest_allowed)) {
//        if (!empty($event->quantity)) {
//            $registered = array();
//            if (!empty($order_ids)) foreach ($order_ids as $order_id) {
//                $newregistrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order_id}'");
//                $registered = array_merge($registered,$newregistrants);
//            }
////            $registrants = array();
//            foreach ($registered as $key=>$person) {
//                $registered[$key]->person = expUnserialize($person->value);
//            }
            if (!empty($f->is_saved)) {
                $controls = $event->getAllControls();
                if ($f->column_names_list == '') {
                    //define some default columns...
                    foreach ($controls as $control) {
                        $rpt_columns[$control->name] = $control->caption;
                    }
                } else {
                    $rpt_columns2 = explode("|!|", $f->column_names_list);
                    $fc = new forms_control();
                    foreach ($rpt_columns2 as $column) {
                        $control = $fc->find('first', "forms_id=" . $f->id . " AND name = '" . $column . "' AND is_readonly = 0 AND is_static = 0", "rank");
                        if (!empty($control)) {
                            $rpt_columns[$control->name] = $control->caption;
                        } else {
                            switch ($column) {
                                case 'ip':
                                    $rpt_columns[$column] = gt('IP Address');
                                    break;
                                case 'referrer':
                                    $rpt_columns[$column] = gt('Event ID');
                                    break;
                                case 'user_id':
                                    $rpt_columns[$column] = gt('Posted by');
                                    break;
                                case 'timestamp':
                                    $rpt_columns[$column] = gt('Registered');
                                    break;
                            }
                        }
                    }
                }
                $rpt_columns['payment'] = gt('Paid?');
                $fc = new forms_control();
                foreach ($rpt_columns as $column_name=>$column_caption) {
                    if ($column_name == "ip" || $column_name == "referrer" || $column_name == "location_data") {
                    } elseif ($column_name == "user_id") {
                        foreach ($registrants as $key => $item) {
                            if ($item->$column_name != 0) {
                                $locUser = user::getUserById($item->$column_name);
                                $item->$column_name = $locUser->username;
                            } else {
                                $item->$column_name = '';
                            }
                            $registrants[$key] = $item;
                        }
                    } elseif ($column_name == "timestamp") {
                        foreach ($registrants as $key => $item) {
                            $item->$column_name = strftime("%m/%d/%y %T", $item->$column_name);  // needs to be in a machine readable format
                            $registrants[$key] = $item;
                        }
                    } else {
                        $control = $fc->find('first', "name='" . $column_name . "' AND forms_id=" . $this->params['id'],'rank');
                        if ($control) {
                            $ctl = expUnserialize($control->data);
                            $control_type = get_class($ctl);
                            foreach ($registrants as $key => $item) {
                                $item->$column_name = call_user_func(array($control_type, 'templateFormat'), $item->$column_name, $ctl);
                                $registrants[$key] = $item;
                            }
                        }
                    }
                }
            } else {
                $rpt_columns = array(
                    'user' => gt('Name'),
                    'qty' => gt('Quantity'),
                    'registered_date' => gt('Registered'),
                    'payment' => gt('Paid?'),
                );
                foreach ($registrants as $key=>$registrant) {
                    $registrants[$key]->registered_date = strftime(DISPLAY_DATETIME_FORMAT, $registrants[$key]->registered_date);
                }
            }
//            $header   = array();  //FIXME reset & pulled from above
//            $header[] = gt('IP Address');  //FIXME
//            $header[] = gt('Event');
//            $header[] = gt('Date Registered');
//            $header[] = gt('User');
//            $header[] = gt('Location');
//            foreach ($controls as $control) {
//                $header[] = $control->caption;
//            }
//        }

        if (LANG_CHARSET == 'UTF-8') {
            $out = chr(0xEF).chr(0xBB).chr(0xBF);  // add utf-8 signature to file to open appropriately in Excel, etc...
        } else {
            $out = "";
        }
//        $out  .= implode(",", $header);
//        $out  .= "\n";
//        $body = '';
//        foreach ($order_ids as $order_id) {
//            $body .= '"' . date("M d, Y h:i a", $db->selectValue("eventregistration_registrants", "registered_date", "event_id = {$event->id} AND connector_id = '{$order_id}'")) . '",';
//
//            if ($event->hasOptions()) {
//                $or        = new order($order_id);
//                $orderitem = new orderitem();
//                if (isset($or->orderitem[0])) {
//                    $body .= '"' . str_replace("<br />", " ", $orderitem->getOption($or->orderitem[0]->options)) . '",';
//                    ;
//                } else {
//                    $body .= '"",';
//                }
//            }
//
//            foreach ($control_names as $control_name) {
//                $value = $db->selectValue("eventregistration_registrants", "value", "event_id = {$event->id} AND control_name ='{$control_name}' AND connector_id = '{$order_id}'");
//                $body .= '"' . iconv("UTF-8", "ISO-8859-1", $value) . '",';
//            }
//            $body = substr($body, 0, -1) . "\n";
//        }
//        foreach ($registrants as $person) {
//            $body .= '"' . date("M d, Y h:i a", $person->registered_date) . '",';
//            foreach ($person->person as $value) {
////                $body .= '"' . iconv("UTF-8", "ISO-8859-1", $value) . '",';
//                $body .= '"' . $value . '",';
//            }
//            $body = substr($body, 0, -1) . "\n";
//        }
//        $out .= $body;
        $out .= formsController::sql2csv($registrants, $rpt_columns);

        $fn = str_replace(' ', '_', $event->title) . '_' . gt('Roster') . '.csv';

		// CREATE A TEMP FILE
		$tmpfname = tempnam(getcwd(), "rep"); // Rig

		$handle = fopen($tmpfname, "w");
		fwrite($handle,$out);
		fclose($handle);

		if(file_exists($tmpfname)) {
            // NO buffering from here on out or things break unexpectedly. - RAM
            ob_end_clean();

            // This code was lifted from phpMyAdmin, but this is Open Source, right?
            // 'application/octet-stream' is the registered IANA type but
            // MSIE and Opera seems to prefer 'application/octetstream'
            // It seems that other headers I've added make IE prefer octet-stream again. - RAM
            $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : 'text/comma-separated-values;';
            header('Content-Type: ' . $mime_type . ' charset=' . LANG_CHARSET. "'");
            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Content-length: '.filesize($tmpfname));
            header('Content-Transfer-Encoding: binary');
            header('Content-Encoding:');
            header('Content-Disposition: attachment; filename="' . $fn . '";');
            // IE need specific headers
            if (EXPONENT_USER_BROWSER == 'IE') {
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Vary: User-Agent');
            } else {
                header('Pragma: no-cache');
            }
            //Read the file out directly
            readfile($tmpfname);

//            if (DEVELOPMENT == 0) exit();
            unlink($tmpfname);
            exit();
        }
    }

    public function get_guest_controls($ajax = '') {  //FIXME this is never used
        $id    = $this->params['id'];
        $ctr   = $this->params['counter'];
        $event = new eventregistration($id);

        $str = "";
        foreach ($event->expDefinableField['guest'] as $field) {
            $str = $str . $event->showControl($field, $field->name . "_" . $ctr);
        }

        echo $str;
        exit();
    }

    function emailRegistrants() {

        if (empty($this->params['email_addresses'])) {
            flash('error', gt('Please add at least one email.'));
            expHistory::back();
        }

        if (empty($this->params['email_subject']) || empty($this->params['email_message'])) {
            flash('error', gt('Nothing to Send! Please enter subject and message.'));
            expHistory::back();
        }

//        $email_arr     = explode("|!|", $this->params['email_addresses']);
        $email_addy = array_flip(array_flip($this->params['email_addresses']));
        $email_addy = array_map('trim', $email_addy);
        $email_addy = array_filter($email_addy);

        $headers = array(
            "MIME-Version" => "1.0",
            "Content-type" => "text/html; charset=" . LANG_CHARSET
        );

        $mail = new expMail();

//        foreach ($this->params['expFile']['attachments'] as $attach) {
//            $expFile = new expFile($attach);
//            if (!empty($expFile->id)) {
//                $mail->attach_file_on_disk($expFile->path, $expFile->mimetype);
//            }
//        }
        if (!empty($_FILES['attach']['size'])) {
            $dir = 'tmp';
            $filename = expFile::fixName(time() . '_' . $_FILES['attach']['name']);
            $dest = $dir . '/' . $filename;
            //Check to see if the directory exists.  If not, create the directory structure.
            if (!file_exists(BASE.$dir)) expFile::makeDirectory($dir);
            // Move the temporary uploaded file into the destination directory, and change the name.
            $file = expFile::moveUploadedFile($_FILES['attach']['tmp_name'], BASE . $dest);
//            $finfo = finfo_open(FILEINFO_MIME_TYPE);
//                $relpath = str_replace(PATH_RELATIVE, '', BASE);
//            $ftype = finfo_file($finfo, BASE.$dest);
//            finfo_close($finfo);
            if (!empty($file)) $mail->attach_file_on_disk(BASE . $file, expFile::getMimeType(BASE . $file));
        }

        $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
        if (empty($from[0])) $from = SMTP_FROMADDRESS;
        $mail->quickBatchSend(array(
            	'headers'=>$headers,
                'html_message'=> $this->params['email_message'],
                'text_message'=> strip_tags(str_replace("<br>", "\r\n", $this->params['email_message'])),
                'to'          => $email_addy,
                'from'        => $from,
                'subject'     => $this->params['email_subject']
        ));
        if (!empty($file)) unlink(BASE . $file);  // delete temp file attachment
        flash('message', gt("You're email to event registrants has been sent."));
        expHistory::back();
    }

    /**
     * function to return event registrations as calendar events
     *
     * @param        $startdate
     * @param        $enddate
     * @param string $color
     *
     * @return array
     */
    static function getRegEventsForDates($startdate, $enddate, $color="#FFFFFF") {
        $er = new eventregistration();
        $events      = $er->find('all', 'product_type="eventregistration" && active_type=0');
        $pass_events = array();
        foreach ($events as $event) {
            if ($event->eventdate >= $startdate && $event->eventdate <= $enddate) {
                $newevent = new stdClass();
                $newevent->eventdate = new stdClass();
                $newevent->eventdate->date = $event->eventdate;
                $newevent->eventstart = $event->event_starttime + $event->eventdate;
                $newevent->eventend = $event->event_endtime + $event->eventdate;
                $newevent->title = $event->title;
                $newevent->body  = $event->body;
                $newevent->location_data = 'eventregistration';
                $newevent->color = $color;
                $newevent->expFile = $event->expFile['mainimage'];
                $pass_events[$event->eventdate][] = $newevent;
            }
        }
        return $pass_events;
    }

        /*
    * Helper function for the Calendar view
    */
    function getEventsForDates($edates, $sort_asc = true) {
        global $db;

        $events = array();
        foreach ($edates as $edate) {
//            if (!isset($this->params['cat'])) {
//                if (isset($this->params['title']) && is_string($this->params['title'])) {
//                    $default_id = $db->selectValue('storeCategories', 'id', "sef_url='" . $this->params['title'] . "'");
//                } elseif (!empty($this->config['category'])) {
//                    $default_id = $this->config['category'];
//                } elseif (ecomconfig::getConfig('show_first_category')) {
//                    $default_id = $db->selectValue('storeCategories', 'id', 'lft=1');
//                } else {
//                    $default_id = 0;
//                }
//            }
//
//            $parent = isset($this->params['cat']) ? intval($this->params['cat']) : $default_id;
//
//            $category = new storeCategory($parent);

            $sql = 'SELECT DISTINCT p.*, er.event_starttime, er.signup_cutoff FROM ' . $db->prefix . 'product p ';
//            $sql .= 'JOIN ' . $db->prefix . 'product_storeCategories sc ON p.id = sc.product_id ';
            $sql .= 'JOIN ' . $db->prefix . 'eventregistration er ON p.product_type_id = er.id ';
            $sql .= 'WHERE 1 ';
//            $sql .= ' AND sc.storecategories_id IN (SELECT id FROM exponent_storeCategories WHERE rgt BETWEEN ' . $category->lft . ' AND ' . $category->rgt . ')';
//            if ($category->hide_closed_events) {
//                $sql .= ' AND er.signup_cutoff > ' . time();
//            }
//            $sql .= ' AND er.id = ' . $edate->id;
            $sql .= ' AND er.id = ' . $edate->product_type_id;

            $order = 'event_starttime';
            $dir = 'ASC';

            $o = $db->selectObjectBySql($sql);
            $o->eventdate = $edate->eventdate;
            $o->eventstart = $edate->event_starttime + $edate->eventdate;
            $o->eventend = $edate->event_endtime + $edate->eventdate;
            $o->expFile = $edate->expFile;
            $events[] = $o;
        }
        $events = expSorter::sort(array('array' => $events, 'sortby' => 'eventstart', 'order' => $sort_asc ? 'ASC' : 'DESC'));
        return $events;
    }

    // create a pseudo global view_registrants permission
    public static function checkPermissions($permission,$location) {
        global $exponent_permissions_r, $router;

        // only applies to the 'view_registrants' method
        if (empty($location->src) && empty($location->int) && $router->params['action'] == 'view_registrants') {
            if (!empty($exponent_permissions_r['eventregistration'])) foreach ($exponent_permissions_r['eventregistration'] as $page) {
                foreach ($page as $pageperm) {
                    if (!empty($pageperm['view_registrants']) || !empty($pageperm['manage'])) return true;
                }
            }
        }
        return false;
    }

}

?>