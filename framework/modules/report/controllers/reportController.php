<?php

##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
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

class reportController extends expController {
    protected $manage_permissions = array(
        'abandoned_carts' => 'Abandoned Carts Report',
        'batch_export' => 'Export Products',
        'cart_summary' => 'View Cart Summary Report',
        'current_carts' => 'Current Carts Report',
        'dashboard' => 'View the e-Commerce Dashboard',
        'download' => 'Download Report',
        'generateOrderReport' => 'View Order Report',
        'generateProductReport' => 'View Product Report',
        'order_report' => 'Generate Order Report',
        'payment_report' => 'Generate Payment Report',
        'print_orders' => 'Print Orders',
        'product_report' => 'Generate Product Report',
        'purge_abandoned_carts' => 'Purge Abandoned Carts',
        'show_payment_summary' => 'Show Payment Summary',
        'status_export' => 'Export Status',
    );

    static function displayname() {
        return gt("e-Commerce Report Builder");
    }

    static function description() {
        return gt("Build reports for your store");
    }

    static function author() {
        return "Phillip Ball - OIC Group, Inc";
    }

    static function hasSources() {
        return false;
    }

    protected $o;
    protected $oneday = 86400;
    protected $tstart;
    protected $tend;
    protected $prev_date;
    protected $prev_hour;
    protected $prev_min;
    protected $prev_ampm;
    protected $now_date;
    protected $now_hour;
    protected $now_min;
    protected $now_ampm;
    protected $quickrange = array();

    // quick range constants
    const PAST_24_HOURS = 0;
    const PAST_7_DAYS = 1;
    const PAST_30_DAYS = 2;
    const LAST_MONTH = 3;
    const PAST_60_DAYS = 4;
    const PAST_90_DAYS = 5;
    const PAST_365_DAYS = 6;
    const LAST_YEAR = 7;
    const FOREVER = 8;
    const CUSTOM = 9;

    function __construct($src = null, $params = array()) {
        parent::__construct($src, $params);
        $this->o = new order();
        $this->tstart = time() - $this->oneday;
        $this->tend = time();
//        $this->prev_date = strftime("%A, %d %B %Y", mktime(0,0,0,(strftime("%m")-1),1,strftime("%Y")));
//        $this->now_date = strftime("%A, %d %B %Y");
        $this->prev_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $this->tstart);
        $this->prev_hour = date('h', $this->tstart);
        $this->prev_min = date('i', $this->tstart);
        $this->prev_ampm = date('A', $this->tstart);

        $this->now_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $this->tend);
        $this->now_hour = date('h');
        $this->now_min = date('i');
        $this->now_ampm = date('A');
        $this->quickrange = array(
            $this::PAST_24_HOURS => gt('Past 24 Hours'),
            $this::PAST_7_DAYS => gt('Past 7 Days'),
            $this::PAST_30_DAYS => gt('Past 30 Days'),
            $this::LAST_MONTH => gt('Last Month'),
            $this::PAST_60_DAYS => gt('Past 60 Days'),
            $this::PAST_90_DAYS => gt('Past 90 Days'),
            $this::PAST_365_DAYS => gt('Past 365 Days'),
            $this::LAST_YEAR => gt('Last Year'),
            $this::FOREVER => gt('Forever'),
            $this::CUSTOM => gt('Custom Range'),
        );
    }

    /**
     * Help parse quick range input
     */
    private function setDateParams() {
        if (isset($this->params['quickrange'])) {
            if ($this->params['quickrange'] == $this::LAST_MONTH) {
                $this->tstart = strtotime('first day of last month');
                $this->tstart = strtotime('today', $this->tstart);
                $this->tend = strtotime('first day of this month');
                $this->tend = strtotime('midnight', $this->tend);
            } elseif ($this->params['quickrange'] == $this::PAST_24_HOURS) {
                $this->tstart = time() - $this->oneday;
            } elseif ($this->params['quickrange'] == $this::PAST_7_DAYS) {
                $this->tstart = time() - $this->oneday * 7;
            } else if ($this->params['quickrange'] == $this::PAST_30_DAYS) {
                $this->tstart = time() - $this->oneday * 30;
            } elseif ($this->params['quickrange'] == $this::LAST_MONTH) {
                $this->tstart = strtotime('first day of last month');
                $this->tstart = strtotime('today', $this->tstart);
                $this->tend = strtotime('first day of this month');
                $this->tend = strtotime('midnight', $this->tend);
            } else if ($this->params['quickrange'] == $this::PAST_60_DAYS) {
                $this->tstart = time() - $this->oneday * 60;
            } else if ($this->params['quickrange'] == $this::PAST_90_DAYS) {
                $this->tstart = time() - $this->oneday * 90;
            } else if ($this->params['quickrange'] == $this::PAST_365_DAYS) {
                $this->tstart = time() - $this->oneday * 365;
            } else if ($this->params['quickrange'] == $this::LAST_YEAR) {
                $this->tstart = expDateTime::startOfYearTimestamp(strtotime("-1 year"));
                $this->tend = expDateTime::endOfYearTimestamp(strtotime("-1 year"));
            } else if ($this->params['quickrange'] == $this::FOREVER) {
                $this->tstart = 0;
            }
            $this->prev_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT),$this->tstart);
            $this->now_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $this->tend);
            $this->prev_hour = date('h', $this->tstart);
            $this->prev_min = date('i', $this->tstart);
            $this->prev_ampm = date('a', $this->tstart);
            $this->now_hour = date('h', $this->tend);
            $this->now_min = date('i', $this->tend);
            $this->now_ampm = date('a', $this->tend);
        } elseif (isset($this->params['date-starttime'])) {  //FIXME OLD calendar control format
            $formatedStart = $this->params['date-starttime'] . ' ' . $this->params['time-h-starttime'] . ":" . $this->params['time-m-starttime'] . ' ' . $this->params['ampm-starttime'];
            $this->tstart = strtotime($formatedStart);
            $this->tend = strtotime($this->params['date-endtime'] . ' ' . $this->params['time-h-endtime'] . ":" . $this->params['time-m-endtime'] . ' ' . $this->params['ampm-endtime']);

            // parse out date into calendarcontrol fields
            $this->prev_date = $formatedStart;
            $this->prev_hour = $this->params['time-h-starttime'];
            $this->prev_min = $this->params['time-m-starttime'];
            $this->prev_ampm = $this->params['ampm-starttime'];

            // parse out date into calendarcontrol fields
            $this->now_date = $this->params['date-endtime'];
            $this->now_hour = $this->params['time-h-endtime'];
            $this->now_min = $this->params['time-m-endtime'];
            $this->now_ampm = $this->params['ampm-endtime'];
            $this->params['quickrange'] = $this::CUSTOM;
        } elseif (isset($this->params['starttime'])) {
            $this->tstart = strtotime($this->params['starttime']);
            $this->tend = strtotime($this->params['endtime']);

            // parse out date into calendarcontrol fields
            $this->prev_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $this->tstart);
            $this->prev_hour = date('h', $this->tstart);
            $this->prev_min = date('i', $this->tstart);
            $this->prev_ampm = date('a', $this->tstart);

            // parse out date into calendarcontrol fields
            $this->now_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), $this->tend);
            $this->now_hour = date('h', $this->tend);
            $this->now_min = date('i', $this->tend);
            $this->now_ampm = date('a', $this->tend);
            $this->params['quickrange'] = $this::CUSTOM;
        } else {
            $this->tstart = time() - $this->oneday;
            $this->params['quickrange'] = $this::PAST_24_HOURS;
        }
        return;
    }

    /**
     * Current Stats
     */
    function dashboard() {
        global $db;

        // get number of active carts
        $sql = "SELECT COUNT(*) AS c FROM " . $db->tableStmt('orders') . ", " . $db->tableStmt('sessionticket') . " WHERE ticket = sessionticket_ticket AND purchased = 0";
        $allCarts = $db->countObjectsBySql($sql);

        // get latest 5 orders
        $order = new order();
        $recent_orders = $order->find('all', 'purchased !=0', 'purchased DESC', 5);

        // get number of online customers in last 30 minutes
        $customers_online = $db->countObjects('sessionticket', 'last_active > ' . (time() - (30 * 60 * 1000)));

        assign_to_template(array(
            'recent'             => $recent_orders,
            'online'             => $customers_online,
            'active_carts'       => $allCarts
        ));
    }

    /**
     * Stats for selected period
     */
    function stats() {
        global $db;

//        if (!isset($this->params['quickrange']) && !isset($this->params['date-starttime']) && !isset($this->params['starttime'])) {
//            $this->params['quickrange'] = $this::LAST_MONTH;
//        }
        $this->setDateParams();

        // get number of online customers in period
        $u = new user();
        $new_customers = $u->find('count', 'created_on >= ' . $this->tstart . ' AND created_on <= ' . $this->tend);

        // get order stats for period
        $except = array('order_discounts', 'billingmethod', 'order_status_changes', 'billingmethod', 'order_discounts');
        $total = $db->countObjects('orders', 'purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend);
        $oar = array();
        for ($i = 0; $i < $total; $i += 100) {
            $orders = $this->o->find('all', 'purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend, null, 100, $i, true, false, $except, true);
            foreach ($orders as $order) {
                //eDebug($order,true);
                if (empty($oar[$order->order_type->title])) {
                    $oar[$order->order_type->title] = array();
                    $oar[$order->order_type->title]['grand_total'] = null;
                    $oar[$order->order_type->title]['num_orders'] = null;
                    $oar[$order->order_type->title]['num_items'] = null;
                }
                $oar[$order->order_type->title]['grand_total'] += $order->total;
                $oar[$order->order_type->title]['num_orders']++;
                $oar[$order->order_type->title]['num_items'] += count($order->orderitem);

                if (empty($oar[$order->order_type->title][$order->order_status->title])) {
                    $oar[$order->order_type->title][$order->order_status->title] = array();
                    $oar[$order->order_type->title][$order->order_status->title]['grand_total'] = null;
                    $oar[$order->order_type->title][$order->order_status->title]['num_orders'] = null;
                    $oar[$order->order_type->title][$order->order_status->title]['num_items'] = null;
                }
                $oar[$order->order_type->title][$order->order_status->title]['grand_total'] += $order->total;
                $oar[$order->order_type->title][$order->order_status->title]['num_orders']++;
                $oar[$order->order_type->title][$order->order_status->title]['num_items'] += count($order->orderitem);
            }
        }

        assign_to_template(array(
            'orders'             => $oar,
            'new'                => $new_customers,
            'quickrange'         => $this->quickrange,
            'quickrange_default' => $this->params['quickrange'],
            'prev_date'          => $this->prev_date,
            'now_date'           => $this->now_date,
            'now_hour'           => $this->now_hour,
            'now_min'            => $this->now_min,
            'now_ampm'           => $this->now_ampm,
            'prev_hour'          => $this->prev_hour,
            'prev_min'           => $this->prev_min,
            'prev_ampm'          => $this->prev_ampm,
        ));
    }

    /**
     * FIXME Purpose not known/incomplete
     */
    function cart_summary() {
        global $db;

//        if (!isset($this->params['quickrange']) && !isset($this->params['date-starttime']) && !isset($this->params['starttime'])) {
//            $this->params['quickrange'] = $this::LAST_MONTH;
//        }
        $this->setDateParams();

        $p = $this->params;
        $sql = "SELECT DISTINCT(o.id), o.invoice_id, " . $db->datetimeStmt('o.purchased') . " AS purchased_date, b.firstname AS bfirst, b.lastname AS blast, " . $db->currencyStmt('o.grand_total') . " AS grand_total, os.title AS status_title FROM ";
        $sql .= $db->tableStmt('orders') . " AS o ";
        $sql .= "INNER JOIN " . $db->tableStmt('orderitems') . " AS oi ON oi.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('product') . " AS p ON oi.product_id = p.id ";
        if (!empty($p['order_status'][0]) && $p['order_status'][0] != -1) $sql .= "INNER JOIN " . $db->tableStmt('order_type') . " AS ot ON o.order_type_id = ot.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('order_status') . " AS os ON os.id = o.order_status_id ";
        $sql .= "INNER JOIN " . $db->tableStmt('billingmethods') . " AS b ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('shippingmethods') . " AS s ON s.id = oi.shippingmethods_id ";
        $sql .= "INNER JOIN " . $db->tableStmt('geo_region') . " AS gr ON (gr.id = b.state OR gr.id = s.state) ";
        if (!empty($p['discounts'][0]) && $p['discounts'][0] != -1) $sql .= "LEFT JOIN " . $db->tableStmt('order_discounts') . " AS od ON od.orders_id = o.id ";

        $sqlwhere = "WHERE o.purchased != 0";

        if (!empty($p['date-startdate'])) $sqlwhere .= " AND o.purchased >= " . strtotime($p['date-startdate'] . " " . $p['time-h-startdate'] . ":" . $p['time-m-startdate'] . " " . $p['ampm-startdate']);
        /*if ($p->['time-h-startdate'] == )
        if ($p->['time-m-startdate'] == )
        if ($p->['ampm-startdate'] == )*/

        if (!empty($p['date-enddate'])) $sqlwhere .= " AND o.purchased <= " . strtotime($p['date-enddate'] . " " . $p['time-h-enddate'] . ":" . $p['time-m-enddate'] . " " . $p['ampm-enddate']);
        /*if ($p->['date-enddate'] == )
        if ($p->['time-h-enddate'] == )
        if ($p->['time-m-enddate'] == )
        if ($p->['ampm-enddate'] == )*/

        $inc = 0;
        $sqltmp = '';
        if (!empty($p['order_status'])) foreach ($p['order_status'] as $os) {
            $os = expString::escape($os);
            if ($os == -1) continue;
            else if ($inc == 0) {
                $inc++;
                $sqltmp .= " AND (o.order_status_id = " . $os;
            } else {
                $sqltmp .= " OR o.order_status_id = " . $os;
            }
        }
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";

        $inc = 0;
        $sqltmp = '';
        if (!empty($p['order_type'])) foreach ($p['order_type'] as $ot) {
            $ot = expString::escape($ot);
            if ($ot == -1) continue;
            else if ($inc == 0) {
                $inc++;
                $sqltmp .= " AND (o.order_type_id = " . $ot;
            } else {
                $sqltmp .= " OR o.order_type_id = " . $ot;
            }
        }
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";

        if (!empty($p['order-range-num'])) {
            $operator = '';
            switch ($p['order-range-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND o.invoice_id" . $operator . (int)($p['order-range-num']);
        }

        if (!empty($p['order-price-num'])) {
            $operator = '';
            switch ($p['order-price-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND o.grand_total" . $operator . expUtil::currency_to_float($p['order-price-num']);
        }

        if (!empty($p['pnam'])) {
            $sqlwhere .= " AND p.title LIKE '%" . expString::escape($p['pnam']) . "%'";
        }

        if (!empty($p['sku'])) {
            $sqlwhere .= " AND p.model LIKE '%" . expString::escape($p['sku']) . "%'";
        }

        $inc = 0;
        $sqltmp = '';
        if (!empty($p['discounts'])) foreach ($p['discounts'] as $d) {
            $d = expString::escape($d);
            if ($d == -1) continue;
            else if ($inc == 0) {
                $inc++;
                $sqltmp .= " AND (od.discounts_id = " . $d;
            } else {
                $sqltmp .= " OR od.discounts_id = " . $d;
            }
        }
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";

        if (!empty($p['blshpname'])) {
            $sqlwhere .= " AND (b.firstname LIKE '%" . expString::escape($p['blshpname']) . "%'";
            $sqlwhere .= " OR s.firstname LIKE '%" . expString::escape($p['blshpname']) . "%'";
            $sqlwhere .= " OR b.lastname LIKE '%" . expString::escape($p['blshpname']) . "%'";
            $sqlwhere .= " OR s.lastname LIKE '%" . expString::escape($p['blshpname']) . "%')";
        }

        if (!empty($p['email'])) {
            $sqlwhere .= " AND (b.email LIKE '%" . expString::escape($p['email']) . "%'";
            $sqlwhere .= " OR s.email LIKE '%" . expString::escape($p['email']) . "%')";
        }

        if (!empty($p['zip'])) {
            if ($p['bl-sp-zip'] === 'b') $sqlwhere .= " AND b.zip LIKE '%" . expString::escape($p['zip']) . "%'";
            else if ($p['bl-sp-zip'] === 's') $sqlwhere .= " AND s.zip LIKE '%" . expString::escape($p['zip']) . "%'";
        }

        if (isset($p['state'])) {
            $inc = 0;
            $sqltmp = '';
            foreach ($p['state'] as $s) {
                $s = expString::escape($s);
                if ($s == -1) continue;
                else if ($inc == 0) {
                    $inc++;
                    if ($p['bl-sp-state'] === 'b') $sqltmp .= " AND (b.state = " . $s;
                    else if ($p['bl-sp-state'] === 's') $sqltmp .= " AND (s.state = " . $s;
                } else {
                    if ($p['bl-sp-state'] === 'b') $sqltmp .= " OR b.state = " . $s;
                    else if ($p['bl-sp-state'] === 's') $sqltmp .= " OR s.state = " . $s;
                }
            }
            if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";
        }

        if (isset($p['payment_method'])) {
            $inc = 0;
            $sqltmp = '';
            foreach ($p['payment_method'] as $s) {
                $s = expString::escape($s);
                if ($s == -1) continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (o.order_status_id = " . $s;
                } else {
                    $sqltmp .= " OR o.order_status_id = " . $s;
                }
            }
            if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";
        }

        //echo $sql . $sqlwhere . "<br>";
        /*
        Need: order, orderitems, order status, ordertype, billingmethods, geo region, shipping methods, products
            [date-startdate] =>
            [time-h-startdate] =>
            [time-m-startdate] =>
            [ampm-startdate] => am
            [date-enddate] =>
            [time-h-enddate] =>
            [time-m-enddate] =>
            [ampm-enddate] => am
            [order_status] => Array
                (
                    [0] => 0
                    [1] => 1
                    [2] => 2
                )

            [order_type] => Array
                (
                    [0] => 0
                    [1] => 2
                )

            [order-range-op] => e
            [order-range-num] =>
            [order-price-op] => l
            [order-price-num] =>
            [pnam] =>
            [sku] =>
            [discounts] => Array
                (
                    [0] => -1
                )

            [blshpname] =>
            [email] =>
            [bl-sp-zip] => s
            [zip] =>
            [bl-sp-state] => s
            [state] => Array
                (
                    [0] => -1
                )

            [status] => Array
                (
                    [0] => -1
                )

        )
        */
        expSession::set('order_print_query', $sql . $sqlwhere);
        //$where = 1;//$this->aggregateWhereClause();
        //$order = 'id';
        //$prod = new product();
        // $order = new order();
        //$items = $prod->find('all', 1, 'id DESC',25);
        //$items = $order->find('all', 1, 'id DESC',25);
        //$res = $mod->find('all',$sql,'id',25);

        //eDebug($items);

        $page = new expPaginator(array(
            //'model'=>'order',
            //'records'=>$items,
            // 'where'=>$where,
            'sql'             => $sql . $sqlwhere,
            'limit'           => empty($this->config['limit']) ? 25 : $this->config['limit'],
            'order'           => (isset($this->params['order']) ? $this->params['order'] : 'invoice_id'),
            'dir'             => (isset($this->params['dir']) ? $this->params['dir'] : 'DESC'),
            'page'            => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'      => $this->baseclassname,
            'action'          => $this->params['action'],
            'columns'         => array(
                'actupon'     => true,
                gt('Order #') => 'invoice_id|controller=order,action=show,showby=id',
                gt('Date')    => 'purchased_date',
                gt('First')   => 'bfirst',
                gt('Last')    => 'blast',
                gt('Total')   => 'grand_total',
                gt('Status')  => 'status_title'
            ),
        ));

        $action_items = array(
            'print_orders' => 'Print Orders',
            'export_odbc'  => 'Export Shipping Data to CSV'
        );
        assign_to_template(array(
            'quickrange'         => $this->quickrange,
            'quickrange_default' => $this->params['quickrange'],
            'page'               => $page,
            'action_items'       => $action_items
        ));
    }

    /**
     * Get parameters for an orders report
     */
    function order_report() {
        // stub function. I'm sure eventually we can pull up existing reports to pre-populate our form.
        $os = new order_status();
        $oss = $os->find('all');
        $order_status = array();
        $order_status[-1] = gt('--Any--');
        foreach ($oss as $status) {
            $order_status[$status->id] = $status->title;
        }

        $ot = new order_type();
        $ots = $ot->find('all');
        $order_type = array();
        $order_type[-1] = gt('--Any--');
        foreach ($ots as $orderType) {
            $order_type[$orderType->id] = $orderType->title;
        }

        $dis = new discounts();
        $diss = $dis->find('all');
        $discounts = array();
        $discounts[-1] = gt('--Any--');
        foreach ($diss as $discount) {
            $discounts[$discount->id] = $discount->coupon_code;
        }

        /*$geo = new geoRegion();
        $geos = $geo->find('all');
        $states = array();
        $states[-1] = gt('--Any--');
        foreach ($geos as $skey=>$state)
        {
            $states[$skey] = $state->name;
        } */

        $payment_methods = billingmethod::$payment_types;
        $payment_methods[-1] = gt('--Any--');
        ksort($payment_methods);
        //array('-1'=>'', 'V'=>'Visa','MC'=>'Mastercard','D'=>'Discover','AMEX'=>'American Express','PP'=>'PayPal','GC'=>'Google Checkout','Other'=>'Other');

        //eDebug(mktime(0,0,0,(strftime("%m")-1),1,strftime("%Y")));
//        $prev_date = strftime("%A, %d %B %Y", mktime(0,0,0,(strftime("%m")-1),1,strftime("%Y")));
        //eDebug(strftime("%A, %d %B %Y", mktime(0,0,0,(strftime("%m")-1),1,strftime("%Y"))));
//        $now_date = strftime("%A, %d %B %Y");
        $prev_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT), mktime(0, 0, 0, date('m'), 1, date('Y')));
        $now_date = date(strftime_to_date_format(DISPLAY_DATE_FORMAT));
        $now_hour = date('h');
        $now_min = date('i');
        $now_ampm = date('A');

        assign_to_template(array(
            'prev_date'       => $prev_date,
            'now_date'        => $now_date,
            'now_hour'        => $now_hour,
            'now_min'         => $now_min,
            'now_ampm'        => $now_ampm,
            'order_status'    => $order_status,
            'discounts'       => $discounts,
//            'states'=>$states,
            'order_type'      => $order_type,
            'payment_methods' => $payment_methods
        ));
    }

    /**
     * Generate an orders report selection view from parameters
     */
    function generateOrderReport() {
        global $db;

        $p = $this->params;
        expSession::set('order_print_params', $p);

        //build
        $start_sql = "SELECT DISTINCT(o.id), ";
        $count_sql = "SELECT COUNT(DISTINCT(o.id)) AS c, ";
        $sql = "o.invoice_id, o.purchased AS purchased_date, b.firstname AS bfirst, b.lastname AS blast, o.grand_total AS grand_total, os.title AS status_title, ot.title AS order_type";
        if (isset($p['order_status_changed'])){
            if ((count($p['order_status_changed']) == 1 && $p['order_status_changed'][0] != -1) || count($p['order_status_changed']) > 1 || (!empty($p['include_status_date']) && (!empty($p['date-sstartdate']) || !empty($p['date-senddate']))))
                $sql .= ", osc.created_at AS status_changed_date";
        }
        $sql .= " FROM " . $db->tableStmt('orders') . " AS o ";
        $sql .= "INNER JOIN " . $db->tableStmt('orderitems') . " AS oi ON oi.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('order_type') . " AS ot ON ot.id = o.order_type_id ";
        $sql .= "INNER JOIN " . $db->tableStmt('product') . " AS p ON oi.product_id = p.id ";
        //if ($p['order_type'][0] != -1) $sql .= "INNER JOIN " . $db->prefix . "order_type AS ot ON o.order_type_id = ot.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('order_status') . " AS os ON os.id = o.order_status_id ";
        if (isset($p['order_status_changed'])) {
            if ((count($p['order_status_changed']) == 1 && $p['order_status_changed'][0] != -1) || count($p['order_status_changed']) > 1 || (!empty($p['include_status_date']) && (!empty($p['date-sstartdate']) || !empty($p['date-senddate']))))
                $sql .= "INNER JOIN " . $db->tableStmt('order_status_changes') . " AS osc ON osc.orders_id = o.id ";
        }
        $sql .= "INNER JOIN " . $db->tableStmt('billingmethods') . " AS b ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->tableStmt('shippingmethods') . " AS s ON s.id = oi.shippingmethods_id ";
        $sql .= "LEFT JOIN " . $db->tableStmt('geo_region') . " AS gr ON (gr.id = b.state OR gr.id = s.state) ";
        if (isset($p['discounts']) && $p['discounts'][0] != -1)
            $sql .= "LEFT JOIN " . $db->tableStmt('order_discounts') . " AS od ON od.orders_id = o.id ";

        $sqlwhere = "WHERE o.purchased != 0";

        if (!empty($p['include_purchased_date'])) {
            if (!empty($p['pstartdate'])) {
                if (is_numeric($p['pstartdate']))
                    $p['pstartdate'] = '@' . $p['pstartdate'];
                $sqlwhere .= " AND o.purchased >= " . strtotime($p['pstartdate']);
            } elseif (!empty($p['date-pstartdate']))
                $sqlwhere .= " AND o.purchased >= " . strtotime($p['date-pstartdate'] . " " . $p['time-h-pstartdate'] . ":" . $p['time-m-pstartdate'] . " " . $p['ampm-pstartdate']);

            if (!empty($p['penddate'])) {
                if (is_numeric($p['penddate']))
                    $p['penddate'] = '@' . $p['penddate'];
                $sqlwhere .= " AND o.purchased <= " . strtotime($p['penddate']);
            } elseif (!empty($p['date-penddate']))
                $sqlwhere .= " AND o.purchased <= " . strtotime($p['date-penddate'] . " " . $p['time-h-penddate'] . ":" . $p['time-m-penddate'] . " " . $p['ampm-penddate']);
        }

        if (!empty($p['include_status_date'])) {
            if (!empty($p['sstartdate'])) {
                if (is_numeric($p['sstartdate']))
                    $p['sstartdate'] = '@' . $p['sstartdate'];
                $sqlwhere .= " AND osc.created_at >= " . strtotime($p['sstartdate']);
            } elseif (!empty($p['date-sstartdate']))
                $sqlwhere .= " AND osc.created_at >= " . strtotime($p['date-sstartdate'] . " " . $p['time-h-sstartdate'] . ":" . $p['time-m-sstartdate'] . " " . $p['ampm-sstartdate']);

            if (!empty($p['senddate'])) {
                if (is_numeric($p['senddate']))
                    $p['senddate'] = '@' . $p['senddate'];
                $sqlwhere .= " AND osc.created_at <= " . strtotime($p['senddate']);
            } elseif (!empty($p['date-senddate']))
                $sqlwhere .= " AND osc.created_at <= " . strtotime($p['date-senddate'] . " " . $p['time-h-senddate'] . ":" . $p['time-m-senddate'] . " " . $p['ampm-senddate']);
        }

        $inc = 0;
        $sqltmp = '';
        if (isset($p['order_status'])) {
            foreach ($p['order_status'] as $os) {
                if ($os == -1)
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (o.order_status_id = " . $os;
                } else {
                    $sqltmp .= " OR o.order_status_id = " . $os;
                }
            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        $inc = 0;
        $sqltmp = '';
        if (isset($p['order_status_changed'])) {
            foreach ($p['order_status_changed'] as $osc) {
                if ($osc == -1)
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    //$sqltmp .= " AND ((osc.to_status_id = " . $osc . " AND (osc.from_status_id != " . $osc . ")";
                    $sqltmp .= " AND (osc.to_status_id = " . $osc;
                } else {
                    //$sqltmp .= " OR (osc.to_status_id = " . $osc . " AND (osc.from_status_id != " . $osc . ")";
                    $sqltmp .= " OR osc.to_status_id = " . $osc;
                }
            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        $inc = 0;
        $sqltmp = '';
        if (isset($p['order_type'])) {
            foreach ($p['order_type'] as $ot) {
                if ($ot == -1)
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (o.order_type_id = " . $ot;
                } else {
                    $sqltmp .= " OR o.order_type_id = " . $ot;
                }
            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        if (!empty($p['order-range-num'])) {
            $operator = '';
            switch ($p['order-range-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND o.invoice_id" . $operator . $p['order-range-num'];
        }

        if (!empty($p['order-price-num'])) {
            $operator = '';
            switch ($p['order-price-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND o.grand_total" . $operator . expUtil::currency_to_float($p['order-price-num']);
        }

        if (!empty($p['pnam'])) {
            $sqlwhere .= " AND p.title LIKE '%" . $p['pnam'] . "%'";
        }

        if (!empty($p['sku'])) {
            $sqlwhere .= " AND p.model LIKE '%" . $p['sku'] . "%'";
        }

        $inc = 0;
        $sqltmp = '';
        if (isset($p['product_status'])) {
            foreach ($p['product_status'] as $pstat) {
                if ($pstat == -1 || empty($pstat))
                    continue;

                $product_status = new product_status($pstat);
                if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (oi.products_status = '" . $product_status->title . "'";
                } else {
                    $sqltmp .= " OR oi.products_status = '" . $product_status->title . "'";
                }
            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        if (!empty($p['uidata'])) {
            $sqlwhere .= " AND oi.user_input_fields != '' AND oi.user_input_fields != 'a:0:{}'";
        }

        $inc = 0;
        $sqltmp = '';
        if (isset($p['discounts'])) {
            foreach ($p['discounts'] as $d) {
                if ($d == -1)
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (od.discounts_id = " . $d;
                } else {
                    $sqltmp .= " OR od.discounts_id = " . $d;
                }
            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        if (!empty($p['blshpname'])) {
            $sqlwhere .= " AND (b.firstname LIKE '%" . $p['blshpname'] . "%'";
            $sqlwhere .= " OR s.firstname LIKE '%" . $p['blshpname'] . "%'";
            $sqlwhere .= " OR b.lastname LIKE '%" . $p['blshpname'] . "%'";
            $sqlwhere .= " OR s.lastname LIKE '%" . $p['blshpname'] . "%')";
        }

        if (!empty($p['email'])) {
            $sqlwhere .= " AND (b.email LIKE '%" . $p['email'] . "%'";
            $sqlwhere .= " OR s.email LIKE '%" . $p['email'] . "%')";
        }

        if (!empty($p['zip'])) {
            if ($p['bl-sp-zip'] === 'b')
                $sqlwhere .= " AND b.zip LIKE '%" . $p['zip'] . "%'";
            else if ($p['bl-sp-zip'] === 's')
                $sqlwhere .= " AND s.zip LIKE '%" . $p['zip'] . "%'";
        }

        if (isset($p['state'])) {
            $inc = 0;
            $sqltmp = '';
            foreach ($p['state'] as $s) {
                if ($s == -1)
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    if ($p['bl-sp-state'] === 'b')
                        $sqltmp .= " AND (b.state = " . $s;
                    else if ($p['bl-sp-state'] === 's')
                        $sqltmp .= " AND (s.state = " . $s;
                } else {
                    if ($p['bl-sp-state'] === 'b')
                        $sqltmp .= " OR b.state = " . $s;
                    else if ($p['bl-sp-state'] === 's')
                        $sqltmp .= " OR s.state = " . $s;
                }
            }
            if (!empty($sqltmp))
                $sqlwhere .= $sqltmp .= ")";
        }

        if (isset($p['payment_method'])) {
            $inc = 0;
            $sqltmp = '';
            //get each calculator's id

            foreach ($p['payment_method'] as $s) {
                if ($s == -1)
                    continue;
                if ($s === 'VisaCard' || $s === 'AmExCard' || $s === 'MasterCard' || $s === 'DiscoverCard') {
                    $paymentQuery = 'b.billing_options LIKE "%' . $s . '%"';
                } else {
                    $bc = new billingcalculator();
                    $calc = $bc->findBy('calculator_name', $s);
                    $paymentQuery = 'billingcalculator_id = ' . $calc->id;
                }

                if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND ( " . $paymentQuery;
                } else {
                    $sqltmp .= " OR " . $paymentQuery;
                }
            }
            if (!empty($sqltmp))
                $sqlwhere .= $sqltmp .= ")";
        }

        //echo $sql . $sqlwhere . "<br>";
        /*
        Need: order, orderitems, order status, ordertype, billingmethods, geo region, shipping methods, products
            [date-startdate] =>
            [time-h-startdate] =>
            [time-m-startdate] =>
            [ampm-startdate] => am
            [date-enddate] =>
            [time-h-enddate] =>
            [time-m-enddate] =>
            [ampm-enddate] => am
            [order_status] => Array
                (
                    [0] => 0
                    [1] => 1
                    [2] => 2
                )

            [order_type] => Array
                (
                    [0] => 0
                    [1] => 2
                )

            [order-range-op] => e
            [order-range-num] =>
            [order-price-op] => l
            [order-price-num] =>
            [pnam] =>
            [sku] =>
            [discounts] => Array
                (
                    [0] => -1
                )

            [blshpname] =>
            [email] =>
            [bl-sp-zip] => s
            [zip] =>
            [bl-sp-state] => s
            [state] => Array
                (
                    [0] => -1
                )

            [status] => Array
                (
                    [0] => -1
                )

        )
        */

        //$sqlwhere .= " ORDER BY purchased_date DESC";
        $count_sql .= $sql . $sqlwhere;
        $sql = $start_sql . $sql;
        expSession::set('order_print_query', $sql . $sqlwhere);
        $reportRecords = $db->selectObjectsBySql($sql . $sqlwhere);
        expSession::set('order_export_values', $reportRecords);

        //eDebug(expSession::get('order_export_values'));
        //$where = 1;//$this->aggregateWhereClause();
        //$order = 'id';
        //$prod = new product();
        // $order = new order();
        //$items = $prod->find('all', 1, 'id DESC',25);
        //$items = $order->find('all', 1, 'id DESC',25);
        //$res = $mod->find('all',$sql,'id',25);
        //eDebug($items);
        //eDebug($sql . $sqlwhere);

        $page = new expPaginator(array(
            //'model'=>'order',
            //'records'=>$items,
            // 'where'=>$where,
            'count_sql'  => $count_sql,
            'sql'        => $sql . $sqlwhere,
            'limit'      => empty($this->config['limit']) ? 350 : $this->config['limit'],
            'order'      => (isset($this->params['order']) ? $this->params['order'] : 'invoice_id'),
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'DESC'),  // newest first
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->baseclassname,
            'action'     => $this->params['action'],
            'columns'    => array(
                'actupon'                    => true,
                gt('Order #')             => 'invoice_id|controller=order,action=show,showby=id',
                gt('Purchased Date')      => 'purchased_date|FORMAT=date',
                gt('First')               => 'bfirst',
                gt('Last')                => 'blast',
                gt('Total')               => 'grand_total|FORMAT=currency',
                gt('Status Changed Date') => 'status_changed_date|FORMAT=date',
                gt('Order Type')          => 'order_type',
                gt('Status')              => 'status_title'
            ),
        ));

        //strftime("%a %d-%m-%Y", get_first_day(3, 1, 2007)); Thursday, 1 April 2010
        //$d_month_previous = date('n', mktime(0,0,0,(strftime("%m")-1),1,strftime("%Y")));

        $action_items = array(
            'print_orders'             => 'Print Orders',
            'export_odbc'              => 'Export Shipping Data to CSV',
            'export_status_report'     => 'Export Order Status Data to CSV',
            'export_inventory'         => 'Export Inventory Data to CSV',
            'export_user_input_report' => 'Export User Input Data to CSV',
            'export_order_items'       => 'Export Order Items Data to CSV',
            'show_payment_summary'     => 'Show Payment & Tax Summary'
        );
        assign_to_template(array(
            'page'         => $page,
            'action_items' => $action_items
        ));
    }

    /**
     * An Order Report to show breakdown of taxes and payment types
     */
    function show_payment_summary() {
        global $db;

        set_time_limit(0);
        $payments = billingmethod::$payment_types;

        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            if (!empty($this->params['act-upon'])) foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);

        $payment_total = 0;
        $payment_summary = array();
        $payments_key_arr = array();
        $payment_values_arr = array();
        // $Credit Cards
//        $sql = "SELECT orders_id, billing_cost, billing_options, calculator_name, user_title FROM " . $db->prefix . "billingmethods, " . $db->prefix . "billingcalculator WHERE " . $db->prefix . "billingcalculator.id = billingcalculator_id and orders_id IN (" . $orders_string . ")";
        $sql_end = "FROM " . $db->tableStmt('billingmethods') . ", " . $db->tableStmt('billingcalculator') . " WHERE " . $db->tableStmt('billingcalculator') . ".id = billingcalculator_id and orders_id IN (" . $orders_string . ")";
        $total = $db->countObjectsBySql('SELECT COUNT(*) AS c ' . $sql_end);
        $sql = "SELECT orders_id, billing_cost, billing_options, calculator_name, title " . $sql_end;
        for ($i = 0; $i < $total; $i += 100) {
            $res = $db->selectObjectsBySql($sql . $db->limitStmt(100, $i));
            if (!empty($res)) {
                foreach ($res as $item) {
                    $options = expUnserialize($item->billing_options);
                    if (!empty($item->billing_cost)) {
//                    if ($item->user_title == 'Credit Card') {
                        if ($item->title === 'Credit Card') {  //FIXME there is no billingmethod->title ...this is translated??
                            if (!empty($options->cc_type)) {
                                @$payment_summary[$payments[$options->cc_type]] += $item->billing_cost;
//                                @$payment_summary[$payments[$options->cc_type]] += $options->result->amount_captured;
                            } else {
                                @$payment_summary[$item->title] += $item->billing_cost;
//                                @$payment_summary[$item->title] += $options->result->amount_captured;
                            }
                        } else {
                            if (empty($payments[$item->calculator_name])) {
                                $type = $item->title;
                            } else {
                                $type = $payments[$item->calculator_name];
                            }
                            @$payment_summary[$type] += $item->billing_cost;
                        }
                        $payment_total += $item->billing_cost;
                    }
                }
            }

        }

        foreach ($payment_summary as $key => $item) {
            $payments_key_arr[] = '"' . $key . '"';
            $payment_values_arr[] = round($item, 2);
        }
        $payments_key = implode(",", $payments_key_arr);
        $payment_values = implode(",", $payment_values_arr);

        //tax
//        $tax_sql = "SELECT SUM(tax) AS tax_total FROM " . $db->prefix . "orders WHERE id IN (" . $orders_string . ")";
//        $tax_res = $db->selectObjectBySql($tax_sql);
//        $tax_types = taxController::getTaxRates();
//        $tax_type_formatted = $tax_types[0]->zonename . ' - ' . $tax_types[0]->classname . ' - ' . $tax_types[0]->rate . '%';

        $ord = new order();
        $except = array('order_discounts', 'billingmethod', 'order_status_changes', 'order_status', 'order_type', 'shippingmethod', 'user');
        $taxes = array();
        $total = $ord->find('count',"id IN (" . $orders_string . ")", null, null, 0, true, true, $except);
        for ($i = 0; $i < $total; $i += 100) {
            $tax_res2 = $ord->find('all', "id IN (" . $orders_string . ")", null, 100, $i, true, true, $except);
            foreach ($tax_res2 as $tt) {
                $key = key($tt->taxzones);
                if (!empty($key)) {
                    $tname = $tt->taxzones[$key]->name;
                    if (!isset($taxes[$key]['format'])) {
                        $taxes[$key] = array();
                        $taxes[$key]['format'] = $tname . ' - ' . $tt->taxzones[$key]->rate . '%';
                        $taxes[$key]['total'] = 0;
                    }
                    $taxes[$key]['total'] += $tt->tax;
                }
            }
        }

        $p = expSession::get('order_print_params');
        $date = '';
        if (!empty($p['include_purchased_date'])) {
            $date = gt('Purchased between') . ' ' . $p['pstartdate'] . ' ' . gt('and') . ' ' . $p['penddate'];
        }
        assign_to_template(array(
            'total'           => $total,
            'date'            => $date,
            'payment_total'   => $payment_total,
            'payment_summary' => $payment_summary,
            'payments_key'    => $payments_key,
            'payment_values'  => $payment_values,
//            'tax_total'       => !empty($tax_res->tax_total) ? $tax_res->tax_total : 0,
//            'tax_type'        => $tax_type_formatted,
            'taxes'           => $taxes
        ));
    }

    /**
     * An Order Report
     */
    function export_user_input_report() {
        $order = new order();
        $out = '"ITEM_NAME","QUANTITY","PERSONALIZATION"' . chr(13) . chr(10);
        //eDebug($this->params,true);
        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);
        $orders = $order->find('all', 'id IN (' . $orders_string . ')');
        //eDebug($orders,true);
        $pattern = '/\(.*\)/i';
        $items = array();
        $top = array();
        foreach ($orders as $order) { //eDebug($order,true);
            foreach ($order->orderitem as $oi) {
                // eDebug($oi,true);
                $item = array();
                if ($oi->user_input_fields == '' || $oi->user_input_fields === 'a:0:{}') continue;
                else $item['user_input_data'] = expUnserialize($oi->user_input_fields);;

                $model = preg_replace($pattern, '', preg_replace('/\s/', '', $oi->products_model));
                $item['model'] = $model;
                //$item['name'] = strip_tags($oi->products_name);
                $item['qty'] = $oi->quantity;

                $items[] = $item;
            }
        }
        unset($item);
        foreach ($items as $item) {
            $line = '';
            //$line = expString::outputField("SMC Inventory - Laurie");
            $line .= expString::outputField($item['model']);
            //$line.= expString::outputField($item['name']);
            $line .= expString::outputField($item['qty']);
            $ui = array();
            $uiInfo = '';
            foreach ($item['user_input_data'] as $tlArray) {
                foreach ($tlArray as $ifKey => $if) {
                    $uiInfo .= $ifKey . '=' . $if . " | ";
                }
            }
            $line .= expString::outputField(strtoupper(substr_replace($uiInfo, '', strrpos($uiInfo, ' |'), strlen(' |'))), chr(13) . chr(10));
            $out .= $line;
        }
        //eDebug($out,true);
        self::download($out, 'User_Input_Export_' . time() . '.csv', 'application/csv');
        // [firstname] => Fred [middlename] => J [lastname] => Dirkse [organization] => OIC Group, Inc. [address1] => PO Box 1111 [address2] => [city] => Peoria [state] => 23 [zip] => 61653 [country] => [phone] => 309-555-1212 begin_of_the_skype_highlighting              309-555-1212      end_of_the_skype_highlighting  [email] => fred@oicgroup.net [shippingcalculator_id] => 4 [option] => 01 [option_title] => 8-10 Day [shipping_cost] => 5.95
    }

    /**
     * An Order Report
     */
    function export_inventory() {
        $order = new order();
        $out = '"BADDR_LAST_NM","ITEM_NAME","ITEM_DESC","ITEM_QUANTITY"' . chr(13) . chr(10);
        //eDebug($this->params,true);
        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);
        $orders = $order->find('all', 'id IN (' . $orders_string . ')');
        //eDebug($orders,true);
        $pattern = '/\(.*\)/i';
        $items = array();
        $top = array();
        foreach ($orders as $order) { //eDebug($order,true);
            foreach ($order->orderitem as $oi) {
                $model = preg_replace($pattern, '', preg_replace('/\s/', '', $oi->products_model));
                if (stripos($model, 'DUI') === 0) {
                    $top[$model]['name'] = strip_tags($oi->products_name);
                    if (isset($top[$model]['qty'])) $top[$model]['qty'] += $oi->quantity;
                    else $top[$model]['qty'] = $oi->quantity;
                } else {
                    $items[$model]['name'] = strip_tags($oi->products_name);
                    if (isset($items[$model]['qty'])) $items[$model]['qty'] += $oi->quantity;
                    else $items[$model]['qty'] = $oi->quantity;
                }
            }
        }
        ksort($top, SORT_STRING);
        ksort($items, SORT_STRING);
        foreach ($top as $model => $item) {
//            $line = '';
            $line = expString::outputField("SMC Inventory - Laurie");
            $line .= expString::outputField($model);
            $line .= expString::outputField($item['name']);
            $line .= expString::outputField($item['qty'], chr(13) . chr(10));
            $out .= $line;
        }
        foreach ($items as $model => $item) {
//            $line = '';
            $line = expString::outputField("SMC Inventory - Laurie");
            $line .= expString::outputField($model);
            $line .= expString::outputField($item['name']);
            $line .= expString::outputField($item['qty'], chr(13) . chr(10));
            $out .= $line;
        }
        //eDebug($out,true);
        self::download($out, 'Inventory_Export_' . time() . '.csv', 'application/csv');
        // [firstname] => Fred [middlename] => J [lastname] => Dirkse [organization] => OIC Group, Inc. [address1] => PO Box 1111 [address2] => [city] => Peoria [state] => 23 [zip] => 61653 [country] => [phone] => 309-555-1212 begin_of_the_skype_highlighting              309-555-1212      end_of_the_skype_highlighting  [email] => fred@oicgroup.net [shippingcalculator_id] => 4 [option] => 01 [option_title] => 8-10 Day [shipping_cost] => 5.95

    }

    /**
     * Generate an products report selection view from parameters
     */
    function generateProductReport() {
        global $db;

        $p = $this->params;
        expSession::set('product_print_params', $p);

        $sqlids = "SELECT DISTINCT(p.id) FROM ";
        $count_sql = "SELECT COUNT(DISTINCT(p.id)) AS c FROM ";
        $sqlstart = "SELECT DISTINCT(p.id), p.title, p.model, base_price"; //, ps.title AS status FROM ";
        $sql =  $db->tableStmt('product') . " AS p ";
        if (!isset($p['allproducts'])){
            $sql .= "INNER JOIN " . $db->tableStmt('product_status') . " AS ps ON p.product_status_id = ps.id ";
            $sqlstart .= ", ps.title AS status FROM ";
            if (!isset($p['uncategorized'])){
                $sql .= "INNER JOIN " . $db->tableStmt('product_storeCategories') . " AS psc ON p.id = psc.product_id ";
            }
        } else {
            $sqlstart .= " FROM ";
        }
        //$sqlidsjoin = "INNER JOIN " . $db->prefix . "product AS childp ON p.id = childp.parent_id ";
        $sqlwhere = 'WHERE (1=1 ';

        $inc = 0;
        $sqltmp = '';
        if (isset($p['product_status'])) {
            foreach ($p['product_status'] as $os) {
                if ($os == '')
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (p.product_status_id = " . $os;
                } else {
                    $sqltmp .= " OR p.product_status_id = " . $os;
                }

            }
            if (!empty($sqltmp))
                $sqlwhere .= $sqltmp .= ")";
        }

        $inc = 0;
        $sqltmp = '';
        if (!empty($p['product_type'])) {
            foreach ($p['product_type'] as $ot) {
                if ($ot == '')
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (p.product_type = '" . $ot . "'";
                } else {
                    $sqltmp .= " OR p.product_type = '" . $ot . "'";
                }

            }
        }
        if (!empty($sqltmp))
            $sqlwhere .= $sqltmp .= ")";

        if (!isset($p['allproducts'])) {
            if (!isset($p['uncategorized'])) {
                $inc = 0;
                $sqltmp = '';
                if (!empty($p['storeCategory'])) foreach ($p['storeCategory'] as $ot) {
                    if ($ot == '')
                        continue;
                    else if ($inc == 0) {
                        $inc++;
                        $sqltmp .= " AND (psc.storecategories_id = " . $ot;
                    } else {
                        $sqltmp .= " OR psc.storecategories_id = " . $ot;
                    }
                }
                if (!empty($sqltmp))
                    $sqlwhere .= $sqltmp .= ")";
            } else {
                $sqlwhere .= " AND psc.storecategories_id = 0 AND p.parent_id = 0";
            }
        }

        if (!empty($p['product-range-num'])) {
            $operator = '';
            switch ($p['product-range-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND p.id" . $operator . $p['product-range-num'];
        }

        $inc = 0;
        $sqltmp = '';
        if (isset($p['company'])) {
            foreach ($p['company'] as $os) {
                if ($os == '')
                    continue;
                else if ($inc == 0) {
                    $inc++;
                    $sqltmp .= " AND (p.companies_id = " . $os;
                } else {
                    $sqltmp .= " OR p.companies_id = " . $os;
                }

            }
            if (!empty($sqltmp))
                $sqlwhere .= $sqltmp .= ")";
        }

        if (!empty($p['product-price-num'])) {
            $operator = '';
            switch ($p['product-price-op']) {
                case 'g':
                    $operator = '>';
                    break;
                case 'l':
                    $operator = '<';
                    break;
                case 'e':
                    $operator = '=';
                    break;
            }
            $sqlwhere .= " AND p.base_price" . $operator . expUtil::currency_to_float($p['product-price-num']);
        }

        if (!empty($p['pnam'])) {
            $sqlwhere .= " AND p.title LIKE '%" . $p['pnam'] . "%'";
        }

        if (!empty($p['sku'])) {
            $sqlwhere .= " AND p.model LIKE '%" . $p['sku'] . "%'";
        }

        $sqlwhere .= ")";

        $exportSQL = $sqlids . $sql . $sqlwhere; // . ")";     // " OR p.parent_id IN (".$sqlids . $sql . $sqlwhere . ")";
        //$sqlidswhere = " OR p.id IN (SELECT id FROM".$db->prefix."_product WHERE parent_id=)";
//        eDebug($sqlstart . $sql . $sqlwhere);
//        eDebug($count_sql . $sql . $sqlwhere);
//        eDebug("Stored:" . $exportSQL);
        expSession::set('product_export_query', $exportSQL);
        //expSession::set('product_export_query', "SELECT  DISTINCT(p.id) FROM `" . $db->prefix . "product` p WHERE (title like '%Velcro%' OR feed_title like '%Velcro%' OR title like '%Multicam%' OR feed_title like '%Multicam%') AND parent_id = 0");

//        $product = new product();
        //$items = $product->find('all', '', 'id', 25);
        //$page = new expPaginator();
        //eDebug($page,true);
        $page = new expPaginator(array(
//            'model'      => 'product',
            //'records'=>$items,
            // 'where'=>$where,
            'sql'        => $sqlstart . $sql . $sqlwhere,
            //'sql'=>"SELECT  DISTINCT(p.id), p.title, p.model, p.base_price FROM `" . $db->prefix . "product` p WHERE (title like '%Velcro%' OR feed_title like '%Velcro%' OR title like '%Multicam%' OR feed_title like '%Multicam%') AND parent_id = 0",
            //'count_sql'=>"SELECT COUNT(DISTINCT(p.id)) FROM `" . $db->prefix . "product` p WHERE (title like '%Velcro%' OR feed_title like '%Velcro%' OR title like '%Multicam%' OR feed_title like '%Multicam%') AND parent_id = 0",
            'count_sql'  => $count_sql . $sql . $sqlwhere,
            'limit'      => empty($this->config['limit']) ? 350 : $this->config['limit'],
            'order'      => (isset($this->params['order']) ? $this->params['order'] : 'id'),
            'dir'        => (isset($this->params['dir']) ? $this->params['dir'] : 'ASC'),
            'page'       => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->baseclassname,
            'action'     => $this->params['action'],
            'columns'    => array(
                'actupon'     => true,
                'ID'      => 'id',
                gt('Product') => 'title|controller=store,action=show,showby=id',
                'SKU'     => 'model',
                gt('Price')   => 'base_price|FORMAT=currency',
                gt('Status')   => 'status'
            ),
            //'columns'=>array('Product'=>'title','SKU'=>'model'),
        ));
        //eDebug($page,true);
        /*$page = new expPaginator(array(
            'model'=>'order',
            'controller'=>$this->params['controller'],
            'action'=>$this->params['action'],
            'sql'=>$sql,
            'order'=>'purchased',
            'dir'=>'DESC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
            'columns'=>array(
                'Customer'=>'lastname',
                'Invoice #'=>'invoice_id',
                'Total'=>'total',
                'Date Purchased'=>'purchased',
                'Status'=>'order_status_id',
            )
        ));            */
        $action_items = array(
            'batch_export' => 'Export Product List to CSV',
            'status_export' => 'Export Product Status Report to CSV'
        );
        assign_to_template(array(
            'page'         => $page,
            'action_items' => $action_items
        ));
        //
        //
        // assign_to_template(array('page'=>$page));
    }

    /**
     * An Order Report
     */
    function print_orders() {
//        global $db, $timer;

        //eDebug($this->params,true);
        //eDebug($timer->mark());
        //eDebug( expSession::get('order_print_query'));
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            //$sql = expSession::get('order_print_query');
            //eDebug($sql);
            //expSession::set('product_export_query','');
            //$orders = $db->selectArraysBySql($sql);
            $obs = expSession::get('order_export_values');
            usort($obs, array("reportController", "sortPrintOrders"));
            foreach ($obs as $ob) {
                $orders[] = array('id' => $ob->id);
            }
            //eDebug($prods);
        } else {
            foreach ($this->params['act-upon'] as $order) {
                $orders[] = array('id' => $order);
            }
        }

        //eDebug("Done with print_orders: " . $timer->mark());
        //eDebug($orders,true);
        $oc = new orderController();
        $oc->getPDF($orders);
    }

    //sort print orders by id, newest to oldest
    static function sortPrintOrders($a, $b) {
        if ($a->invoice_id > $b->invoice_id) return -1;
        else if ($a->invoice_id < $b->invoice_id) return 1;
        else if ($a->invoice_id == $b->invoice_id) return 0;
    }

    /**
     * An Order Report
     */
    function export_odbc() {
        $order = new order();
        $out = '"order_id","shipping_method_id","shipping_option","shipping_cost","firstname","middlename","lastname","organization","address1","address2","city","state","zip","country","phone"' . chr(13) . chr(10);
        //eDebug($this->params,true);
        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);
        $orders = $order->find('all', 'id IN (' . $orders_string . ')');
        //eDebug($orders);
        foreach ($orders as $order) {
            $line = expString::outputField($order->invoice_id);
            foreach ($order->shippingmethods as $m) {
                $line .= expString::outputField($m->id);
                $line .= expString::outputField($m->option_title);
                $line .= expString::outputField($order->shipping_total + $order->surcharge_total);
                $line .= expString::outputField($m->firstname);
                $line .= expString::outputField($m->middlename);
                $line .= expString::outputField($m->lastname);
                $line .= expString::outputField($m->organization);
                $line .= expString::outputField($m->address1);
                $line .= expString::outputField($m->address2);
                $line .= expString::outputField($m->city);
//                $state = new geoRegion($m->state);
                //eDebug($state);
//                $line .= expString::outputField($state->code);
                $line .= expString::outputField(geoRegion::getAbbrev($m->state));
                $line .= expString::outputField($m->zip);
//                $line .= expString::outputField('US');
                $line .= expString::outputField(geoRegion::getCountryCode($m->country));
                $line .= expString::outputField($m->phone, chr(13) . chr(10));
                break;  // fixme this breaks foreach loop on 1st run
            }
            $out .= $line;
        }
        //eDebug($out,true);
        self::download($out, 'Shipping_Export.csv', 'application/csv');
        // [firstname] => Fred [middlename] => J [lastname] => Dirkse [organization] => OIC Group, Inc. [address1] => PO Box 1111 [address2] => [city] => Peoria [state] => 23 [zip] => 61653 [country] => [phone] => 309-555-1212 begin_of_the_skype_highlighting              309-555-1212      end_of_the_skype_highlighting  [email] => fred@oicgroup.net [shippingcalculator_id] => 4 [option] => 01 [option_title] => 8-10 Day [shipping_cost] => 5.95

    }

    /**
     * An Order Report
     */
    function export_order_items() {
        $order = new order();
        $out = '"order_id","quantity","SKU","product_title","firstname","middlename","lastname","organization","address1","address2","city","state","zip"' . chr(13) . chr(10);
        //eDebug($this->params,true);
        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);
        $orders = $order->find('all', 'id IN (' . $orders_string . ')');
        //eDebug($orders);
        foreach ($orders as $order) {
            $m = array_shift($order->shippingmethods);
            foreach ($order->orderitem as $orderitem) {
                $line = expString::outputField($order->invoice_id);
                $line .= expString::outputField($orderitem->quantity);
                $line .= expString::outputField($orderitem->products_model);
                $line .= expString::outputField($orderitem->products_name);

                $line .= expString::outputField($m->firstname);
                $line .= expString::outputField($m->middlename);
                $line .= expString::outputField($m->lastname);
                $line .= expString::outputField($m->organization);
                $line .= expString::outputField($m->address1);
                $line .= expString::outputField($m->address2);
                $line .= expString::outputField($m->city);
                $state = new geoRegion($m->state);
                $line .= expString::outputField($state->code);
                $line .= expString::outputField($m->zip, chr(13) . chr(10));
                $out .= $line;
            }
        }
        //eDebug($out,true);
        self::download($out, 'Order_Item_Export.csv', 'application/csv');
        // [firstname] => Fred [middlename] => J [lastname] => Dirkse [organization] => OIC Group, Inc. [address1] => PO Box 1111 [address2] => [city] => Peoria [state] => 23 [zip] => 61653 [country] => [phone] => 309-555-1212 begin_of_the_skype_highlighting              309-555-1212      end_of_the_skype_highlighting  [email] => fred@oicgroup.net [shippingcalculator_id] => 4 [option] => 01 [option_title] => 8-10 Day [shipping_cost] => 5.95

    }

    /**
     * An Order Report
     */
    function export_status_report() {
        $order = new order();
        $out = '"ITEM_NAME","ITEM_DESC","ITEM_QUANTITY","ITEM_STATUS"' . chr(13) . chr(10);
        //eDebug($this->params,true);
        $order_ids = array();
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $obs = expSession::get('order_export_values');
            foreach ($obs as $ob) {
                $order_ids[] = $ob->id;
            }
        } else {
            foreach ($this->params['act-upon'] as $order_id) {
                $order_ids[] = $order_id;
            }
        }
        $order_ids = array_unique($order_ids);
        $orders_string = implode(',', $order_ids);
        $orders = $order->find('all', 'id IN (' . $orders_string . ')', null, null, null, true, true, array('order_discounts', 'billingmethod', 'order_status_changes', 'order_status', 'order_type'), true);
        $pattern = '/\(.*\)/i';
        foreach ($orders as $order) {
            foreach ($order->orderitem as $oi) {
                $model = preg_replace($pattern, '', preg_replace('/\s/', '', $oi->products_model));
                $line = '';
                $line .= expString::outputField($model);
                $line .= expString::outputField($oi->products_name);
                $line .= expString::outputField($oi->quantity);
                $line .= expString::outputField($oi->products_status, chr(13) . chr(10));
                $out .= $line;
            }
        }
        self::download($out, 'Status_Export_' . time() . '.csv', 'application/csv');
    }

    /**
     * Output report data for download
     *
     * @param $file
     * @param $name
     * @param $type
     */
    static function download($file, $name, $type) {
        if (!headers_sent()) {
            //echo $file;
            //exit();
            ob_clean();
            header('Content-Description: File Transfer');
            header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
            header('Pragma: public');
//            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            // force download dialog
            header('Content-Type: application/force-download');
            //header('Content-Type: application/octet-stream', false);
            header('Content-Type: application/download', false);
            header('Content-Type: ' . $type, false);
            //header('Content-Type: application/pdf', false);
            // use the Content-Disposition header to supply a recommended filename
            header('Content-Disposition: attachment; filename="' . $name . '";');
            header('Content-Transfer-Encoding: ascii');
            header('Content-Length: ' . strlen($file));
            //header('Content-Length: '.filesize($this->tmp_rendered));
            echo $file;
            //echo readfile($this->tmp_rendered);
        } else {
            echo "Oops, headers already sent.  Check DEVELOPMENT variable?";
        }
        die();
    }

    /**
     * FIXME Purpose not known/incomplete
     */
    function productFeed() {
//        global $db;

        //check query password to avoid DDOS
        /*
            * condition  = new
            * description
            * id - SKU
            * link
            * price
            * title
            * brand - manufacturer
            * image link - fullsized image, up to 10, comma seperated
            * product type - category - "Electronics > Audio > Audio Accessories MP3 Player Accessories","Health & Beauty > Healthcare > Biometric Monitors > Pedometers"
         */
        $out = '"id","condition","description","like","price","title","brand","image link","product type"' . chr(13) . chr(10);

        $p = new product();
        $prods = $p->find('all', 'parent_id=0 AND ');
        //$prods =  $db->selectObjects('product','parent_id=0 AND');
    }

    /**
     * Report on abandoned carts
     */
    function abandoned_carts() {
        global $db;

        $allCarts = array();
        $carts = array();
        $cartsWithoutItems = array();
        $cartsWithItems = array();
        $cartsWithItemsAndInfo = array();
        $summary = array();
        $valueproducts = '';

//        if (!isset($this->params['quickrange']) && !isset($this->params['date-starttime']) && !isset($this->params['starttime'])) {
//            $this->params['quickrange'] = $this::LAST_MONTH;
//        }
        $this->setDateParams();

        // purchased == 0 or invoice_id == 0 on unsubmitted orders
        $sql = "SELECT * FROM " . $db->tableStmt('orders') . " WHERE purchased = 0 AND edited_at >= " . $this->tstart . " AND edited_at <= " . $this->tend . " AND sessionticket_ticket NOT IN ";
        $sql .= "(SELECT ticket FROM " . $db->tableStmt('sessionticket') . ") ORDER BY edited_at DESC";
        // echo $sql;
        $allCarts = $db->selectObjectsBySql($sql);
        foreach ($allCarts as $item) {

            $sql = "SELECT * FROM " . $db->tableStmt('orderitems') . " WHERE orders_id =" . $item->id;

            $carts = $db->selectObjectsBySql($sql);
            foreach ($carts as $item2) {
                $valueproducts += $item2->products_price_adjusted * $item2->quantity;
            }

            $carts['last_visit'] = date('Y-m-d, g:i:s A', $item->edited_at);
            $carts['referrer'] = $item->orig_referrer;

            if (count($carts) > 2) {
                if (!empty($item->user_id)) {
                    $u = $db->selectObject('user', 'id=' . $item->user_id);
                    $carts['name'] = $u->firstname . ' ' . $u->lastname;
                    $carts['email'] = $u->email;
                    $cartsWithItemsAndInfo[] = $carts;
                    // $cartsWithItemsAndInfo['length_of_time']  = round(abs($item->last_active - $item->start_time) / 60,2)." minutes";
                    // $cartsWithItemsAndInfo['ip_address']  = $item->ip_address;
                    // $cartsWithItemsAndInfo['referrer']    = $item->referrer;
                } else {
                    $cartsWithItems[] = $carts;
                    // $cartsWithItems['length_of_time']  = round(abs($item->last_active - $item->start_time) / 60,2)." minutes";
                    // $cartsWithItems['ip_address']  = $item->ip_address;
                    // $cartsWithItems['referrer']    = $item->referrer;
                }

            } else {
                $item->last_visit = date('Y-m-d, g:i:s A', $item->edited_at);
                $cartsWithoutItems[] = $item;
            }
        }
        //Added the count
        $allCarts['count'] = count($allCarts);
        $cartsWithoutItems['count'] = count($cartsWithoutItems);
        $cartsWithItems['count'] = count($cartsWithItems); //for the added values at the top
        $cartsWithItemsAndInfo['count'] = count($cartsWithItemsAndInfo); //for the added values at the top

        // eDebug($allCarts);
        // eDebug($cartsWithoutItems);
        // eDebug($cartsWithItems);
        // eDebug($cartsWithItemsAndInfo);
        // exit();
        $summary['totalcarts'] = $allCarts['count'];
        $summary['valueproducts'] = $valueproducts;
        $summary['cartsWithoutItems'] = round(($allCarts['count'] ? $cartsWithoutItems['count'] / $allCarts['count'] : 0) * 100, 2) . '%';
        $summary['cartsWithItems'] = round(($allCarts['count'] ? $cartsWithItems['count'] / $allCarts['count'] : 0) * 100, 2) . '%';
        $summary['cartsWithItemsAndInfo'] = round(($allCarts['count'] ? $cartsWithItemsAndInfo['count'] / $allCarts['count'] : 0) * 100, 2) . '%';

        assign_to_template(array(
            'quickrange'            => $this->quickrange,
            'quickrange_default'    => $this->params['quickrange'],
            'summary'               => $summary,
            'cartsWithoutItems'     => $cartsWithoutItems,
            'cartsWithItems'        => $cartsWithItems,
            'cartsWithItemsAndInfo' => $cartsWithItemsAndInfo
        ));
    }

    /**
     * Report on abandoned carts in row format
     */
    function abandoned_carts_row() {
        global $db;

        $allCarts = array();
        $carts = array();
        $cartsWithItemsAndInfo = array();
        $summary = array();
        $valueproducts = '';

//        if (!isset($this->params['quickrange']) && !isset($this->params['date-starttime']) && !isset($this->params['starttime'])) {
//            $this->params['quickrange'] = $this::LAST_MONTH;
//        }
        $this->setDateParams();

        // purchased == 0 or invoice_id == 0 on unsubmitted orders
        $sql = "SELECT * FROM " . $db->tableStmt('orders') . " WHERE purchased = 0 AND edited_at >= " . $this->tstart . " AND edited_at <= " . $this->tend . " AND sessionticket_ticket NOT IN ";
        $sql .= "(SELECT ticket FROM " . $db->tableStmt('sessionticket') . ") ORDER BY edited_at DESC";
        // echo $sql;
        $allCarts = $db->selectObjectsBySql($sql);
        foreach ($allCarts as $item) {
            $sql = "SELECT * FROM " . $db->tableStmt('orderitems') . " WHERE orders_id =" . $item->id;

            $carts = $db->selectObjectsBySql($sql);
            foreach ($carts as $item2) {
                $valueproducts += $item2->products_price_adjusted * $item2->quantity;
            }

            $carts['last_visit'] = date('Y-m-d, g:i:s A', $item->edited_at);
            $carts['referrer'] = $item->orig_referrer;

            if (count($carts) > 2) {
                if (!empty($item->user_id)) {
                    $u = $db->selectObject('user', 'id=' . $item->user_id);
                    $carts['name'] = $u->firstname . ' ' . $u->lastname;
                    $carts['email'] = $u->email;
                    $cartsWithItemsAndInfo[] = $carts;
                    // $cartsWithItemsAndInfo['length_of_time']  = round(abs($item->last_active - $item->start_time) / 60,2)." minutes";
                    // $cartsWithItemsAndInfo['ip_address']  = $item->ip_address;
                    // $cartsWithItemsAndInfo['referrer']    = $item->referrer;
                }
            }
        }
        //Added the count
        $allCarts['count'] = count($allCarts);
        $cartsWithItemsAndInfo['count'] = count($cartsWithItemsAndInfo); //for the added values at the top

        $summary['totalcarts'] = $allCarts['count'];
        $summary['valueproducts'] = $valueproducts;
        $summary['cartsWithItemsAndInfo'] = round(($allCarts['count'] ? $cartsWithItemsAndInfo['count'] / $allCarts['count'] : 0) * 100, 2) . '%';

        assign_to_template(array(
            'quickrange'            => $this->quickrange,
            'quickrange_default'    => $this->params['quickrange'],
            'summary'               => $summary,
            'cartsWithItemsAndInfo' => $cartsWithItemsAndInfo
        ));
    }

    /**
     * Remove abandoned carts data
     */
    function pruge_abandoned_carts() {
        global $db;

        $db->delete("orders","'invoice_id' = '0' AND 'edited_at' < UNIX_TIMESTAMP(now()) - 2592000 AND 'sessionticket_ticket' NOT IN (SELECT 'ticket' FROM " . $db->tableStmt('sessionticket') . ")");
        $db->delete("orderitems","'orders_id' NOT IN (SELECT 'id' FROM " . $db->tableStmt('orders') . ")");
        $db->delete("shippingmethods","'id' NOT IN (SELECT 'shippingmethods_id' FROM " . $db->tableStmt('orders') . ")");
    }

    /**
     * Display report of currently active carts
     */
    function current_carts() {
        global $db;

        $allCarts = array();
        $carts = array();
        $cartsWithoutItems = array();
        $cartsWithItems = array();
        $cartsWithItemsAndInfo = array();
        $summary = array();
        $valueproducts = 0;
        // $sql = "SELECT * FROM " . $db->prefix . "orders WHERE DATEDIFF(FROM_UNIXTIME(edited_at, '%Y-%m-%d'), '" . date('Y-m-d') . "') = 0";

        $sql = "SELECT * FROM " . $db->tableStmt('orders') . ", " . $db->tableStmt('sessionticket') . " WHERE ticket = sessionticket_ticket";

        $allCarts = $db->selectObjectsBySql($sql);

        // eDebug($allCarts, true);
        foreach ($allCarts as $item) {

            $sql = "SELECT * FROM " . $db->tableStmt('orderitems') . " WHERE orders_id =" . $item->id;

            $carts = $db->selectObjectsBySql($sql);

            foreach ($carts as $item2) {
                $valueproducts += $item2->products_price_adjusted * $item2->quantity;
            }

            $carts['length_of_time'] = round(abs($item->last_active - $item->start_time) / 60, 2) . " minutes";
            $carts['last_active'] = $item->last_active;
            $carts['ip_address'] = $item->ip_address;
            $carts['referrer'] = $item->referrer;

            if (count($carts) > 4) {
                if (!empty($item->user_id)) {
                    $u = $db->selectObject('user', 'id=' . $item->user_id);
                    $carts['name'] = $u->firstname . ' ' . $u->lastname;
                    $carts['email'] = $u->email;
                    $cartsWithItemsAndInfo[] = $carts;
                    // $cartsWithItemsAndInfo['length_of_time']  = round(abs($item->last_active - $item->start_time) / 60,2)." minutes";
                    // $cartsWithItemsAndInfo['ip_address']  = $item->ip_address;
                    // $cartsWithItemsAndInfo['referrer']    = $item->referrer;
                } else {
                    $cartsWithItems[] = $carts;
                    // $cartsWithItems['length_of_time']  = round(abs($item->last_active - $item->start_time) / 60,2)." minutes";
                    // $cartsWithItems['ip_address']  = $item->ip_address;
                    // $cartsWithItems['referrer']    = $item->referrer;
                }

            } else {
                $item->length_of_time = round(abs($item->last_active - $item->start_time) / 60, 2) . " minutes";
                $cartsWithoutItems[] = $item;
            }
        }
        //Added the count
        $allCarts['count'] = count($allCarts);
        $cartsWithoutItems['count'] = count($cartsWithoutItems);
        $cartsWithItems['count'] = count($cartsWithItems); //for the added values at the top
        $cartsWithItemsAndInfo['count'] = count($cartsWithItemsAndInfo); //for the added values at the top

        // eDebug($allCarts);
        // eDebug($cartsWithoutItems);
        // eDebug($cartsWithItems);
        // eDebug($cartsWithItemsAndInfo);

        $summary['totalcarts'] = $allCarts['count'];
        $summary['valueproducts'] = (int)($valueproducts);
        $summary['cartsWithoutItems'] = round(($allCarts['count'] ? $cartsWithoutItems['count'] / $allCarts['count'] : 0) * 100, 2) . '%';
        $summary['cartsWithItems'] = round(($allCarts['count'] ? $cartsWithItems['count'] / $allCarts['count'] : 0) * 100, 2) . '%';
        $summary['cartsWithItemsAndInfo'] = round(($allCarts['count'] ? $cartsWithItemsAndInfo['count'] / $allCarts['count'] : 0) * 100, 2) . '%';

        // eDebug($summary, true);
        assign_to_template(array(
            'summary'               => $summary,
            'cartsWithoutItems'     => $cartsWithoutItems,
            'cartsWithItems'        => $cartsWithItems,
            'cartsWithItemsAndInfo' => $cartsWithItemsAndInfo
        ));
        /*
        $this->setDateParams();
        $except = array('order_discounts', 'billingmethod', 'order_status_changes', 'billingmethod','order_discounts');
        //$orders = $this->o->find('all','purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend,null,null,null,true,false,$except,true);
        // $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS formattedDate FROM orders WHERE created_at
        eDebug(date('Y-m-d'), true);
        // eDebug($this->tend);
        eDebug(date('Y-m-d, g:i:s A', $this->tend));
        $allOrderCount = $this->o->find('count','created_at >= ' . $this->tstart . ' AND created_at <= ' . $this->tend,null,null,null,true,false,$except,true);
        $sql = "SELECT COUNT(DISTINCT(`orders_id`)) AS c FROM " . $db->prefix . "orderitems oi ";
        $sql .= "JOIN " . $db->prefix . "orders o ON  oi.orders_id = o.id ";
        $sql .= "WHERE o.created_at >= " . $this->tstart . " AND o.created_at <= " . $this->tend;
        //$sql .= " AND o.user_id != 0 AND o.order_type_id = 1";

        eDebug($sql);
        $allCartsWithItems = $db->countObjectsBySql($sql);

        $sql = "SELECT COUNT(DISTINCT(`orders_id`)) AS c FROM " . $db->prefix . "orderitems oi ";
        $sql .= "JOIN " . $db->prefix . "orders o ON  oi.orders_id = o.id ";
        $sql .= "WHERE o.created_at >= " . $this->tstart . " AND o.created_at <= " . $this->tend;
        eDebug($sql);
        $realUserCartsWithItems = $db->countObjectsBySql($sql);

        $ordersInCheckout = $this->o->find('count','created_at >= ' . $this->tstart . ' AND created_at <= ' . $this->tend . " AND user_id != 0",null,null,null,true,false,$except,true);

        //$ordersPurchased = $this->o->find('count','purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend . " AND user_id != 0 AND order_type_id = 1",null,null,null,true,false,$except,true);
        //$ordersPurchased = $this->o->find('count','purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend . " AND user_id != 0",null,null,null,true,false,$except,true);
        $ordersPurchased = $this->o->find('count','purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend,null,null,null,true,false,$except,true);
        $orders = $this->o->find('all','purchased >= ' . $this->tstart . ' AND purchased <= ' . $this->tend,null,null,null,true,false,$except,true);

        eDebug("All:" . $allOrderCount);
        eDebug("Carts w/ Items:" . $allCartsWithItems);
        eDebug("Carts w/ Items in Checkout:" . $ordersInCheckout);
        eDebug("Purchased:" . $ordersPurchased);

        $totalAbandoned = ($allCartsWithItems - $ordersPurchased) / $allCartsWithItems;
        $checkoutAbandoned = ($ordersInCheckout - $ordersPurchased) / $ordersInCheckout;
        eDebug("Total Abandoned: " . $totalAbandoned);
        eDebug("Checkout Abandoned: " . $checkoutAbandoned);




        $quickrange_default = isset($this->params['quickrange']) ? $this->params['quickrange'] : $this::LAST_MONTH;
        assign_to_template(array('orders'=>$oar,'quickrange'=>$this->quickrange,'quickrange_default'=>$quickrange_default));
        assign_to_template(array('prev_date'=>$this->prev_date, 'now_date'=>$this->now_date, 'now_hour'=>$this->now_hour, 'now_min'=>$this->now_min, 'now_ampm'=>$this->now_ampm, 'prev_hour'=>$this->prev_hour, 'prev_min'=>$this->prev_min, 'prev_ampm'=>$this->prev_ampm));
        */
    }

    /**
     * A Product Report
     */
    function batch_export() {
        global $db;

        //eDebug($this->params);
        //$sql = "SELECT * INTO OUTFILE '" . BASE . "tmp/export.csv' FIELDS TERMINATED BY ','  FROM " . $db->prefix . "product WHERE 1 LIMIT 10";
//        $out = '"id","parent_id","child_rank","title","body","model","warehouse_location","sef_url","canonical","meta_title","meta_keywords","meta_description","tax_class_id","quantity","availability_type","base_price","special_price","use_special_price","active_type","product_status_id","category1","category2","category3","category4","category5","category6","category7","category8","category9","category10","category11","category12","surcharge","category_rank","feed_title","feed_body"' . chr(13) . chr(10);
        $out = '"id","parent_id","child_rank","title","body","model","warehouse_location","sef_url","meta_title","meta_keywords","meta_description","tax_class_id","quantity","availability_type","base_price","special_price","use_special_price","active_type","product_status_id","category1","category2","category3","category4","category5","category6","category7","category8","category9","category10","category11","category12","surcharge","category_rank","feed_title","feed_body","weight","width","height","length","companies_id"' . chr(13) . chr(10);
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $sql = expSession::get('product_export_query');
            if (empty($sql))
                $sql = 'SELECT DISTINCT(p.id) FROM ' . $db->tableStmt('product') . ' AS p WHERE (parent_id=0)';
            //eDebug($sql);
            //expSession::set('product_export_query','');
            $prods = $db->selectArraysBySql($sql);
            //eDebug($prods);
        } else {
            foreach ($this->params['act-upon'] as $prod) {
                $prods[] = array('id' => $prod);
            }
        }
        set_time_limit(0);
        $baseProd = new product();

        //$p = new product($pid['id'], false, false);
        foreach ($prods as $pid) {
            $except = array('company', 'crosssellItem', 'optiongroup', 'product_notes', 'product_status');
            $p = $baseProd->find('first', 'id=' . $pid['id'], null, null, 0, true, false, $except, true);

            //eDebug($p,true);
            $out .= expString::outputField($p->id);
            $out .= expString::outputField($p->parent_id);
            $out .= expString::outputField($p->child_rank);
            $out .= expString::outputField($p->title);
            $out .= expString::outputField(expString::stripLineEndings($p->body), ",", true);
            $out .= expString::outputField($p->model);
            $out .= expString::outputField($p->warehouse_location);
            $out .= expString::outputField($p->sef_url);
//            $out .= expString::outputField($p->canonical);  //FIXME this is NOT in import
            $out .= expString::outputField($p->meta_title);
            $out .= expString::outputField($p->meta_keywords);
            $out .= expString::outputField($p->meta_description);
            $out .= expString::outputField($p->tax_class_id);
            $out .= expString::outputField($p->quantity);
            $out .= expString::outputField($p->availability_type);
            $out .= expString::outputField($p->base_price);
            $out .= expString::outputField($p->special_price);
            $out .= expString::outputField($p->use_special_price);
            $out .= expString::outputField($p->active_type);
            $out .= expString::outputField($p->product_status_id);

            $rank = 0;
            //eDebug($p);
            for ($x = 0; $x < 12; $x++) {
                $this->catstring = '';
                if (isset($p->storeCategory[$x])) {
                    $out .= expString::outputField(storeCategory::buildCategoryString($p->storeCategory[$x]->id, true));
                    $rank = $db->selectValue('product_storeCategories', 'rank', 'product_id=' . $p->id . ' AND storecategories_id=' . $p->storeCategory[$x]->id);
                } else $out .= ',';
            }
            $out .= expString::outputField($p->surcharge);
            $out .= expString::outputField($rank);
            $out .= expString::outputField($p->feed_title);
            $out .= expString::outputField($p->feed_body);
            $out .= expString::outputField($p->weight);
            $out .= expString::outputField($p->height);
            $out .= expString::outputField($p->width);
            $out .= expString::outputField($p->length);
            $out .= expString::outputField($p->companies_id, chr(13) . chr(10)); //Removed the extra "," in the last element

            foreach ($p->childProduct as $cp) {
                //$p = new product($pid['id'], true, false);
                //eDebug($p,true);
                $out .= expString::outputField($cp->id);
                $out .= expString::outputField($cp->parent_id);
                $out .= expString::outputField($cp->child_rank);
                $out .= expString::outputField($cp->title);
                $out .= expString::outputField(expString::stripLineEndings($cp->body));
                $out .= expString::outputField($cp->model);
                $out .= expString::outputField($cp->warehouse_location);
                $out .= expString::outputField($cp->sef_url);
//                $out .= expString::outputField($cp->canonical);  //FIXME this is NOT in import
                $out .= expString::outputField($cp->meta_title);
                $out .= expString::outputField($cp->meta_keywords);
                $out .= expString::outputField($cp->meta_description);
                $out .= expString::outputField($cp->tax_class_id);
                $out .= expString::outputField($cp->quantity);
                $out .= expString::outputField($cp->availability_type);
                $out .= expString::outputField($cp->base_price);
                $out .= expString::outputField($cp->special_price);
                $out .= expString::outputField($cp->use_special_price);
                $out .= expString::outputField($cp->active_type);
                $out .= expString::outputField($cp->product_status_id);
                $out .= ',,,,,,,,,,,,';  // for store categories
                $out .= expString::outputField($cp->surcharge);
                $out .= ',,,'; // for rank, feed title, feed body
                $out .= expString::outputField($cp->weight);
                $out .= expString::outputField($cp->height);
                $out .= expString::outputField($cp->width);
                $out .= expString::outputField($cp->length);
                $out .= expString::outputField($cp->companies_id, chr(13) . chr(10)); //Removed the extra "," in the last element
                //echo($out);
            }

        }

        $outFile = 'tmp/product_export_' . time() . '.csv';
        $outHandle = fopen(BASE . $outFile, 'wb');
        fwrite($outHandle, $out);
        fclose($outHandle);

        echo "<br/><br/>".gt('Download the file here').": <a href='" . PATH_RELATIVE . $outFile . "'>".gt('Product Export')."</a>";

        /*eDebug(BASE . "tmp/export.csv");
        $db->sql($sql);
        eDebug($db->error());*/
        /*OPTIONALLY ENCLOSED BY '" . '"' .
        "' ESCAPED BY '\\'
        LINES TERMINATED BY '" . '\\n' .
        "' */
    }

    /**
     * FIXME Purpose not known/incomplete
     */
    function payment_report() {
//        global $db;

        $payment_methods = array('-1' => '', 'V' => 'Visa', 'MC' => 'Mastercard', 'D' => 'Discover', 'AMEX' => 'American Express', 'PP' => 'PayPal', 'GC' => 'Google Checkout', 'Other' => 'Other');
        //5 paypal
        //4 credit card - VisaCard, MasterCard, AmExCard, DiscoverCard

        $oids = "(";

        eDebug(expSession::get('order_print_query'));
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            //$sql = expSession::get('order_print_query');
            //eDebug($sql);
            //expSession::set('product_export_query','');
            //$orders = $db->selectArraysBySql($sql);
            $obs = expSession::get('order_export_values');
            usort($obs, array("reportController", "sortPrintOrders"));
            foreach ($obs as $ob) {
                $oids .= $ob->id . ",";
            }
            //eDebug($prods);
        } else {
            if (!empty($this->params['act-upon'])) foreach ($this->params['act-upon'] as $order) {
                $oids .= $order->id . ",";
            }
        }
        $oids = strrev(expUtil::right(strrev($oids), strlen($oids) - 1));
        $oids .= ")";
        eDebug($oids);
        //eDebug($orders,true);

    }

    /**
     * A Product Report
     */
    function status_export() {
        global $db;

        //eDebug($this->params);
        //$sql = "SELECT * INTO OUTFILE '" . BASE . "tmp/export.csv' FIELDS TERMINATED BY ','  FROM " . $db->prefix . "product WHERE 1 LIMIT 10";

        //is | parent_id | SKU |WAREHOUSE LOCATION | Title | Vendor/Manufacturer | Product Status | Notes

        $out = '"id","parent_id","model","warehouse_location","title","vendor","product_status","notes"' . chr(13) . chr(10);
        if (isset($this->params['applytoall']) && $this->params['applytoall'] == 1) {
            $sql = expSession::get('product_export_query');
            if (empty($sql))
                $sql = 'SELECT DISTINCT(p.id) FROM ' . $db->tableStmt('product') . ' AS p WHERE (parent_id=0)';
            //eDebug($sql);
            //expSession::set('product_export_query','');
            $prods = $db->selectArraysBySql($sql);
            //eDebug($prods);
        } else {
            foreach ($this->params['act-upon'] as $prod) {
                $prods[] = array('id' => $prod);
            }
        }

        $stats = new product_status();
        $stats = $stats->find('all');

//        $statuses = array();
        $statuses = array(0=>'');
        foreach ($stats as $stat) {
            $statuses[$stat->id] = $stat->title;
        }

//        eDebug($statuses);

        set_time_limit(0);
        $baseProd = new product();

        //$p = new product($pid['id'], false, false);
        //id | parent_id | SKU |WAREHOUSE LOCATION | Title | Vendor/Manufacturer | Product Status | Notes
        foreach ($prods as $pid) {
            $except = array('crosssellItem', 'optiongroup', 'childProduct');
            $p = $baseProd->find('first', 'id=' . $pid['id'], null, null, 0, true, true, $except, true);

            /*if(count($p->expSimpleNote))
            {
                eDebug($p,true);
            }
            else
            {
                continue;
            }*/

            $out .= expString::outputField($p->id);
            $out .= expString::outputField($p->parent_id);
            $out .= expString::outputField($p->model);
            $out .= expString::outputField($p->warehouse_location);
            $out .= expString::outputField($p->title);
            $out .= expString::outputField($p->company->title);
            $out .= expString::outputField($statuses[$p->product_status_id]);

            $noteString = '';
            foreach ($p->expSimpleNote as $note) {
                $noteString .= "(" . $note->name . " - " . date('M d Y H:i A', $note->created_at) . ") " . $note->body . "||";
            }
            $out .= expString::outputField($noteString, chr(13) . chr(10));

            $cps = $baseProd->find('all', 'parent_id=' . $p->id, null, null, 0, true, true, $except, true);
            foreach ($cps as $cp) {
                $out .= expString::outputField($cp->id);
                $out .= expString::outputField($cp->parent_id);
                $out .= expString::outputField($cp->model);
                $out .= expString::outputField($cp->warehouse_location);
                $out .= expString::outputField($cp->title);
                $out .= expString::outputField($cp->company->title);
                $out .= expString::outputField($statuses[$cp->product_status_id]);

                $noteString = '';
                foreach ($cp->expSimpleNote as $note) {
                    $noteString .= "(" . $note->name . " - " . date('M d Y H:i A', $note->created_at) . ") " . $note->body . "||";
                }
                $out .= expString::outputField($noteString, chr(13) . chr(10));
            }
        }

        //eDebug($out,true);
        $outFile = 'tmp/product_status_' . time() . '.csv';
        $outHandle = fopen(BASE . $outFile, 'wb');
        fwrite($outHandle, $out);
        fclose($outHandle);

        echo "<br/><br/>".gt('Download the file here').": <a href='" . PATH_RELATIVE . $outFile . "'>".gt('Product Export')."</a>";

        /*eDebug(BASE . "tmp/export.csv");
        $db->sql($sql);
        eDebug($db->error());*/
        /*OPTIONALLY ENCLOSED BY '" . '"' .
        "' ESCAPED BY '\\'
        LINES TERMINATED BY '" . '\\n' .
        "' */
    }

    //public $catstring = '';

    /**
     * Get parameters for an products report
     */
    function product_report() {
        $pts = storeController::getProductTypes();
        $newPts = array();
        foreach ($pts as $pt) {
            $newPts[$pt] = $pt;
        }
        assign_to_template(array(
            'product_types' => $newPts
        ));
    }

}

?>