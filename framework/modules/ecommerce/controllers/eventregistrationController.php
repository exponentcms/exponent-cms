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
        'showall' => 'Show all events',
//        'showByTitle' => "Show events by title",
    );

    // hide the configs we don't need
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'files',
//        'module_title',
        'rss',
        'tags'
    );  // all options: ('aggregation','categories','comments','ealerts','files','pagination','rss','tags')

    public $add_permissions = array(
        'view_registrants' => 'View Registrants',
    );

    static function displayname() {
        return gt("Online Event Registration");
    }

    static function description() {
        return gt("Manage event registrations on your website");
    }

    function showall() {
        global $user;

        expHistory::set('viewable', $this->params);
        $limit = (!empty($this->config['limit'])) ? $this->config['limit'] : 10;

        if ($user->isAdmin()) {
            $pass_events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
        } else {
            $events = $this->eventregistration->find('all', 'product_type="eventregistration" && active_type=0', "title ASC", $limit);
            $pass_events = array();

            foreach ($events as $event) {
                // $this->signup_cutoff > time()
                if ($event->signup_cutoff > time()) {
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
            'records'    => $pass_events,
            'limit'      => $limit,
            'order'      => "title ASC",
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
            'src'        => $this->loc->src,
            'view'       => empty($this->params['view']) ? null : $this->params['view'],
            'columns'    => array(
                gt('Event') => 'title',
                gt('Date')  => 'eventdate',
                gt('Seats') => 'quantity'
            ),
        ));
        assign_to_template(array(
            'page' => $page
        ));
    }

    function manage() {
        global $user;

        expHistory::set('viewable', $this->params);
        $limit = (!empty($this->config['limit'])) ? $this->config['limit'] : 10;

        if ($user->isAdmin()) {
            $pass_events = $this->eventregistration->find('all', 'product_type="eventregistration"', "title ASC", $limit);
        } else {
            $events = $this->eventregistration->find('all', 'product_type="eventregistration" && active_type=0', "title ASC", $limit);
            $pass_events = array();

            foreach ($events as $event) {
                // $this->signup_cutoff > time()
                if ($event->signup_cutoff > time()) {
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
            'records'    => $pass_events,
            'limit'      => $limit,
            'order'      => "title ASC",
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->params['controller'],
            'action'     => $this->params['action'],
            'src'        => $this->loc->src,
            'view'       => empty($this->params['view']) ? null : $this->params['view'],
            'columns'    => array(
                gt('Event') => 'title',
                gt('Date')  => 'eventdate',
                gt('Seats') => 'quantity'
            ),
        ));
        assign_to_template(array(
            'page' => $page
        ));
    }

    function metainfo() {
        global $router;
        if (empty($router->params['action'])) return false;

        // figure out what metadata to pass back based on the action we are in.
//        $action   = $_REQUEST['action'];
        $action   = $router->params['action'];
        $metainfo = array('title' => '', 'keywords' => '', 'description' => '');
        switch ($action) {
            case 'donate':
                $metainfo['title'] = 'Make a eventregistration';
                $metainfo['keywords'] = 'donate online';
                $metainfo['description'] = "Make a eventregistration";
                break;
            default:
                $metainfo = array('title' => $this->displayname() . " - " . SITE_TITLE, 'keywords' => SITE_KEYWORDS, 'description' => SITE_DESCRIPTION);
        }

        return $metainfo;
    }

    function index() {
        redirect_to(array('controller' => 'eventregistrations', 'action' => 'showall'));
    }

    function show() {
        global $order, $db, $template, $user;

        expHistory::set('viewable', $this->params);
        if (!empty($this->params['token'])) {
            $record = expSession::get("last_POST_Paypal");
        } else {
            $record = expSession::get("last_POST");
        }
        $id = isset($this->params['title']) ? addslashes($this->params['title']) : $this->params['id'];
        $product = new eventregistration($id);

        //TODO should we pull in an existing reservation already in the cart to edit? e.g., the registrants
//        $product_type = new stdClass();
//        if ($product->active_type == 1) {
//            $product_type->user_message = "This product is temporarily unavailable for purchase.";
//        } elseif ($product->active_type == 2 && !$user->isAdmin()) {
//            flash("error", $product->title . " " . gt("is currently unavailable."));
//            expHistory::back();
//        } elseif ($product->active_type == 2 && $user->isAdmin()) {
//            $product_type->user_message = $product->title . " is currently marked as unavailable for registration or display.  Normal users will not see this product.";
//        }

        $registrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND event_id =" . $product->id);
        $order_registrations = array();
        if (!empty($registrants)) foreach ($registrants as $registrant) {
            $order_registrations[] = expUnserialize($registrant->value);
        }

        //eDebug($product, true);
        assign_to_template(array(
            'product'    => $product,
            'record'     => $record,
            'registered' => $order_registrations,
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
        $product_type = new stdClass();
        if ($product->active_type == 1) {
            $product_type->user_message = "This product is temporarily unavailable for purchase.";
        } elseif ($product->active_type == 2 && !$user->isAdmin()) {
            flash("error", $product->title . " " . gt("is currently unavailable."));
            expHistory::back();
        } elseif ($product->active_type == 2 && $user->isAdmin()) {
            $product_type->user_message = $product->title . " is currently marked as unavailable for registration or display.  Normal users will not see this product.";
        }

        //eDebug($product, true);
        assign_to_template(array(
            'product' => $product,
            'record'  => $record
        ));
    }

    function eventregistration_process() {
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
        expSession::set('paypal_link', makeLink(array('controller' => 'eventregistration', 'action' => 'show', 'title' => $product->sef_url)));

        //Validation for customValidation
        foreach ($this->params['event'] as $key => $value) {
            $expField = $db->selectObject("expDefinableFields", "name = '{$key}'");
            $expFieldData = expUnserialize($expField->data);
            if (!empty($expFieldData->customvalidation)) {

                $customValidation = "is_valid_" . $expFieldData->customvalidation;
                $fieldname = $expField->name;
                $obj = new stdClass();
                $obj->$fieldname = $value;
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

        //Validation for ticker types
        if (isset($this->params['ticket_types']) && empty($this->params['options'])) {
            expValidator::failAndReturnToForm("Invalid ticket types.", $this->params);
        }

//        if (!empty($this->params['event'])) {
        $sess_id = session_id();
//            $sess_id = expSession::getTicketString();
        $data = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order->id}' AND event_id =" . $this->params['eventregistration']['product_id']);
        if (!empty($data)) {
            foreach ($data as $item) {
                if (!empty($this->params['event'][$item->control_name])) {
                    $item->value = $this->params['event'][$item->control_name];
                    $db->updateObject($item, "eventregistration_registrants");
                }
            }
        } else {
            if (!empty($this->params['event'])) foreach ($this->params['event'] as $key => $value) {
                $obj = new stdClass();
                $obj->event_id = $this->params['eventregistration']['product_id'];
                $obj->control_name = $key;
                $obj->value = $value;
                $obj->connector_id = $order->id;
                $obj->registered_date = time();
                $db->insertObject($obj, "eventregistration_registrants");
            } else {
                $obj = new stdClass();
                $obj->event_id = $this->params['eventregistration']['product_id'];
                $obj->connector_id = $order->id;
                $obj->registered_date = time();
                $db->insertObject($obj, "eventregistration_registrants");
            }
        }
        expSession::set('session_id', $sess_id);
//        }

        //Add to Cart
        $product_id = $this->params['eventregistration']['product_id'];
        $product_type = "eventregistration";
        $product = new $product_type($product_id, true, true);

        if ($this->params['options']) {
            $this->params['eventregistration']['options'] = $this->params['options'];
            $this->params['eventregistration']['options_quantity'] = $this->params['options_quantity'];
            $product->addToCart($this->params['eventregistration']);
        } else {
            $this->params['eventregistration']['qtyr'] = $this->params['theValue'] + 1;
            $product->addToCart($this->params['eventregistration']);
        }

        $order->setOrderType($this->params);
        $order->setOrderStatus($this->params);
        $order->calculateGrandTotal();

        $billing = new billing();
        $result = $billing->calculator->preprocess($billing->billingmethod, $opts, $this->params, $order); //FIXME $opts doesn't exist
        redirect_to(array('controller' => 'cart', 'action' => 'preprocess'));
    }

    function delete() {
        redirect_to(array('controller' => 'eventregistrations', 'action' => 'showall'));
    }

    public function delete_registrant() {
        global $db;
        $connector_id = $this->params['connector_id'];

        $db->delete("eventregistration_registrants", "connector_id='{$connector_id}'");
        flash('message', gt("Registrant successfully deleted."));
        expHistory::back();
    }

    public function edit_registrant() {
        global $db;

        $event_id = $this->params['event_id'];
        $connector_id = @$this->params['connector_id'];
        if (empty($connector_id)) {
            $connector_id = "admin-created" . rand() . time(); //Meaning it is been added by admin
        }
        $reg_data = $db->selectObjects("eventregistration_registrants", "connector_id ='{$connector_id}'");
        $registrant = array();
        foreach ($reg_data as $item) {
            $registrant[$item->control_name] = $item->value;
        }

        $event = new eventregistration($event_id);

        // eDebug($registrant, true);
        assign_to_template(array(
            'registrant'   => $registrant,
            'event'        => $event,
            'connector_id' => $connector_id
        ));
    }

    public function update_registrant() {
        global $db;
        $event_id = $this->params['event_id'];
        $connector_id = $this->params['connector_id'];
        $fields = $this->params['event'];
        $obj = '';
        // eDebug($fields);
        foreach ($fields as $key => $value) {
            $obj = $db->selectObject("eventregistration_registrants", "event_id = '{$event_id}' AND connector_id = '{$connector_id}' AND control_name='{$key}'");

            if (!empty($obj)) {
                $obj->value = $value;
                if (!empty($value)) {
                    $db->updateObject($obj, 'eventregistration_registrants');
                } else {
                    $db->delete('eventregistration_registrants', 'id=' . $obj->id);
                }
            } else {
                if (!empty($value)) {
                    $reg = new stdClass();
                    $reg->event_id = $event_id;
                    $reg->control_name = $key;
                    $reg->value = $value;
                    $reg->connector_id = $connector_id;
                    $reg->registered_date = time();

                    $db->insertObject($reg, 'eventregistration_registrants');
                }

            }
        }
        // exit();
        redirect_to(array('controller' => 'eventregistration', 'action' => 'view_registrants', 'id' => $event_id));
    }

    public function export() {
        global $db;

        $event = new eventregistration($this->params['id']);
        $sql = "SELECT connector_id FROM " . DB_TABLE_PREFIX . "_eventregistration_registrants GROUP BY connector_id";
        $order_ids_complete = $db->selectColumn("eventregistration_registrants", "connector_id", "connector_id <> '0' AND event_id = {$event->id}", "registered_date", true);

        foreach ($order_ids_complete as $item) {
            $odr = $db->selectObject("orders", "id = {$item} and invoice_id <> 0");
            if (!empty($odr) || strpos($item, "admin-created") !== false) {
                $order_ids[] = $item;
            }
        }

        $header = array();
        $control_names = array();
        $header[] = '"Date Registered"';
        //Check if it has ticket types
        if ($event->hasOptions()) {
            $header[] = '"Ticket Types"'; //Add some configuration here
        }

        if (!empty($event->expDefinableField['registrant'])) foreach ($event->expDefinableField['registrant'] as $field) {
            $data = expUnserialize($field->data);
            if (!empty($data->caption)) {
                $header[] = '"' . $data->caption . '"';
            } else {
                $header[] = '"' . $field->name . '"';
            }
            $control_names[] = $field->name;
        }

        if ($event->num_guest_allowed > 0) {
            for ($i = 1; $i <= $event->num_guest_allowed; $i++) {
                if (!empty($event->expDefinableField['guest'])) foreach ($event->expDefinableField['guest'] as $field) {
                    $data = expUnserialize($field->data);
                    if (!empty($data->caption)) {
                        $header[] = $data->caption . "_$i";
                    } else {
                        $header[] = $field->name . "_$i";
                    }
                    $control_names[] = $field->name . "_$i";
                }

            }
        }

        // new method to check for guests/registrants
        if (!empty($event->num_guest_allowed)) {
            $registered = array();
            if (!empty($order_ids)) foreach ($order_ids as $order_id) {
                $newregistrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order_id}'");
                $registered = array_merge($registered, $newregistrants);
            }
//            $registrants = array();
            foreach ($registered as $key => $person) {
                $registered[$key]->person = expUnserialize($person->value);
            }
            $header[] = '"Name"';
            $header[] = '"Phone"';
            $header[] = '"Email"';
        }

        if (LANG_CHARSET == 'UTF-8') {
            $out = chr(0xEF) . chr(0xBB) . chr(0xBF); // add utf-8 signature to file to open appropriately in Excel, etc...
        } else {
            $out = "";
        }
        $out .= implode(",", $header);
        $out .= "\n";
        $body = '';
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
        foreach ($registered as $person) {
            $body .= '"' . date("M d, Y h:i a", $person->registered_date) . '",';
            foreach ($person->person as $value) {
//                $body .= '"' . iconv("UTF-8", "ISO-8859-1", $value) . '",';
                $body .= '"' . $value . '",';
            }
            $body = substr($body, 0, -1) . "\n";
        }
        $out .= $body;

//        $fp = BASE . 'tmp/';
        $fn = str_replace(' ', '_', $event->title) . '_' . gt('Roster') . '.csv';
//        $f  = fopen($fp . $fn, 'w');
//        // Put all values from $out to export.csv.
//        fputs($f, $out);
//        fclose($f);

        // CREATE A TEMP FILE
        $tmpfname = tempnam(getcwd(), "rep"); // Rig

        $handle = fopen($tmpfname, "w");
        fwrite($handle, $out);
        fclose($handle);

        if (file_exists($tmpfname)) {
            // NO buffering from here on out or things break unexpectedly. - RAM
            ob_end_clean();

            // This code was lifted from phpMyAdmin, but this is Open Source, right?
            // 'application/octet-stream' is the registered IANA type but
            // MSIE and Opera seems to prefer 'application/octetstream'
            // It seems that other headers I've added make IE prefer octet-stream again. - RAM
            $mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : 'text/comma-separated-values;';
            header('Content-Type: ' . $mime_type . ' charset=' . LANG_CHARSET . "'");
            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header("Content-length: " . filesize($tmpfname));
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

            readfile($tmpfname);
            if (DEVELOPMENT == 0)
                exit();
        }
    }

    public function get_guest_controls($ajax = '') {
        $id = $this->params['id'];
        $ctr = $this->params['counter'];
        $event = new eventregistration($id);

        $str = "";
        foreach ($event->expDefinableField['guest'] as $field) {
            $str = $str . $event->getControl($field, $field->name . "_" . $ctr);
        }

        echo $str;
        exit();
    }

    function view_registrants() {
        global $db;

        expHistory::set('viewable', $this->params);

        $event = new eventregistration($this->params['id']);

        //Get all the registrants in the event using order id
        $order_ids_complete = $db->selectColumn("eventregistration_registrants", "connector_id", "connector_id <> '0' AND event_id = {$event->id}", "registered_date", true);

        foreach ($order_ids_complete as $item) {
//            $odr = $db->selectObject("orders", "id = {$item} and invoice_id <> 0");
            $odr = $db->selectObject("orders", "id ='{$item}' and invoice_id <> 0");
            if (!empty($odr) || strpos($item, "admin-created") !== false) {
                $order_ids[] = $item;
            }
        }

        $header = array();
        $control_names = array();
        $header[] = 'Date Registered';
        //Check if it has ticket types
        if ($event->hasOptions()) {
            $header[] = "Types"; //Add some configuration here
        }
        //Get the input labels as table headers
        if (!empty($event->expDefinableField['registrant'])) foreach ($event->expDefinableField['registrant'] as $field) {
            $data = expUnserialize($field->data);
            if (!empty($data->caption)) {
                $header[] = $data->caption;
            } else {
                $header[] = $field->name;
            }
            $control_names[] = $field->name;
        }

        //Check if there are guests
        if (!empty($event->num_guest_allowed)) {
            for ($i = 1; $i <= $event->num_guest_allowed; $i++) {
                if (!empty($event->expDefinableField['guest'])) foreach ($event->expDefinableField['guest'] as $field) {
                    $data = expUnserialize($field->data);
                    if (!empty($data->caption)) {
                        $header[] = $data->caption . "_$i";
                    } else {
                        $header[] = $field->name . "_$i";
                    }
                    $control_names[] = $field->name . "_$i";
                }
            }
        }

        // new method to check for guests/registrants
        if (!empty($event->num_guest_allowed)) {
            $registered = array();
            if (!empty($order_ids)) foreach ($order_ids as $order_id) {
                $newregistrants = $db->selectObjects("eventregistration_registrants", "connector_id ='{$order_id}'");
                $registered = array_merge($registered, $newregistrants);
            }
            $registrants = array();
            foreach ($registered as $person) {
                $registrants[] = expUnserialize($person->value);
            }
        }

        //Get the data and registrant emails
        $email = array();
        $num_of_guest_fields = 0;
        $num_of_guest = 0;
        $num_of_guest_total = 0;

        $body = array();
        if (!empty($order_ids)) foreach ($order_ids as $order_id) {
            $body[$order_id][] = date("M d, Y h:i a", $db->selectValue("eventregistration_registrants", "registered_date", "event_id = {$event->id} AND connector_id = '{$order_id}'"));
            if ($event->hasOptions()) {
                $or = new order($order_id);
                $orderitem = new orderitem();
                if (isset($or->orderitem[0])) {
                    $body[$order_id][] = $orderitem->getOption($or->orderitem[0]->options);
                } else {
                    $body[$order_id][] = '';
                }
            }
            foreach ($control_names as $control_name) {
                $value = $db->selectValue("eventregistration_registrants", "value", "event_id = {$event->id} AND control_name ='{$control_name}' AND connector_id = '{$order_id}'");
                $body[$order_id][] = $value;
                if (expValidator::isValidEmail($value) === true) {
                    $email[$value] = $value;
                }
            }

            if (!empty($order_id)) {
                $num_of_guest_total += $db->countObjects("eventregistration_registrants", "event_id ={$event->id} AND control_name LIKE 'guest_%' AND connector_id = '{$order_id}'");
            }

        } else $order_ids = array();

        $num_of_guest_fields = $db->countObjects("content_expDefinableFields", "content_id ={$event->id} AND subtype='guest'");
        if ($num_of_guest_fields <> 0) {
            $num_of_guest = $num_of_guest_total / $num_of_guest_fields;
        } else {
            $num_of_guest = 0;
        }

        //Removed duplicate emails
        $email = array_unique($email);

        $registered = count($order_ids) + $num_of_guest;
        if (!empty($event->registrants)) {
            $event->registrants = expUnserialize($event->registrants);
        } else {
            $event->registrants = array();
        }

        $event->number_of_registrants = $registered;
        assign_to_template(array(
            'event'       => $event,
            'registrants' => $registrants,
//            'header'=> $header,
//            'body'=> $body,
//            'email'=> $email
        ));
    }

    function emailRegistrants() {

        if (empty($this->params['email_addresses'])) {
            flash('error', gt('Please add at least one email.'));
            expHistory::back();
        }

        if (empty($this->params['email_subject'])) {
            flash('error', gt('Please enter your email subject.'));
            expHistory::back();
        }

        $email_arr = explode("|!|", $this->params['email_addresses']);
        $email_subject = $this->params['email_subject'];
        $email_message = $this->params['email_message'];

        $mail = new expMail();

        foreach ($this->params['expFile']['attachments'] as $attach) {
            $expFile = new expFile($attach);
            if (!empty($expFile->id)) {
                $mail->attach_file_on_disk($expFile->path, $expFile->mimetype);
            }
        }

        foreach ($email_arr as $email_addy) {
            $mail->quickSend(array(
                'html_message' => $email_message,
                'text_message' => str_replace("<br>", "\r\n", $email_message),
                'to'           => $email_addy,
                'from'         => ecomconfig::getConfig('from_address'),
                'subject'      => $email_subject
            ));
        }

        flash('message', gt("You're email has been successfully sent."));
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
    static function getEventsForDates($startdate, $enddate, $color = "#FFFFFF") {
        $er = new eventregistration();
        $events = $er->find('all', 'product_type="eventregistration" && active_type=0');
        $pass_events = array();
        foreach ($events as $event) {
            if ($event->eventdate >= $startdate && $event->eventdate <= $enddate) {
                $newevent = new stdClass();
                $newevent->eventdate = new stdClass();
                $newevent->eventdate->date = $event->eventdate;
                $newevent->eventstart = $event->event_starttime + $event->eventdate;
                $newevent->eventend = $event->event_endtime + $event->eventdate;
                $newevent->title = $event->title;
                $newevent->body = $event->body;
                $newevent->location_data = 'eventregistration';
                $newevent->color = $color;
                $newevent->expFile = $event->expFile['mainimage'];
                $pass_events[$event->eventdate][] = $newevent;
            }
        }
        return $pass_events;
    }

    // create a pseudo global view_registrants permission
    public static function checkPermissions($permission, $location) {
        global $exponent_permissions_r, $user, $db, $router;

        // only applies to the 'view_registrants' method
        if (empty($location->src) && empty($location->int) && $router->params['action'] == 'view_registrants') {
            if (!empty($exponent_permissions_r['eventregistrationController'])) foreach ($exponent_permissions_r['eventregistrationController'] as $page) {
                foreach ($page as $pageperm) {
                    if (!empty($pageperm['view_registrants']) || !empty($pageperm['manage'])) return true;
                }
            }
        }
        return false;
    }

}

?>