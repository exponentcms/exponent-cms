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

class orderController extends expController {
    protected $add_permissions = array(
        'showall'             => 'Manage',
        'show'                => 'View Orders',
        'setStatus'           => 'Change Status',
        'edit_payment_info'   => 'Edit Payment Info',
        'save_payment_info'=> 'Save Payment Info',
        'edit_address'        => 'Edit Address',
        'save_address'=> 'Save Address',
        'edit_order_item'     => 'Edit Order Item',
        'save_order_item'=> 'Save Order Item',
        'add_order_item'      => 'Add Order Item',
        'save_new_order_item'=> 'Save New Order Item',
        'edit_totals'         => 'Edit Totals',
        'save_totals'=> 'Save Totals',
        'edit_invoice_id'     => 'Edit Invoice Id',
        'save_invoice_id'=> 'Save Invoice Id',
        'update_sales_reps'   => 'Manage Sales Reps',
        'quickfinder'=> 'Do a quick order lookup',
        'edit_shipping_method'=> 'Edit Shipping Method',
        'save_shipping_method'=> 'Save Shipping Method',
        'create_new_order'    => 'Create A New Order',
        'save_new_order'=> 'Save a new order',
        'createReferenceOrder'=> 'Create Reference Order',
        'save_reference_order'=> 'Save Reference Order'
    );

    static function displayname() {
        return gt("e-Commerce Order Manager");
    }

    static function description() {
        return gt("Use this module to manage the orders from your ecommerce store.");
    }

    function showall() {
        global $db;

        expHistory::set('viewable', $this->params);

        // remove abaondoned carts
        /*$count = $db->countObjects('orders', 'purchased=0');
        for($i=0; $i<$count; $i++) {
            // get the cart
            $cart = $db->selectObject('orders','purchased=0');
            
            // check to make sure this isn't an active session
            $ticket = $db->selectObject('sessionticket', "ticket='".$cart->sessionticket_ticket."'");
            if (empty($ticket)) {
                // delete all the order items for this cart and their shippingmethods
                foreach($db->selectObjects('orderitems', 'orders_id='.$cart->id) as $oi) {
                    $db->delete('shippingmethods', 'id='.$oi->shippingmethods_id);
                    $db->delete('orderitems', 'orders_id='.$cart->id);    
                }
                
                // delete the billing methods for this cart.
                $db->delete('billingmethods', 'orders_id='.$cart->id);
                $db->delete('orders', 'id='.$cart->id);
            }           
            
        } */

        // find orders with a "closed" status type
//        $closed_count = 0;
        if (empty($this->params['showclosed'])) {
            $closed_status = $db->selectColumn('order_status', 'id', 'treat_as_closed=1');
            $closed_status = implode(',',$closed_status);
//            $status_where  = '';
            $status_where  = ' AND order_status_id NOT IN (' . $closed_status . ')';

//            foreach ($closed_status as $status) {
//                if (empty($status_where)) {
//                    $status_where .= ' AND (order_status_id!=' . $status;
//                } else {
//                    $status_where .= ' AND order_status_id!=' . $status;
//                }
//                $closed_count += $db->countObjects('orders', 'order_status_id=' . $status);
//            }
            $closed_count = $db->countObjects('orders', 'order_status_id IN (' . $closed_status . ')');
        } else {
            $status_where = '';
            $closed_count = -1;
        }

        // build out a SQL query that gets all the data we need and is sortable.
        $sql = 'SELECT o.*, b.firstname as firstname, b.billing_cost as total, b.transaction_state as paid, b.billingcalculator_id as method, b.middlename as middlename, b.lastname as lastname, os.title as status, ot.title as order_type ';
        $sql .= 'FROM ' . $db->prefix . 'orders o, ' . $db->prefix . 'billingmethods b, ';
        $sql .= $db->prefix . 'order_status os, ';
        $sql .= $db->prefix . 'order_type ot ';
        $sql .= 'WHERE o.id = b.orders_id AND o.order_status_id = os.id AND o.order_type_id = ot.id AND o.purchased > 0';
  //FIXME this sql isn't correct???
//        if (!empty($status_where)) {
//            $status_where .= ')';
            $sql .= $status_where;
//        }
        if (ECOM_LARGE_DB) {
            $limit = empty($this->config['limit']) ? 50 : $this->config['limit'];
        } else {
            $limit = 0;  // we'll paginate on the page
        }
        //eDebug($sql, true);
        $page = new expPaginator(array(
            //'model'=>'order',
            'sql'       => $sql,
            'order'     => 'purchased',
            'dir'       => 'DESC',
            'limit'     => $limit,
            'page'      => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=> $this->params['controller'],
            'action'    => $this->params['action'],
            'columns'   => array(
                gt('Customer')      => 'lastname',
                gt('Inv #')         => 'invoice_id',
                gt('Total')         => 'total',
                gt('Payment')       => 'method',
                gt('Purchased')     => 'purchased',
                gt('Type')          => 'order_type_id',
                gt('Status')        => 'order_status_id',
                gt('Ref')           => 'orig_referrer',
            )
        ));
        //eDebug($page,true);
        assign_to_template(array(
            'page'        => $page,
            'closed_count'=> $closed_count,
            'new_order'   => order::getDefaultOrderStatus()
        ));
    }

    function show() {
        global $db, $user;
//eDebug($_REQUEST);
//eDebug($this->params,true);
//if (!empty($this->params['printerfriendly'])) $_REQUEST['printerfriendly'] = 1;

        expHistory::set('viewable', $this->params);

        if (!empty($this->params['invoice']) && empty($this->params['id'])) {
            $ord = new order();
            $order = $ord->find('first', 'invoice_id=' . $this->params['invoice']);
            $this->params['id'] = $order->id;
        } elseif (!empty($this->params['id'])) {
            $order = new order($this->params['id']);
        }
        if (empty($order->id)) {
            flash('notice', gt('That order does not exist.'));
            expHistory::back();
        }

        // We're forcing the location. Global store setting will always have this loc
//        $storeConfig = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));

        $billing         = new billing($this->params['id']);
        $status_messages = $db->selectObjects('order_status_messages');
        $order_type      = $order->getOrderType();
        //eDebug($order->billingmethod[0]->billingtransaction);
        $order->billingmethod[0]->billingtransaction = array_reverse($order->billingmethod[0]->billingtransaction);
        if (empty($order->billingmethod[0]->billingtransaction[0]->billingcalculator_id)) {
            $calc_name = $order->billingmethod[0]->billingcalculator->calculator_name;
            $order->billingmethod[0]->billingtransaction[0]->billingcalculator = new $calc_name();
        }
        //eDebug($order->billingmethod[0]->billingtransaction);
        if (isset($this->params['printerfriendly']))
            $pf = $this->params['printerfriendly'];
        else
            $pf = 0;

        $to_addresses[] = $order->billingmethod[0]->email;
//        $s              = array_pop($order->shippingmethods);  //FIXME we don't really want to 'pop' it off the object
        $s              = reset($order->shippingmethods);
        if ($s->email != $order->billingmethod[0]->email) $to_addresses[] = $s->email;

        $from_addresses                                        = array();
        $from_addresses[SMTP_FROMADDRESS]                      = SMTP_FROMADDRESS;
        $from_addresses[ecomconfig::getConfig('from_address')] = ecomconfig::getConfig('from_address');
        $from_addresses[$user->email]                          = $user->email;
        $from_addresses['other']                               = 'Other (enter below)';
        $from_addresses = array_filter($from_addresses);
        $from_default = ecomconfig::getConfig('from_address');
        $from_default = !empty($from_default) ? $from_default : SMTP_FROMADDRESS;

        $email_subject = 'Message from ' . ecomconfig::getConfig('storename') . ' about your order (#' . $order->invoice_id . ')';

        $order->setReferencingIds();

        $css = file_get_contents(BASE . 'framework/modules/ecommerce/assets/css/print-invoice.css');

        assign_to_template(array(
            'css'            => $css,
            'pf'             => $pf,
            'order'          => $order,
            'order_user'     => new user($order->user_id),
//            'shipping'       => $order->orderitem[0],  //FIXME what about new orders with no items??
            'billing'        => $billing,
            'billinginfo'    => $billing->getBillingInfo(),
            'messages'       => $status_messages,
            'order_type'     => $order_type,
//            'storeConfig'    => $storeConfig->config,
            'sales_reps'     => order::getSalesReps(),
            'from_addresses' => $from_addresses,
            'from_default'   => $from_default,
            'email_subject'  => $email_subject,
            'to_addresses'   => implode(',', $to_addresses)
        ));
        if ($order->shipping_required) {
            assign_to_template(array(
                'shipping'       => $order->orderitem[0],  //FIXME what about new orders with no items??
            ));
        }
    }

    function myOrder() {
        global $user, $db;

        $order = new order($this->params['id']);
        if ($order->purchased == 0)
            flashAndFlow('error', gt('You do not have permission to view this order.'));

        $this->loc->src = "@globalstoresettings";

        // We're forcing the location. Global store setting will always have this loc
//        $storeConfig = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));

        //check here for the hash in the params, or session set w/ perms to view...shs = xaf7y0s87d7elshd70 etc
        //if present, prompt user for the order number and email address on the order
        //and if they pass, show the order to them. Need to maybe set something in the session then for subsequent
        //viewing of the order?        
        if ($user->id != $order->user_id) {
            if ($user->isAdmin()) {
                redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['id']));
            } else {
                flashAndFlow('error', gt('You do not have permission to view this order.'));
            }
        }

        expHistory::set('viewable', $this->params);

        $billing            = new billing($this->params['id']);
        $status_messages    = $db->selectObjects('order_status_messages');
        $order_type         = $order->getOrderType();
        $order->total_items = 0;
        foreach ($order->orderitem as $item) {
            $order->total_items += $item->quantity;
            $order->shipping_city  = $item->shippingmethod->city;
            $order->shipping_state = $item->shippingmethod->state;
        }
        $state                   = new geoRegion($order->shipping_state);
        $country                 = new geoCountry($state->country_id);
        $order->shipping_country = $country->iso_code_3letter;
        $order->shipping_state   = $state->name;

        //eDebug($order,true);

        $order->billingmethod[0]->billingtransaction = array_reverse($order->billingmethod[0]->billingtransaction);
        if (isset($this->params['printerfriendly'])) $pf = $this->params['printerfriendly'];
        else $pf = 0;
        $css = file_get_contents(BASE . 'framework/modules/ecommerce/assets/css/print-invoice.css');

        $order->calculateGrandTotal();

        $trackMe = false;
        if (isset($this->params['tc']) && $this->params['tc'] == 1) {
            if (expSession::is_set('orders_tracked')) {
                $trackingArray = expSession::get('orders_tracked');
                if (in_array($order->invoice_id, $trackingArray)) {
                    $trackMe = false;
                } else {
                    $trackMe         = true;
                    $trackingArray[] = $order->invoice_id;
                    expSession::set('orders_tracked', $trackingArray);
                }
            } else {
                $trackMe         = true;
                $trackingArray[] = $order->invoice_id;
                expSession::set('orders_tracked', $trackingArray);
            }
        }
        if (DEVELOPMENT != 0)
            $trackMe = false;
        assign_to_template(array(
            'printerfriendly'=> $pf,
            'css'            => $css,
            'order'          => $order,
            'shipping'       => $order->orderitem[0],
            'billing'        => $billing,
            'billinginfo'    => $billing->getBillingInfo(),
            'order_type'     => $order_type,
//            'storeConfig'    => $storeConfig->config,
            'tc'             => $trackMe,
            'checkout'       => !empty($this->params['tc'])  //FIXME we'll use the tc param for now
        ));

    }

    function email() {
        global $template, $user;

        // setup a template suitable for emailing
        $template = expTemplate::get_template_for_action($this, 'email_invoice', $this->loc);
        $order    = new order($this->params['id']);
        $billing  = new billing($this->params['id']);
//        if ($billing->calculator != null) {
//            $billinginfo = $billing->calculator->userView(unserialize($billing->billingmethod->billing_options));
//        } else {
//            if (empty($opts)) {
//                $billinginfo = false;
//            } else {
//                $billinginfo = gt("No Cost");
//                if (!empty($opts->payment_due)) {
//                    $billinginfo .= '<br>'.gt('Payment Due') . ': ' . expCore::getCurrencySymbol() . number_format($opts->payment_due, 2, ".", ",");
//                }
//            }
//        }
        $css = file_get_contents(BASE.'framework/modules/ecommerce/assets/css/print-invoice.css');
        assign_to_template(array(
            'css'         => $css,
            'order'       => $order,
            'shipping'    => $order->orderitem[0],
            'billing'     => $billing,
            'billinginfo' => $billing->getBillingInfo(),
        ));

        // build the html and text versions of the message
        $html = $template->render();
        $txt  = strip_tags($html);

        // send email invoices to the admins if needed
        if (ecomconfig::getConfig('email_invoice') == true) {
            $addresses = explode(',', ecomconfig::getConfig('email_invoice_addresses'));
            foreach ($addresses as $address) {
                $mail = new expMail();
                $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
                if (empty($from[0])) $from = SMTP_FROMADDRESS;
                $mail->quickSend(array(
                    'html_message'=> $html,
                    'text_message'=> $txt,
                    'to'          => trim($address),
                    'from'        => $from,
                    'subject'     => 'An order was placed on the ' . ecomconfig::getConfig('storename'),
                ));
            }
        }

        // email the invoice to the user if we need to
        if (ecomconfig::getConfig('email_invoice_to_user') == true && !empty($user->email)) {
            $usermsg = "<p>" . ecomconfig::getConfig('invoice_msg') . "<p>";
            $usermsg .= $html;
//            $usermsg .= ecomconfig::getConfig('ecomfooter');

            $mail = new expMail();
            $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
            if (empty($from[0])) $from = SMTP_FROMADDRESS;
            $mail->quickSend(array(
                'html_message'=> $usermsg,
                'text_message'=> $txt,
                'to'          => array(trim($user->email) => trim(user::getUserAttribution($user->id))),
                //'to'=>$order->billingmethod[0]->email,
                'from'        => $from,
                'subject'     => ecomconfig::getConfig('invoice_subject'),
            ));
        }
    }

    function update_shipping() {
        $order                   = new order($this->params['id']);
        $this->params['shipped'] = datetimecontrol::parseData('shipped', $this->params);
        $order->update($this->params);
        flash('message', gt('Shipping information updated.'));
        expHistory::back();
    }

    function getPDF($orders = null) {
        global $user, $timer;

        $invoice = '<!DOCTYPE HTML><HTML><HEAD>';
        // the basic styles
        if (!bs3())
            $invoice .= '<link rel="stylesheet" type="text/css" href="'.URL_FULL.'external/normalize/normalize.css" >';
        if (!bs())
//            $invoice .= '<link rel="stylesheet" type="text/css" href="'.URL_FULL.'external/normalize/normalize.css" >';
        if (bs2())
            $invoice .= '<link rel="stylesheet" type="text/css" href="'.URL_FULL.'external/bootstrap/css/bootstrap.css" >';
        if (bs3())
            $invoice .= '<link rel="stylesheet" type="text/css" href="'.URL_FULL.'external/bootstrap3/css/bootstrap.css" >';
        $invoice .= '<link rel="stylesheet" type="text/css" href="'.URL_FULL.'framework/modules/ecommerce/assets/css/print-invoice.css">
        <style>
            html{background:none;}
            #store-header{text-align:left;}
        </style>';
        $invoice .= '</HEAD><BODY>';
        if (is_array($orders)) {
            foreach ($orders as $order) {
                if ($user->isAdmin()) {
                    $invoice .= renderAction(array('controller'=> 'order', 'action'=> 'show', 'view'=> 'show_printable', 'id'=> $order['id'], 'printerfriendly'=> '1', 'no_output'=> 'true'));
                    //eDebug($order['id'] . ": " . $timer->mark());                        
                } else {
                    $invoice .= renderAction(array('controller'=> 'order', 'action'=> 'myOrder', 'view'=> 'show_printable', 'id'=> $order['id'], 'printerfriendly'=> '1', 'no_output'=> 'true'));
                }
                $invoice .= '<p style="page-break-before: always;"></p>';
            }
            $invoice = substr($invoice, 0, (strlen($invoice) - 42));
        } else {
            if ($user->isAdmin()) {
                $invoice .= renderAction(array('controller'=> 'order', 'action'=> 'show', 'view'=> 'show_printable', 'id'=> $this->params['id'], 'printerfriendly'=> '1', 'no_output'=> 'true'));
            } else {
                $invoice .= renderAction(array('controller'=> 'order', 'action'=> 'myOrder', 'view'=> 'show_printable', 'id'=> $this->params['id'], 'printerfriendly'=> '1', 'no_output'=> 'true'));
            }
        }

        $invoice .= "</BODY></HTML>";
        $invoice = mb_convert_encoding($invoice, 'HTML-ENTITIES', "UTF-8");
        // eDebug($invoice);
        $org_name = str_ireplace(" ", "_", ORGANIZATION_NAME);

        //eDebug("Here",1);
        // Actually create/output the pdf file

        /**
         * to do this same thing as below using html2pdf
         * //FIXME uncomment to implement, comment out above
        require_once(BASE.'external/html2pdf_v4.03/html2pdf.class.php');
        $html2pdf = new HTMLTPDF('P', 'LETTER', substr(LOCALE,0,2));
        $html2pdf->writeHTML($invoice);
        $html2pdf->Output($org_name . "_Invoice" . ".pdf",HTMLTOPDF_OUTPUT?'D':'');
        exit();
         */
        /**
         * to do this same thing as below using dompdf
         * //FIXME uncomment to implement, comment out above
        require_once(BASE.'external/dompdf/dompdf_config.inc.php');
        $mypdf = new DOMPDF();
        $mypdf->load_html($invoice);
        $mypdf->set_paper('letter','portrait');
        $mypdf->render();
        $mypdf->stream($org_name . "_Invoice" . ".pdf",array('Attachment'=>HTMLTOPDF_OUTPUT));
        exit();
         */
        /**
         * to do this same thing as below using expHtmlToPDF
         */
        $mypdf = new expHtmlToPDF('Letter','portrait',$invoice);
        $mypdf->createpdf('D',$org_name . "_Invoice" . ".pdf");
        exit();

        if (stristr(PHP_OS, 'Win')) {
            if (file_exists(HTMLTOPDF_PATH)) {
                do {
                    $htmltopdftmp = HTMLTOPDF_PATH_TMP . mt_rand() . '.html';
                } while (file_exists($htmltopdftmp));
            }
            file_put_contents($htmltopdftmp, $invoice);

            exec(HTMLTOPDF_PATH . " " . $htmltopdftmp . " " . HTMLTOPDF_PATH_TMP . $org_name . "_Invoice.pdf");
            $this->returnFile(HTMLTOPDF_PATH_TMP . $org_name . "_Invoice.pdf", $org_name . "_Invoice.pdf", "pdf");
            exit();
        } else {

            //require_once(BASE.'external/tcpdf/config/lang/eng.php');
            //require_once(BASE.'external/tcpdf/tcpdf.php');

            //----
            // create new PDF document
            /*$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set default font subsetting mode
//$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
//$pdf->SetFont('helvetica', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();
//eDebug($invoice,1);
// Print text using writeHTMLCell()
//$pdf->writeHTML($w=0, $h=0, $x='', $y='', $invoice, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
$pdf->writeHTML($invoice);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
ob_clean();
$pdf->Output('example_001.pdf', 'I');
exit();
//============================================================+
// END OF FILE
//============================================================+

            
            // create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor(ORGANIZATION_NAME);
            $pdf->SetTitle($org_name . "_Invoice");
            $pdf->SetSubject($org_name . "_Invoice");
            // remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            //set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

            //set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $invoice, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
            $pdf->Output($org_name . "_Invoice" . ".pdf", 'I');
            exit();*/
            eDebug("Done rendering invoice html. Starting PDF Generation: " . $timer->mark());
            $pdfer = new expHtmlToPDF('Letter', 'Portrait', $invoice);
//            $pdfer->set_html($invoice);
//            $pdfer->set_orientation('Portrait');
//            $pdfer->set_page_size('Letter');
            $pdfer->set_grayscale(true);
//            $pdfer->render();
            eDebug("Done rendering PDF " . $timer->mark());
//            exit();
            ob_clean();
            $pdfer->createpdf('D', $org_name . "_Invoice" . ".pdf");
            exit();
        }
    }

    private function returnFile($file, $name, $mime_type = '') {
        /*
            This function takes a path to a file to output ($file),
            the filename that the browser will see ($name) and
            the MIME type of the file ($mime_type, optional).

            If you want to do something on download abort/finish,
            register_shutdown_function('function_name');
        */
        if (!is_readable($file)) die('File not found or inaccessible!');

        $size = filesize($file);
        $name = rawurldecode($name);

        /* Figure out the MIME type (if not specified) */
        $known_mime_types = array(
            "pdf"  => "application/pdf",
            "txt"  => "text/plain",
            "html" => "text/html",
            "htm"  => "text/html",
            "exe"  => "application/octet-stream",
            "zip"  => "application/zip",
            "doc"  => "application/msword",
            "xls"  => "application/vnd.ms-excel",
            "ppt"  => "application/vnd.ms-powerpoint",
            "gif"  => "image/gif",
            "png"  => "image/png",
            "jpeg" => "image/jpg",
            "jpg"  => "image/jpg",
            "php"  => "text/plain"
        );

        if ($mime_type == '') {
//            $file_extension = strtolower(substr(strrchr($file, "."), 1));
//
//            if (array_key_exists($file_extension, $known_mime_types)) {
//                $mime_type = $known_mime_types[$file_extension];
//            } else {
//                $mime_type = "application/force-download";
//            }
            $mime_type = expFile::getMimeType($file);
        }

        //@ob_end_clean(); //turn off output buffering to decrease cpu usage
        // required for IE, otherwise Content-Disposition may be ignored
        if (ini_get('zlib.output_compression'))
            ini_set('zlib.output_compression', 'Off');

        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');

        /* The three lines below basically make the download non-cacheable */
        header('Cache-control: private');
        header('Pragma: private');
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        // multipart-download and download resuming support
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(',', $range, 2);
            list($range, $range_end) = explode('-', $range);

            $range = intval($range);

            $range_end  = (!$range_end) ? $size - 1 : intval($range_end);
            $new_length = $range_end - $range + 1;

            header('HTTP/1.1 206 Partial Content');
            header('Content-Length: ' . $new_length);
            header('Content-Range: bytes ' . ($range - $range_end / $size));
        } else {
            $new_length = $size;

            header('Content-Length: ' . $size);
        }

        /* output the file itself */
        $chunksize  = 1 * (1024 * 1024); //you may want to change this
        $bytes_send = 0;

        if ($file = fopen($file, 'r')) {
            if (isset($_SERVER['HTTP_RANGE'])) fseek($file, $range);

            while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length)) {
                $buffer = fread($file, $chunksize);

                print($buffer);
                flush();

                $bytes_send += strlen($buffer);
            }

            fclose($file);
        } else {
            die('Error - can not open file.');
        }
    }

    function set_order_type() {  //FIXME never used
//        global $db;

        if (empty($this->params['id'])) expHistory::back();

        // get the order and update the type
        $order                = new order($this->params['id']);
        $order->order_type_id = $this->params['order_type_id'];
        $order->save();
        flash('message', gt('Invoice #') . $order->invoice_id . ' ' . gt('has been set to') . ' ' . $order->getOrderType());
        expHistory::back();
    }

    /**
     * Change order status and email notification if necessary
     */
    function setStatus() {
        global $db, $template;

        if (empty($this->params['id'])) expHistory::back();

        // get the order and create a new order_Status_change
        $order = new order($this->params['id']);

        //set order type
        if (isset($this->params['order_type_id'])) $order->order_type_id = $this->params['order_type_id'];

        //only save the status change if it actually changed to something different
        if ($order->order_status_id != $this->params['order_status_id']) {
            if (empty($this->params['order_status_messages'])) {
                $comment = $this->params['comment'];
            } else {
                $comment = $this->params['order_status_messages'];
            }
            // save the order status change
            $change = new order_status_changes();
            $change->from_status_id = $order->order_status_id;
            $change->comment        = $comment;
            $change->to_status_id   = $this->params['order_status_id'];
            $change->orders_id      = $order->id;
            $change->save();

            // update the status of the order
            $order->order_status_id = $this->params['order_status_id'];

            // Save the message for future use if that is what the user wanted.
            if (!empty($this->params['save_message']) && !empty($this->params['comment'])) {
                $message       = new stdClass();
                $message->body = $this->params['comment'];
                $db->insertObject($message, 'order_status_messages');
            }

            // email the user if we need to
            if (!empty($this->params['email_user'])) {
                $email_addy = $order->billingmethod[0]->email;
                if (!empty($email_addy)) {
                    $from_status = $db->selectValue('order_status', 'title', 'id=' . $change->from_status_id);
                    $to_status   = $db->selectValue('order_status', 'title', 'id=' . $change->to_status_id);

                    if ($order->shippingmethod->carrier == 'UPS') {
                        $carrierTrackingLink = "http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=";
                    } elseif ($order->shippingmethod->carrier == 'FedEx') {
                        $carrierTrackingLink = "http://www.fedex.com/Tracking?action=track&tracknumbers=";
                    } elseif ($order->shippingmethod->carrier == 'USPS') {
                        $carrierTrackingLink = "https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=";
                    }

                    assign_to_template(array(
                        'comment'         => $change->comment,
                        'to_status'       => $to_status,
                        'from_status'     => $from_status,
                        'order'           => $order,
                        'date'            => date("F j, Y, g:i a"),
                        'storename'       => ecomconfig::getConfig('storename'),
                        'include_shipping'=> isset($this->params['include_shipping_info']) ? true : false,
                        'tracking_link'    => $carrierTrackingLink . $order->shipping_tracking_number,
                        'carrier'          => $order->shippingmethod->carrier
                    ));

                    $html = $template->render();
                    $html .= ecomconfig::getConfig('ecomfooter');

                    $mail = new expMail();
                    $from = array(ecomconfig::getConfig('from_address') => ecomconfig::getConfig('from_name'));
                    if (empty($from[0])) $from = SMTP_FROMADDRESS;
                    $mail->quickSend(array(
                        'html_message'=> $html,
                        'text_message'=> str_replace("<br>", "\r\n", $template->render()),
                        'to'          => array($email_addy => $order->billingmethod[0]->firstname . ' ' . $order->billingmethod[0]->lastname),
                        'from'        => $from,
                        'subject'     => 'The status of your order (#' . $order->invoice_id . ') has been updated on ' . ecomconfig::getConfig('storename') . '.'
                    ));
                } else {
                    flash('error', gt('The email address was NOT send. An email address count not be found for this customer'));
                }
            }
            flash('message', gt('Order Type and/or Status Updated.'));
        } else {
            flash('message', gt('Order Type and/or Status was not changed.'));
        }

        $order->save();
        expHistory::back();
    }

    function emailCustomer() {
        //eDebug($this->params,true);
        global $db, $template, $user;

        if (empty($this->params['id'])) expHistory::back();

        // get the order
        $order = new order($this->params['id']);

        if (empty($this->params['order_status_messages'])) {
            $email_message = $this->params['email_message'];
        } else {
            $email_message = $this->params['order_status_messages'];
        }

        // Save the message for future use if that is what the user wanted.
        if (!empty($this->params['save_message']) && !empty($this->params['email_message'])) {
            $message       = new stdClass();
            $message->body = $this->params['email_message'];
            $db->insertObject($message, 'order_status_messages');
        }

        $email_addys = explode(',', $this->params['to_addresses']); //$order->billingmethod[0]->email;
        //eDebug($email_addy,true);
        if (!empty($email_addys)) {
            assign_to_template(array(
                'message'=> $email_message
            ));
            $html = $template->render();
            if (!empty($this->params['include_invoice'])) {
                $html .= '<hr><br>';
                $html .= renderAction(array('controller'=> 'order', 'action'=> 'show', 'view'=> 'email_invoice', 'id'=> $this->params['id'], 'printerfriendly'=> '1', 'no_output'=> 'true'));
            } else {
                $html .= ecomconfig::getConfig('ecomfooter');
            }

            //eDebug($html,true);
            if (isset($this->params['from_address'])) {
                if ($this->params['from_address'] == 'other') {
                    $from = $this->params['other_from_address'];
                } else {
                    $from = $this->params['from_address'];
                }
            } else {
                $from = ecomconfig::getConfig('from_address');
            }
            if (empty($from[0])) $from = SMTP_FROMADDRESS;

            if (isset($this->params['email_subject'])) {
                $email_subject = $this->params['email_subject'];
            } else {
                $email_subject = gt('Message from') . ' ' . ecomconfig::getConfig('storename') . ' ' . gt('about your order') . ' (#' . $order->invoice_id . ')';
            }

            $mail = new expMail();
            //FIXME Unless you need each mail sent separately, you can now set 'to'=>$email_addys and let expMail send a single email to all addresses
            foreach ($email_addys as $email_addy) {
                $mail->quickSend(array(
                    'html_message'=> $html,
                    'text_message'=> str_replace("<br>", "\r\n", $template->render()),
                    'to'          => $email_addy,
                    'from'        => $from,
                    'subject'     => $email_subject
                ));
            }
            $emailed_to     = implode(',', $email_addys);

            // manually add/attach an expSimpleNote to the order
            $note           = new expSimpleNote();
            $note->body     = "<strong>[" . gt('action') . "]: " . gt('Emailed message to') . " " . $emailed_to . ":</strong>" . $email_message;
            $note->approved = 1;
            $note->name     = $user->firstname . " " . $user->lastname;
            $note->email    = $user->email;

            $note->save();
            $note->refresh();
//            $noteObj                   = new stdClass();
//            $noteObj->expsimplenote_id = $note->id;
//            $noteObj->content_id       = $order->id;
//            $noteObj->content_type     = 'order';
//            $db->insertObject($noteObj, 'content_expSimpleNote');
            $note->attachNote('order', $order->id);

            //eDebug($note,true);            
        } else {
            flash('error', gt('The email was NOT sent. An email address was not found for this customer'));
            expHistory::back();
        }

        flash('message', gt('Email sent.'));
        expHistory::back();
    }

    function ordersbyuser() {
        global $user;

        // if the user isn't logged in flash an error msg
        if (!$user->isLoggedIn()) expQueue::flashAndFlow('error', gt('You must be logged in to view past orders.'));

        expHistory::set('viewable', $this->params);
        $page = new expPaginator(array(
            'model'     => 'order',  //FIXME we should also be getting the order status name
            'where'     => 'purchased > 0 AND user_id=' . $user->id,
            'limit'     => 10,
            'order'     => 'purchased',
            'dir'       => 'DESC',
            'page'      => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=> $this->params['controller'],
            'action'    => $this->params['action'],
            'columns'   => array(
                gt('Date Purchased')=> 'purchased',
                gt('Invoice #')     => 'invoice_id',
            )
        ));
        assign_to_template(array(
            'page'=> $page
        ));

    }

    function metainfo() {
        global $router;

        if (empty($router->params['action'])) return false;

        // figure out what metadata to pass back based on the action 
        // we are in.
        $action   = $router->params['action'];
        $metainfo = array('title'=>'', 'keywords'=>'', 'description'=>'', 'canonical'=> '', 'noindex' => true, 'nofollow' => true);
        $storename = ecomconfig::getConfig('storename');
        switch ($action) {
            case 'myOrder':
            case 'show':
            case 'showByTitle':
                if (!empty($router->params['id'])) {
                    $order    = new order($router->params['id']);
                } elseif (!empty($router->params['invoice'])) {
                    $order = $this->order->find('first', 'invoice_id=' . $router->params['invoice']);
                } else {
                    $order    = $this->order;
                }
                $metainfo['title']       = gt('Viewing Order') . ' #' . $order->invoice_id . ' - ' . $storename;
                $metainfo['keywords']    = empty($order->meta_keywords) ? SITE_KEYWORDS : $order->meta_keywords;
                $metainfo['description'] = empty($order->meta_description) ? SITE_DESCRIPTION : $order->meta_description;
//                $metainfo['canonical'] = empty($order->canonical) ? $router->plainPath() : $order->canonical;
//                $metainfo['noindex'] = empty($order->meta_noindex) ? false : $order->meta_noindex;
//                $metainfo['nofollow'] = empty($order->meta_nofollow) ? false : $order->meta_nofollow;
                break;
            case 'showall':
            case 'ordersbyuser':
            default:
                $metainfo['title']       = gt("Order Management") . " - " . $storename;
                $metainfo['keywords']    = SITE_KEYWORDS;
                $metainfo['description'] = SITE_DESCRIPTION;
        }

        return $metainfo;
    }

    function captureAuthorization() {
        //eDebug($this->params,true);
        $order = new order($this->params['id']);
        /*eDebug($this->params); 
        //eDebug($order,true);*/
        //eDebug($order,true);
        //$billing = new billing();

        //eDebug($billing, true);
        //$billing->calculator = new $calcname($order->billingmethod[0]->billingcalculator_id);
        $calc         = $order->billingmethod[0]->billingcalculator->calculator;
        $calc->config = $order->billingmethod[0]->billingcalculator->config;

        //$calc = new $calc-
        //eDebug($calc,true);
        if (!method_exists($calc, 'delayed_capture')) {
            flash('error', gt('The Billing Calculator does not support delayed capture'));
            expHistory::back();
        }

        $result = $calc->delayed_capture($order->billingmethod[0], $this->params['capture_amt'], $order);

        if (empty($result->errorCode)) {
            flash('message', gt('The authorized payment was successfully captured'));
            expHistory::back();

        } else {
            flash('error', gt('An error was encountered while capturing the authorized payment.') . '<br /><br />' . $result->message);
            expHistory::back();
        }
    }

    function voidAuthorization() {
        $order   = new order($this->params['id']);
        $billing = $order->billingmethod[0];

        $calc         = $order->billingmethod[0]->billingcalculator->calculator;
        $calc->config = $order->billingmethod[0]->billingcalculator->config;

        if (!method_exists($calc, 'void_transaction')) {
            flash('error', gt('The Billing Calculator does not support void'));
            expHistory::back();
        }

        $result = $calc->void_transaction($order->billingmethod[0], $order);

        if (empty($result->errorCode)) {
            flash('message', gt('The transaction has been successfully voided'));
            expHistory::back();

        } else {
            flash('error', gt('An error was encountered while voiding the authorized payment.') . '<br /><br />' . $result->message);
            expHistory::back();
        }
    }

    function creditTransaction() {
        $order   = new order($this->params['id']);
        $billing = new billing($this->params['id']);
        //eDebug($this->params,true);
        $result = $billing->calculator->credit_transaction($billing->billingmethod, $this->params['capture_amt'],$order);

        if ($result->errorCode == '0') {
            flash('message', gt('The transaction has been credited'));
            expHistory::back();

        } else {
            flash('error', gt('An error was encountered while capturing the authorized payment.') . '<br /><br />' . $result->message);
            expHistory::back();
        }
    }

    function edit_payment_info() {
        //$order = new order($this->params['id']);
        $billing = new billing($this->params['id']);
        $opts    = expUnserialize($billing->billingmethod->billing_options);  //FIXME already unserialized???
        //eDebug($billing);
//        eDebug($opts);
        assign_to_template(array(
            'orderid'=> $this->params['id'],
            'opts'   => $opts,  //FIXME credit card doesn't have a result
            'billing_cost' => $billing->billingmethod->billing_cost,
            'transaction_state' => $billing->billingmethod->transaction_state
        ));
    }

    function save_payment_info() {
        //need to save billing methods and billing options
        //$order = new order($this->params['id']);
        //eDebug($this->params, true);
        $res_obj = new stdClass();
        foreach ($this->params['result'] as $resultKey=> $resultItem) {
            $res_obj->$resultKey = $resultItem;
        }
//        $res     = serialize($res_obj);
        $billing = new billing($this->params['id']);
        // eDebug($billing);
        $billingmethod      = $billing->billingmethod;
        $billingtransaction = $billingmethod->billingtransaction[0];

        // update billing method
        $billingmethod->billing_cost           = $this->params['billing_cost'];
        $billingmethod->transaction_state      = $this->params['transaction_state'];
        $bmopts                         = expUnserialize($billingmethod->billing_options);
        $bmopts->result                 = $res_obj;
        $billingmethod->billing_options = serialize($bmopts);
//        if (!empty($this->params['result']['payment_status']))
//            $billingmethod->transaction_state = $this->params['result']['payment_status'];  //FIXME should this be discrete??
        $billingmethod->save();

        // add new billing transaction
        $billingtransaction->billing_cost                = $this->params['billing_cost'];
        $billingtransaction->transaction_state           = $this->params['transaction_state'];
        $btopts                              = expUnserialize($billingtransaction->billing_options);
        $btopts->result                      = $res_obj;
        $billingtransaction->billing_options = serialize($btopts);
//        if (!empty($this->params['result']['payment_status']))
//            $billingtransaction->transaction_state = $this->params['result']['payment_status'];
        $billingtransaction->id = null;  // force a new record by erasing the id, easy method to copy record
//        $order = new order($this->params['id']);
//        $billingtransaction->billing_cost = $order->grand_total;  //FIXME should it always be the grand total???
        $billingtransaction->save();

//        flashAndFlow('message', gt('Payment info updated.'));
        flash('message', gt('Payment info updated.'));
        redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['id']));
    }

    function edit_shipping_method() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request.  Order invalid.'));
        $order = new order($this->params['id']);
        $s     = array_pop($order->shippingmethods);  //FIXME only getting 1st one and then removing it
        $sm    = new shippingmethod($s->id);
        //eDebug($sm);
        assign_to_template(array(
            'orderid' => $this->params['id'],
            'shipping'=> $sm
        ));
    }

    function save_shipping_method() {
        if (!isset($this->params['id']) || !isset($this->params['sid']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm               = new shippingmethod($this->params['sid']);
        $sm->option_title = $this->params['shipping_method_title'];
        $sm->carrier      = $this->params['shipping_method_carrier'];
        $sm->save();
        flashAndFlow('message', gt('Shipping method updated.'));
    }

    function edit_parcel() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();  // add calculator object
        assign_to_template(array(
            'shipping'=> $sm
        ));
    }

    function save_parcel() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        if (!isset($this->params['in_box']) || !isset($this->params['qty'])) {
            flashAndFlow('notice', gt('Nothing was included in the shipping package!'));
        } else {
            $sm = new shippingmethod($this->params['id']);
            $sm->attachCalculator();
//            $sm_new = clone($sm);  // prepare for another 'package' if needed since we didn't place everything in this one
//            $sm_new->id = null;
//            $sm_new->orderitem = array();
            //FIXME for now multiple shipping methods will crash ecom with shipping->__construct()
            foreach ($sm->orderitem as $key => $oi) {
                if (!array_key_exists($oi->id, $this->params['in_box'])) {
                    // one of the items by type is not in this package and needs to be moved to another package
//                    $tmp = 1;
//                    $sm_new->update();  // we don't need to actually create a new shippingmethod until needed
//                    $sm->orderitem[$key]->shippingmethods_id = $sm_new->id;
//                    $sm->orderitem[$key]->update();
//                    unset($sm->orderitem[$key]);
                    //NOTE $sm_new->update(); ???
                } else {
                    if ($oi->quantity != $this->params['qty'][$oi->id]) {
                        // one of the items by quantity is not in this package and remaining items need to be moved another package
//                        $tmp = 1;
//                        $new_quantity = $oi->quantity - $this->params['qty'][$oi->id];
//                        $sm->orderitem[$key]->quantity = $this->params['qty'][$oi->id];  // adjust to new quantity
//                        $sm->orderitem[$key]->update();
//                        $sm_new->update();  // we don't need to actually create a new shippingmethod until needed
//                        $oi->id = null;  // create a new orderitem copy
//                        $oi->shippingmethods_id = $sm_new->id;
//                        $oi->quantity = $new_quantity;
//                        $oi->update();
                        //NOTE $sm_new->update(); ???
                    }
                }
            }
            // update $sm with the passed $this->params (package data)
            $sm->update($this->params);  //NOTE will this update assoc orderitems???
            $msg = $sm->calculator->createLabel($sm);
            if (!is_string($msg)) {
                flashAndFlow('message', gt('Shipping package updated.'));
            } else {
                expHistory::back();
            }
        }
    }

    function edit_label() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $method = explode(':', $sm->option);
//FIXME check for existing rate, if not get next cheapest? (based on predefined package?)
        assign_to_template(array(
            'shipping'=> $sm,
            'cost' => $sm->shipping_options['shipment_rates'][$method[0]][$method[1]]['cost']
        ));
    }

    function save_label() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $msg = $sm->calculator->buyLabel($sm);
//        $sm->refresh();  //FIXME updated with new options we may need to take action on like tracking number?
//        $order->shipping_tracking_number = $sm->shipping_options['shipment_tracking_number'];
        if (!is_string($msg)) {
            flashAndFlow('message', gt('Shipping label purchased.'));
        } else {
            expHistory::back();
        }
    }

    function download_label() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $label = $sm->calculator->getLabel($sm);
        expHistory::back();
    }

    function delete_label() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $msg = $sm->calculator->cancelLabel($sm);
        // also need to cancel the pickup if created/purchased
        if (!is_string($msg) && ($sm->shipping_options['pickup_status'] == 'created' || $sm->shipping_options['pickup_status'] == 'purchased')) {
            $msg = $sm->calculator->cancelPickup($sm);
            if (!is_string($msg)) {
                flashAndFlow('message', gt('Shipping label and pickup cancelled and refunded.'));
            }
        } else {
            if (!is_string($msg)) {
                flashAndFlow('message', gt('Shipping label cancelled and refunded.'));
            }
        }
        expHistory::back();
    }

    function edit_pickup()
    {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        assign_to_template(array(
            'shipping'=> $sm,
        ));
    }

    function edit_pickup2() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $this->params['pickupdate'] = strtotime($this->params['pickupdate']);
        $this->params['pickupenddate'] = strtotime($this->params['pickupenddate']);
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $pickup = $sm->calculator->createPickup($sm, $this->params['pickupdate'], $this->params['pickupenddate'], $this->params['instructions']);
        $sm->refresh();
        $pickup_rates = array();
        foreach ($sm->shipping_options['pickup_rates'] as $pu_rate) {
            $pickup_rates[$pu_rate['id']] = $pu_rate['id'] . ' - ' . expCore::getCurrency($pu_rate['cost']);
        }
        assign_to_template(array(
            'shipping'=> $sm,
            'rates' => $pickup_rates
        ));
    }

    function save_pickup() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        //FIXME should we add the params to the $sm->shipping_options, or pass them??
        $msg = $sm->calculator->buyPickup($sm, $this->params['pickuprate']);
        if (!is_string($msg)) {
            flashAndFlow('message', gt('Package pickup ordered.'));
        } else {
            expHistory::back();
        }
    }

    function delete_pickup() {
        if (!isset($this->params['id']))
            flashAndFlow('error', gt('Unable to process request. Order invalid.'));
        $sm = new shippingmethod($this->params['id']);
        $sm->attachCalculator();
        $msg = $sm->calculator->cancelPickup($sm);
        if (!is_string($msg)) {
            flashAndFlow('message', gt('Package pickup cancelled.'));
        } else {
            expHistory::back();
        }
    }

    function createReferenceOrder() {
        if (!isset($this->params['id'])) {
            flashAndFlow('error', gt('Unable to process request. Invalid order number.'));
//            expHistory::back();
        }
        $order = new order($this->params['id']);
        assign_to_template(array(
            'order'=> $order
        ));
    }

    function save_reference_order() {
//        global $user;

        //eDebug($this->params,true);
        $order = new order($this->params['original_orderid']);
        //eDebug($order,true); 
        //x
        $newOrder                  = new order();
        $newOrder->order_status_id = $this->params['order_status_id'];
        $newOrder->order_type_id   = $this->params['order_type_id'];
        //$newOrder->order_references = $order->id;
        $newOrder->reference_id    = $order->id;
        $newOrder->user_id         = $order->user_id;
        $newOrder->purchased       = time();
        $newOrder->updated         = time();
        $newOrder->invoice_id      = $newOrder->getInvoiceNumber();
        $newOrder->orderitem       = array();
        $newOrder->subtotal        = $this->params['subtotal'];
        $newOrder->total_discounts = $this->params['total_discounts'];
        $newOrder->tax             = $this->params['tax'];
        $newOrder->shipping_total  = $this->params['shipping_total'];
        $newOrder->surcharge_total = $this->params['surcharge_total'];

        if ($this->params['autocalc'] == true) {
            $newOrder->grand_total = ($newOrder->subtotal - $newOrder->total_discounts) + $newOrder->tax + $newOrder->shipping_total + $newOrder->surcharge_total;
        } else {
            $newOrder->grand_total = round($this->params['grand_total'], 2);
        }
        $newOrder->save();
        $newOrder->refresh();

        // save initial order status
        $change = new order_status_changes();
//        $change->from_status_id = null;
        $change->to_status_id   = $newOrder->order_status_id;
        $change->orders_id      = $newOrder->id;
        $change->save();

        $tObj                             = new stdClass();
        $tObj->result                     = new stdClass();
        $tObj->result->errorCode          = 0;
        $tObj->result->message            = "Reference Order Pending";
        $tObj->result->PNREF              = "Pending";
        $tObj->result->authorization_code = "Pending";
        $tObj->result->AVSADDR            = "Pending";
        $tObj->result->AVSZIP             = "Pending";
        $tObj->result->CVV2MATCH          = "Pending";
        $tObj->result->traction_type      = "Pending";
        $tObj->result->payment_status     = "Pending";

        $newBillingMethod                       = $order->billingmethod[0];
        $newBillingMethod->id                   = null;
        $newBillingMethod->orders_id            = $newOrder->id;
//        $newBillingMethod->billingcalculator_id = 6;
        $newBillingMethod->billingcalculator_id = billingcalculator::getDefault();
        $newBillingMethod->billing_cost         = 0;
        $newBillingMethod->transaction_state    = 'authorization pending';
        $newBillingMethod->billing_options      = serialize($tObj);
        $newBillingMethod->save();

        //eDebug(expUnserialize($order->billingmethod[0]->billing_options));        
        //eDebug(expUnserialize($order->billingmethod[0]->billingtransaction[0]->billing_options),true); 

        $newBillingTransaction                       = new billingtransaction();
//        $newBillingTransaction->billingcalculator_id = 6; ///setting to manual/passthru
        $newBillingTransaction->billingcalculator_id = billingcalculator::getDefault();
        $newBillingTransaction->billing_cost         = 0;
        $newBillingTransaction->billingmethods_id    = $newBillingMethod->id;
        $newBillingTransaction->transaction_state    = 'authorization pending';

        $newBillingTransaction->billing_options = serialize($tObj);
        $newBillingTransaction->save();

        $sid                              = $order->orderitem[0]->shippingmethods_id;
        $newShippingMethod                = $order->shippingmethods[$sid];
        $newShippingMethod->id            = null;
        $newShippingMethod->shipping_cost = 0;
        $newShippingMethod->save();
        $newShippingMethod->refresh();

        foreach ($this->params['oi'] as $oikey=> $oi) {
            //eDebug($oikey);
            $newOi                          = new orderitem($oikey);
            $newOi->id                      = null;
            $newOi->quantity                = $this->params['quantity'][$oikey];
            $newOi->orders_id               = $newOrder->id;
            $newOi->products_name           = $this->params['products_name'][$oikey];
            $newOi->products_price          = $this->params['products_price'][$oikey];
            $newOi->products_price_adjusted = $this->params['products_price'][$oikey];
            //$newOi->products_tax = 0;        
            $newOi->shippingmethods_id = $newShippingMethod->id;
            $newOi->save();
        }

        $newOrder->shippingmethod = $newShippingMethod;
        $newOrder->billingmethod = $newBillingMethod;
        $newOrder->update();  //FIXME do we need to do this?

        flash('message', gt('Reference Order #') . $newOrder->invoice_id . " " . gt("created successfully."));
        redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $newOrder->id));
    }

    function create_new_order() {
//        $order = new order();
//        assign_to_template(array(
//            'order'=> $order
//        ));
    }

    function save_new_order() {
        //eDebug($this->params);
        /*addresses_id
        customer_type = 1 //new
        customer_type = 2 //existing Internal
        customer_type = 3 //existing external*/
//        global $user, $db;
        //eDebug($this->params,true);
        //$order = new order($this->params['original_orderid']);
        //eDebug($order,true); 

        $newAddy = new address();
        if ($this->params['customer_type'] == 1) {
            //blank order
            $newAddy->save(false);
        } else if ($this->params['customer_type'] == 2) {
            //internal customer
            $newAddy = new address($this->params['addresses_id']);
        } else if ($this->params['customer_type'] == 3) {
            //other customer
            $otherAddy = new external_address($this->params['addresses_id']);
            $newAddy->user_id      = $otherAddy->user_id;
            $newAddy->firstname    = $otherAddy->firstname;
            $newAddy->lastname     = $otherAddy->lastname;
            $newAddy->organization = $otherAddy->organization;
            $newAddy->address1     = $otherAddy->address1;
            $newAddy->address2     = $otherAddy->address2;
            $newAddy->city         = $otherAddy->city;
            $newAddy->state        = $otherAddy->state;
            $newAddy->zip          = $otherAddy->zip;
            $newAddy->phone        = $otherAddy->phone;
            $newAddy->email        = $otherAddy->email;
            $newAddy->save();
        }

        $newOrder                  = new order();
        $newOrder->order_status_id = $this->params['order_status_id'];
        $newOrder->order_type_id   = $this->params['order_type_id'];
        //$newOrder->order_references = $order->id;
        $newOrder->reference_id    = 0;
        $newOrder->user_id         = $newAddy->user_id;
        $newOrder->purchased       = time();
        $newOrder->updated         = time();
        $newOrder->invoice_id      = $newOrder->getInvoiceNumber();
        $newOrder->orderitem       = array();
        $newOrder->subtotal        = 0;
        $newOrder->total_discounts = 0;
        $newOrder->tax             = 0;
        $newOrder->shipping_total  = 0;
        $newOrder->surcharge_total = 0;
        $newOrder->grand_total     = 0;
        $newOrder->save();
        $newOrder->refresh();

        // save initial order status
        $change = new order_status_changes();
//        $change->from_status_id = null;
        $change->to_status_id   = $newOrder->order_status_id;
        $change->orders_id      = $newOrder->id;
        $change->save();

        $tObj                             = new stdClass();
        $tObj->result                     = new stdClass();
        $tObj->result->errorCode          = 0;
        $tObj->result->message            = "Reference Order Pending";
        $tObj->result->PNREF              = "Pending";
        $tObj->result->authorization_code = "Pending";
        $tObj->result->AVSADDR            = "Pending";
        $tObj->result->AVSZIP             = "Pending";
        $tObj->result->CVV2MATCH          = "Pending";
        $tObj->result->traction_type      = "Pending";
        $tObj->result->payment_status     = "Pending";

        $newBillingMethod                       = new billingmethod();
        $newBillingMethod->addresses_id         = $newAddy->id;
        $newBillingMethod->orders_id            = $newOrder->id;
//        $newBillingMethod->billingcalculator_id = 6;
        $newBillingMethod->billingcalculator_id = billingcalculator::getDefault();
        $newBillingMethod->billing_cost         = 0;
        $newBillingMethod->transaction_state    = 'authorization pending';
        $newBillingMethod->billing_options      = serialize($tObj);
        $newBillingMethod->firstname            = $newAddy->firstname;
        $newBillingMethod->lastname             = $newAddy->lastname;
        $newBillingMethod->organization         = $newAddy->organization;
        $newBillingMethod->address1             = $newAddy->address1;
        $newBillingMethod->address2             = $newAddy->address2;
        $newBillingMethod->city                 = $newAddy->city;
        $newBillingMethod->state                = $newAddy->state;
        $newBillingMethod->zip                  = $newAddy->zip;
        $newBillingMethod->phone                = $newAddy->phone;
        $newBillingMethod->email                = $newAddy->email;
        $newBillingMethod->save();

        //eDebug(expUnserialize($order->billingmethod[0]->billing_options));        
        //eDebug(expUnserialize($order->billingmethod[0]->billingtransaction[0]->billing_options),true); 

        $newBillingTransaction                       = new billingtransaction();
//        $newBillingTransaction->billingcalculator_id = 6; ///setting to manual/passthru
        $newBillingTransaction->billingcalculator_id = billingcalculator::getDefault();
        $newBillingTransaction->billing_cost         = 0;
        $newBillingTransaction->billingmethods_id    = $newBillingMethod->id;
        $newBillingTransaction->transaction_state    = 'authorization pending';
        $newBillingTransaction->billing_options      = serialize($tObj);
        $newBillingTransaction->save();

        $newShippingMethod                        = new shippingmethod();
        $newShippingMethod->shipping_cost         = 0;
//        $newShippingMethod->shippingcalculator_id = $db->selectValue('shippingcalculator', 'id', 'is_default=1');
        $newShippingMethod->shippingcalculator_id = shippingcalculator::getDefault();
        $newShippingMethod->addresses_id = $newAddy->id;
        $newShippingMethod->firstname    = $newAddy->firstname;
        $newShippingMethod->lastname     = $newAddy->lastname;
        $newShippingMethod->organization = $newAddy->organization;
        $newShippingMethod->address1     = $newAddy->address1;
        $newShippingMethod->address2     = $newAddy->address2;
        $newShippingMethod->city         = $newAddy->city;
        $newShippingMethod->state        = $newAddy->state;
        $newShippingMethod->zip          = $newAddy->zip;
        $newShippingMethod->phone        = $newAddy->phone;
        $newShippingMethod->email        = $newAddy->email;
        $newShippingMethod->save();
        $newShippingMethod->refresh();

        //FIXME add a fake item?
//        $oi                     = new orderitem();
//        $oi->orders_id          = $newOrder->id;
//        $oi->product_id         = 0;
//        $oi->product_type       = 'product';
//        $oi->products_name      = "N/A";
//        $oi->products_model     = "N/A";
//        $oi->products_price     = 0;
//        $oi->shippingmethods_id = $newShippingMethod->id;
//        $oi->save(false);

        $newOrder->shippingmethod = $newShippingMethod;
        $newOrder->billingmethod = $newBillingMethod;
        $newOrder->update();

        flash('message', gt('New Order #') . $newOrder->invoice_id . " " . gt("created successfully."));
        if (empty($this->params['no_redirect'])) {
            redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $newOrder->id));
        } else {
            return $newOrder->id;
        }
    }

    function edit_address() {
        //if $type = 'b' - business
        //if $type = 's' - shipping
        //addresses_id
        $order = new order($this->params['id']);
        $same  = false;

        $sm = array_pop($order->shippingmethods);  //FIXME only getting 1st one and then removing it
        //$bm = array_pop($order->billingmethods);

        //eDebug($sm->addresses_id);
        //eDebug($order->billingmethod[0]->addresses_id);
        if ($sm->addresses_id == $order->billingmethod[0]->addresses_id) {
            $same = true;
            // echo "Yes";
            //$addy = new address($sm->addresses_id);
        }

        if ($this->params['type'] == 'b') {
            $type = 'billing';
            $addy = new address($order->billingmethod[0]->addresses_id);
        } else if ($this->params['type'] == 's') {
            $type = 'shipping';
            $addy = new address($sm->addresses_id);
        }
        /* eDebug($this->params);
        eDebug($addy);
        eDebug($order,true);*/
        $billingmethod = new billingmethod($this->params['id']);
        //eDebug($billingmethod,true);
        //$opts = expUnserialize($billing->billingmethod->billing_options);
        //eDebug($billing);
        //eDebug($opts);
        assign_to_template(array(
            'orderid'=> $this->params['id'],
            'record' => $addy,
            'same'   => $same,
            'type'   => $type
        ));
    }

    function save_address() {
        global $db;

        $order          = new order($this->params['orderid']);
        $billing        = new billing($this->params['orderid']);
        $s              = array_pop($order->shippingmethods);  //FIXME only getting 1st one and then removing it
        $shippingmethod = new shippingmethod($s->id);

        //eDebug($order);
        //eDebug($this->params,true);
        //eDebug($shippingmethod);
        $billingmethod = $billing->billingmethod;
        /*
        eDebug($order);
        eDebug($shippingmethod);
        eDebug($billingmethod);*/

        if ($this->params['save_option'] == 0) {
            //update existing
            //echo "Update";
            $addy = new address($this->params['addyid']);
        } else if ($this->params['save_option'] == 1) {
            //create new
            //echo "New";
            $oldaddy       = new address($this->params['addyid']);
            $addy          = new address();
            $addy->user_id = $oldaddy->user_id;
        }

        //eDebug($addy,true);

        foreach ($this->params['address'] as $key=> $val) {
            if ($key == 'address_country_id') {
                $key = 'country';
            }
            if ($key == 'address_region_id') {
                $key = 'state';
            }
            $addy->$key = $val;
            if (isset($billingmethod->$key)) $billingmethod->$key = $val;
            if (isset($shippingmethod->$key)) $shippingmethod->$key = $val;
        }
        $addy->is_billing  = 0;
        $addy->is_shipping = 0;
        $addy->save();
        $addy->refresh();

        if ($this->params['type'] == 'billing' || ($this->params['same'] == true && $this->params['save_option'] == 0)) {
            //echo "Billing";
            $billingmethod->addresses_id = $addy->id;
            $billingmethod->save();
            $addy->is_billing = 1;
        }

        if ($this->params['type'] == 'shipping' || ($this->params['same'] == true && $this->params['save_option'] == 0)) {
            //eDebug("Shipping",true);
            $shippingmethod->addresses_id = $addy->id;
            $shippingmethod->save();
            $addy->is_shipping = 1;
        }

        $addy->save();
        if ($addy->is_default) $db->setUniqueFlag($addy, 'addresses', 'is_default', 'user_id=' . $addy->user_id);

        //eDebug($shippingmethod,true);
//        flashAndFlow('message', gt('Address updated.'));
        flash('message', gt('Address updated.'));
        redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['id']));
    }

    function edit_order_item() {
        $oi = new orderitem($this->params['id'], true, true);
        if (empty($oi->id)) {
            flash('error', gt('Order item doesn\'t exist.'));
            expHistory::back();
        }
        $oi->user_input_fields = expUnserialize($oi->user_input_fields);
        $params['options'] = $oi->opts;
        $params['user_input_fields'] = $oi->user_input_fields;
        $oi->product           = new product($oi->product->id, true, true);
        if ($oi->product->parent_id != 0) {
            $parProd = new product($oi->product->parent_id);
            //$oi->product->optiongroup = $parProd->optiongroup;   
            $oi->product = $parProd;
        }
        //FIXME we don't use selectedOpts?
//        $oi->selectedOpts = array();
//        if (!empty($oi->opts)) {
//            foreach ($oi->opts as $opt) {
//                $option = new option($opt[0]);
//                $og     = new optiongroup($option->optiongroup_id);
//                if (!isset($oi->selectedOpts[$og->id]) || !is_array($oi->selectedOpts[$og->id]))
//                    $oi->selectedOpts[$og->id] = array($option->id);
//                else
//                    array_push($oi->selectedOpts[$og->id], $option->id);
//            }
//        }
        //eDebug($oi->selectedOpts);

        assign_to_template(array(
            'oi' => $oi,
            'params' => $params
        ));
    }

    function delete_order_item() {
        $order = new order($this->params['orderid']);
        if (count($order->orderitem) <= 1) {
            flash('error', gt('You may not delete the only item on an order.  Please edit this item, or add another item before removing this one.'));
            expHistory::back();
        }

        $oi = new orderitem($this->params['id']);
        $oi->delete();

        $s  = array_pop($order->shippingmethods);  //FIXME only getting 1st one and then removing it
        $sm = new shippingmethod($s->id);

        $shippingCalc = new shippingcalculator($sm->shippingcalculator_id);
        $calcName     = $shippingCalc->calculator_name;
        $calculator   = new $calcName($shippingCalc->id);
        $pricelist = $calculator->getRates($order);

        foreach ($pricelist as $rate) {
            if ($rate['id'] == $sm->option) {
                $sm->shipping_cost = $rate['cost'];
            }
        }
        $sm->save();

        $order->refresh();
        $order->calculateGrandTotal();
        $order->save();

//        flashAndFlow('message', gt('Order item removed and order totals updated.'));
        flash('message', gt('Order item removed and order totals updated.'));
        if (empty($this->params['no_redirect'])) redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['orderid']));
    }

    function save_order_item() {
        if (!empty($this->params['id'])) {
            $oi = new orderitem($this->params['id']);
        } else {
            $oi = new orderitem($this->params);
        }
        //eDebug($this->params);

        /*eDebug($oi);
        eDebug(expUnserialize($oi->options));
        eDebug(expUnserialize($oi->user_input_fields),true);*/
        if (!empty($this->params['products_price'])) $oi->products_price = expUtil::currency_to_float($this->params['products_price']);
        $oi->quantity       = $this->params['quantity'];
        if (!empty($this->params['products_name'])) $oi->products_name  = $this->params['products_name'];

        if ($oi->product->parent_id != 0) {
            $oi->product = new product($oi->product->parent_id, true, false);
        } else {
            //reattach the product so we get the option fields and such
            $oi->product = new product($oi->product->id, true, false);
        }

        if (isset($this->params['product_status_id'])) {
            $ps = new product_status($this->params['product_status_id']);
            $oi->products_status = $ps->title;
        }

        $options = array();
        foreach ($oi->product->optiongroup as $og) {
            $isOptionEmpty = true;
            if (!empty($this->params['options'][$og->id])) {
                foreach ($this->params['options'][$og->id] as $opt) {
                    if (!empty($opt)) $isOptionEmpty = false;
                }
            }
            if (!$isOptionEmpty) {
                foreach ($this->params['options'][$og->id] as $opt_id) {
                    $selected_option = new option($opt_id);
                    $cost            = $selected_option->modtype == '$' ? $selected_option->amount : $this->getBasePrice() * ($selected_option->amount * .01);
                    $cost            = $selected_option->updown == '+' ? $cost : $cost * -1;
                    $options[]       = array($selected_option->id, $selected_option->title, $selected_option->modtype, $selected_option->updown, $selected_option->amount);
                }
            }
        }

        eDebug($this->params);
        //eDebug($oi,true);

        $user_input_info = array();
        //check user input fields
        //$this->user_input_fields = expUnserialize($this->user_input_fields);
        //eDebug($this,true);
        if (!empty($oi->product->user_input_fields)) foreach ($oi->product->user_input_fields as $uifkey=> $uif) {
            /*if ($uif['is_required'] || (!$uif['is_required'] && strlen($params['user_input_fields'][$uifkey]) > 0)) 
            {
                if (strlen($params['user_input_fields'][$uifkey]) < $uif['min_length'])
                {
                    //flash('error', 'test');    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));  
                    $params['error'] .= $uif['name'].' field has a minimum requirement of ' . $uif['min_length'] . ' characters.<br/>';
                    
                }else if (strlen($params['user_input_fields'][$uifkey]) > $uif['max_length'] && $uif['max_length'] > 0)
                {
                    //flash('error', );    
                    //redirect_to(array('controller'=>cart, 'action'=>'displayForm', 'form'=>'addToCart', 'product_id'=>$this->id, 'product_type'=>$this->product_type));      
                    $params['error'] .= $uif['name'].' field has a maximum requirement of ' . $uif['max_length'] . ' characters.<br/>';
                } 
            }*/
            $user_input_info[] = array($uif['name']=> $this->params['user_input_fields'][$uifkey]);
        }
        //eDebug($options);
        //eDebug($user_input_info,true);

        $oi->options           = serialize($options);
        $oi->user_input_fields = serialize($user_input_info);
        //eDebug($oi);        
        $oi->save();
        $oi->refresh();
        //eDebug($oi,true);

        $order = new order($oi->orders_id);
        $order->calculateGrandTotal();

        $s = array_pop($order->shippingmethods);  //FIXME only getting 1st one and thenremoving it
        eDebug($s);
        $sm = new shippingmethod($s->id);

        $shippingCalc = new shippingcalculator($sm->shippingcalculator_id);
        $calcName     = $shippingCalc->calculator_name;
        $calculator   = new $calcName($shippingCalc->id);
        $pricelist = $calculator->getRates($order);

        foreach ($pricelist as $rate) {
            if ($rate['id'] == $sm->option) {
                $sm->shipping_cost = $rate['cost'];
                break;
            }
        }
        $sm->save();
        $order->refresh();
        $order->calculateGrandTotal();
        //FIXME attempt to update w/ new billing transaction
//        $bmopts = expUnserialize($order->billingmethod[0]->billing_options);
//        $bmopts->result->transId = gt('Item edited in order');
//        $order->billingmethod[0]->update(array('billing_options' => serialize($bmopts), 'transaction_state' => $transaction_state));
//        $order->billingmethod[0]->billingcalculator->calculator->createBillingTransaction($order->billingmethod[0], number_format($order->grand_total, 2, '.', ''), $bmopts->result, $bmopts->result->payment_status);
        $order->save();

//        flashAndFlow('message', gt('Order item updated and order totals recalculated.'));
        flash('message', gt('Order item updated and order totals recalculated.'));
        if (empty($this->params['no_redirect'])) {
            redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['orderid']));
        } else {
            return $oi->id;
        }
    }

    function add_order_item() {
//        eDebug($this->params);
        $product     = new product($this->params['product_id']);
        $paramsArray = array('orderid'=> $this->params['orderid']);
        assign_to_template(array(
            'product'=> $product,
            'params' => $paramsArray
        ));
    }

    function save_new_order_item() {  //FIXME we need to be able to call this from program with $params also, addToOrder
        //eDebug($this->params,true);
        //check for multiple product adding
        $order = new order($this->params['orderid']);
        if (isset($this->params['prod-quantity'])) {
            //we are adding multiple children, so we approach a bit different
            //we'll send over the product_id of the parent, along with id's and quantities of children we're adding 
            foreach ($this->params['prod-quantity'] as $qkey=> &$quantity) {
                if (in_array($qkey, $this->params['prod-check'])) {
                    $this->params['children'][$qkey] = $quantity;
                }
                if (isset($child)) $this->params['product_id'] = $child->parent_id;
            }
        }

        $pt      = $this->params['product_type'];
        $product = new $pt($this->params['product_id'], true, true); //need true here?

        if ($product->addToCart($this->params, $this->params['orderid'])) {
            $order->refresh();
            $order->calculateGrandTotal();
            //FIXME attempt to update w/ new billing transaction
//            $bmopts = expUnserialize($order->billingmethod[0]->billing_options);
//            $bmopts->result->transId = gt('Item added to order');
//            $order->billingmethod[0]->billingcalculator->calculator->createBillingTransaction($order->billingmethod[0], number_format($order->grand_total, 2, '.', ''), $bmopts->result, $bmopts->result->payment_status);
            $order->save();
//            flashAndFlow('message', gt('Product added to order and order totals recalculated.'));
            flash('message', gt('Product added to order and order totals recalculated.'));
            redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['orderid']));
        }
        /*else
        {
            expHistory::back();
        }*/
    }

    function edit_invoice_id() {
        if (!isset($this->params['id'])) flashAndFlow('error', gt('Unable to process request.  Order invalid.'));
        $order = new order($this->params['id']);
        assign_to_template(array(
            'orderid'   => $this->params['id'],
            'invoice_id'=> $order->invoice_id
        ));
    }

    function save_invoice_id() {
        if (!isset($this->params['id'])) flashAndFlow('error', gt('Unable to process request.  Order invalid.'));
        if (empty($this->params['invoice_id']) || !is_numeric($this->params['invoice_id'])) flashAndFlow('error', gt('Unable to process request.  Invoice ID #.'));
        $order             = new order($this->params['id']);
        $order->invoice_id = $this->params['invoice_id'];
        $order->save(false);
        flashAndFlow('message', gt('Invoice # saved.'));
    }

    function edit_totals() {
        //eDebug($this->params);
        $order = new order($this->params['orderid']);
        assign_to_template(array(
//            'orderid'=>$this->params['id'],
            'order'=> $order
        ));
    }

    function save_totals() {
        //eDebug($this->params);
        //if(!is_numeric($this->params['subtotal']))
        $order                  = new order($this->params['orderid']);
        $order->subtotal        = expUtil::currency_to_float($this->params['subtotal']);
        $order->total_discounts = expUtil::currency_to_float($this->params['total_discounts']);
        $order->total           = round($order->subtotal - $order->total_discounts, 2);
        $order->tax             = expUtil::currency_to_float($this->params['tax']);
        $order->shipping_total  = expUtil::currency_to_float($this->params['shipping_total']);
        //note: the shippingmethod record will still reflect the ORIGINAL shipping amount for this order.
        $order->surcharge_total = expUtil::currency_to_float($this->params['surcharge_total']);

        if ($this->params['autocalc'] == true) {
            $order->grand_total = round(($order->subtotal - $order->total_discounts) + $order->tax + $order->shipping_total + $order->surcharge_total, 2);
        } else {
            $order->grand_total = round(expUtil::currency_to_float($this->params['grand_total']), 2);
        }
        //FIXME attempt to update w/ new billing transaction
//        $bmopts = expUnserialize($order->billingmethod[0]->billing_options);
//        $bmopts->result->transId = gt('Totals Adjusted');
//        $order->billingmethod[0]->billingcalculator->calculator->createBillingTransaction($order->billingmethod[0], number_format($order->grand_total, 2, '.', ''), $bmopts->result, $bmopts->result->payment_status);
        $order->save();

//        flashAndFlow('message', gt('Order totals updated.'));
        flash('message', gt('Order totals updated.'));
        redirect_to(array('controller'=> 'order', 'action'=> 'show', 'id'=> $this->params['orderid']));
    }

    function update_sales_reps() {
        if (!isset($this->params['id'])) {
            flashAndFlow('error', gt('Unable to process request. Invalid order number.'));
            //expHistory::back();
        }
        $order                 = new order($this->params['id']);
        $order->sales_rep_1_id = $this->params['sales_rep_1_id'];
        $order->sales_rep_2_id = $this->params['sales_rep_2_id'];
        $order->sales_rep_3_id = $this->params['sales_rep_3_id'];
        $order->save();
        flashAndFlow('message', gt('Sales reps updated.'));
    }

    function quickfinder() {
        global $db;

        $search    = $this->params['ordernum'];
        $searchInv = intval($search);

        $sql = "SELECT DISTINCT(o.id), o.invoice_id, FROM_UNIXTIME(o.purchased,'%c/%e/%y %h:%i:%s %p') as purchased_date, b.firstname as bfirst, b.lastname as blast, concat('".expCore::getCurrencySymbol()."',format(o.grand_total,2)) as grand_total, os.title as status_title, ot.title as order_type";
        $sql .= " from " . $db->prefix . "orders as o ";
        $sql .= "INNER JOIN " . $db->prefix . "orderitems as oi ON oi.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_type as ot ON ot.id = o.order_type_id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_status as os ON os.id = o.order_status_id ";
        $sql .= "INNER JOIN " . $db->prefix . "billingmethods as b ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "shippingmethods as s ON s.id = oi.shippingmethods_id ";

        $sqlwhere = "WHERE o.purchased != 0";
        if ($searchInv != 0) $sqlwhere .= " AND (o.invoice_id LIKE '%" . $searchInv . "%' OR";
        else $sqlwhere .= " AND (";
        $sqlwhere .= " b.firstname LIKE '%" . $search . "%'";
        $sqlwhere .= " OR s.firstname LIKE '%" . $search . "%'";
        $sqlwhere .= " OR b.lastname LIKE '%" . $search . "%'";
        $sqlwhere .= " OR s.lastname LIKE '%" . $search . "%'";
        $sqlwhere .= " OR b.email LIKE '%" . $search . "%')";

        $limit = empty($this->config['limit']) ? 350 : $this->config['limit'];
        //eDebug($sql . $sqlwhere)  ;
        $page = new expPaginator(array(
            'sql'       => $sql . $sqlwhere,
            'limit'     => $limit,
            'order'     => 'o.invoice_id',
            'dir'       => 'DESC',
            'page'      => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller'=> $this->baseclassname,
            'action'    => $this->params['action'],
            'columns'   => array(
                'actupon'           => true,
                gt('Order #')       => 'invoice_id|controller=order,action=show,showby=id',
                gt('Purchased Date')=> 'purchased_date',
                gt('First')         => 'bfirst',
                gt('Last')          => 'blast',
                gt('Total')         => 'grand_total',
                gt('Order Type')    => 'order_type',
                gt('Status')        => 'status_title'
            ),
        ));
        assign_to_template(array(
            'page'=> $page,
            'term'=> $search
        ));

        //eDebug($this->params);
        /*$o = new order();
        $b = new billingmethod();
        $s = new shippingmethod();
        
        $search = intval($this->params['ordernum']);
        if (is_int($oid) && $oid > 0)
        {
            $orders = $o->find('all',"invoice_id LIKE '%".$oid."%'");
            if(count($orders == 1))
            {
                redirect_to(array('controller'=>'order','action'=>'show','id'=>$order[0]->id));            
            }
            else
            {
                flashAndFlow('message',"Orders containing " . $search . " in the order number not found.");
            }
        }
        else
        {
            //lookup just a customer
            $bms = $b->find('all', )
        }*/
        /*$o = new order();
        $oid = intval($this->params['ordernum']);
        if (is_int($oid) && $oid > 0)
        {
            $order = $o->find('first','invoice_id='.$oid);
            if(!empty($order->id))
            {
                redirect_to(array('controller'=>'order','action'=>'show','id'=>$order->id));            
            }
            else
            {
                flashAndFlow('message',"Order #" . intval($this->params['ordernum']) . " not found.");
            }
        }
        else
        {
            flashAndFlow('message','Invalid order number.');        
        }*/
    }

    public function verifyReturnShopper() {
//        global $user, $order;

        $sessAr = expSession::get('verify_shopper');
        if (isset($sessAr)) {
            assign_to_template(array(
                'firstname'=> $sessAr['firstname'],
                'cid'=> $sessAr['cid']

            ));
            /*eDebug(expSession::get('verify_shopper'));
            eDebug($this->params);
            eDebug("here");
            eDebug($user);
            eDebug($order);*/
        }
    }

    public function verifyAndRestoreCart() {
//        global $user, $order;

        $sessAr = expSession::get('verify_shopper');
        if (isset($sessAr) && isset($this->params['cid']) && $this->params['cid'] == $sessAr['cid']) {
            $tmpCart = new order($sessAr['cid']);
            if (isset($tmpCart->id)) {
                //eDebug($tmpCart,true); 
                $shippingMethod = $tmpCart->shippingmethod;
                $billingMethod  = $tmpCart->billingmethod[0];

                if (($this->params['lastname'] == $shippingMethod->lastname || $this->params['lastname'] == $billingMethod->lastname) &&
                    ($this->params['email'] == $shippingMethod->email || $this->params['email'] == $billingMethod->email) &&
                    ($this->params['zip_code'] == $shippingMethod->zip || $this->params['zip_code'] == $billingMethod->zip)
                ) {
                    //validatio succeed, so restore order, login user and continue on to orig_path
                    //eDebug("Validated",true);
                    $sessAr['validated'] = true;
                    expSession::set('verify_shopper', $sessAr);
                    redirect_to($sessAr['orig_path']);
                } else {
                    //eDebug("Validated NOT",true);
                    //validation failed, so go back
                    flash('error', gt("We're sorry, but we could not verify your information.  Please try again, or start a new shopping cart."));
                    redirect_to(array('controller'=> 'order', 'action'=> 'verifyReturnShopper', 'id'=> $sessAr['cid']));
                }
            } else {
                flash('error', gt('We were unable to restore the previous order, we apologize for the inconvenience.  Please start a new shopping cart.'));
                $this->clearCart();
            }
        }
    }

    public static function clearCartCookie() {
        expSession::un_set('verify_shopper');
        order::setCartCookie(null);
    }

    public function clearCart() {
        global $order;

        $sessAr = expSession::get('verify_shopper');
        if (isset($sessAr)) {
            order::setCartCookie($order);
            $orig_path = $sessAr['orig_path'];
            expSession::un_set('verify_shopper');
            redirect_to($orig_path);
        } else {
            expHistory::back();
        }
    }

    /**
     * AJAX search for internal (addressController) addresses
     *
     */
    public function search() {
//        global $db, $user;
        global $db;

        $sql = "select DISTINCT(a.id) as id, a.firstname as firstname, a.middlename as middlename, a.lastname as lastname, a.organization as organization, a.email as email ";
        $sql .= "from " . $db->prefix . "addresses as a "; //R JOIN " . 
        //$db->prefix . "billingmethods as bm ON bm.addresses_id=a.id ";
        $sql .= " WHERE match (a.firstname,a.lastname,a.email,a.organization) against ('" . $this->params['query'] .
            "*' IN BOOLEAN MODE) ";
        $sql .= "order by match (a.firstname,a.lastname,a.email,a.organization)  against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) ASC LIMIT 12";
        $res = $db->selectObjectsBySql($sql);
        foreach ($res as $key=>$record) {
            $res[$key]->title = $record->firstname . ' ' . $record->lastname;
        }
        //eDebug($sql);
        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }

    /**
     * Ajax search for external addresses
     *
     */
    public function search_external() {
//        global $db, $user;
        global $db;

        $sql = "select DISTINCT(a.id) as id, a.source as source, a.firstname as firstname, a.middlename as middlename, a.lastname as lastname, a.organization as organization, a.email as email ";
        $sql .= "from " . $db->prefix . "external_addresses as a "; //R JOIN " . 
        //$db->prefix . "billingmethods as bm ON bm.addresses_id=a.id ";
        $sql .= " WHERE match (a.firstname,a.lastname,a.email,a.organization) against ('" . $this->params['query'] .
            "*' IN BOOLEAN MODE) ";
        $sql .= "order by match (a.firstname,a.lastname,a.email,a.organization)  against ('" . $this->params['query'] . "*' IN BOOLEAN MODE) ASC LIMIT 12";
        $res = $db->selectObjectsBySql($sql);
        foreach ($res as $key=>$record) {
            $res[$key]->title = $record->firstname . ' ' . $record->lastname;
        }
        //eDebug($sql);
        $ar = new expAjaxReply(200, gt('Here\'s the items you wanted'), $res);
        $ar->send();
    }

}

?>