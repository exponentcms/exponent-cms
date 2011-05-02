<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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

class reportController extends expController {
	//protected $basemodel_name = '';
	//public $useractions = array('showall'=>'Show all');
	protected $add_permissions = array('build_report'=>'Manage','dashboard'=>'View the Ecommerce Dashboard');
	
	function name() { return $this->displayname(); } //for backwards compat with old modules
	function displayname() { return "Ecom Report Builder"; }
	function description() { return "Build reports based on store activity"; }
	function author() { return "Phillip Ball - OIC Group, Inc"; }
	function hasSources() { return false; }
	function hasViews() { return true; }
	function hasContent() { return true; }
	function supportsWorkflow() { return false; }
	
	function dashboard (){
        // stub function.
    }
    
	function order_report (){
        // stub function. I'm sure eventually we can pull up exising reports to pre-populate our form.
        $os = new order_status();
        $oss = $os->find('all');        
        $order_status =  array();
        $order_status[-1] = '';
        foreach ($oss as $okey=>$status)
        {
            $order_status[$okey] = $status->title;
        }
        
        $ot = new order_type();
        $ots = $ot->find('all');        
        $order_type =  array();
        $order_type[-1] = '';
        foreach ($ots as $otkey=>$orderType)
        {
            $order_type[$otkey] = $orderType->title;
        }
        
        $dis = new discounts();
        $diss = $dis->find('all');        
        $discounts =  array();
        $discounts[-1] = '';
        foreach ($diss as $dkey=>$discount)
        {
            $discounts[$dkey] = $discount->coupon_code;
        }
        
        /*$geo = new geoRegion();
        $geos = $geo->find('all');        
        $states = array();
        $states[-1] = '';
        foreach ($geos as $skey=>$state)
        {
            $states[$skey] = $state->name;
        } */
        
        $payment_methods = array('-1'=>'', 'V'=>'Visa','MC'=>'Mastercard','D'=>'Discover','AMEX'=>'American Express','PP'=>'PayPal','GC'=>'Google Checkout','Other'=>'Other');
        
       // eDebug($order_status);
        assign_to_template(array('order_status'=>$order_status));  
        assign_to_template(array('discounts'=>$discounts)); 
        //assign_to_template(array('states'=>$states));   
        assign_to_template(array('order_type'=>$order_type));
        assign_to_template(array('payment_methods'=>$payment_methods));    
    }
    
	function generateOrderReport (){
        global $db;
        eDebug($this->params);
        $p = $this->params;   
        
        //eDebug();
        //build 
        $sql = "SELECT DISTINCT(o.id), o.purchased, b.firstname, b.lastname, o.grand_total, os.title from ";
        $sql .= $db->prefix . "orders as o ";
        $sql .= "INNER JOIN " . $db->prefix . "orderitems as oi ON oi.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "product as p ON oi.product_id = p.id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_type as ot ON o.order_type_id = ot.id ";
        $sql .= "INNER JOIN " . $db->prefix . "order_status as os ON os.id = o.order_status_id ";
        $sql .= "INNER JOIN " . $db->prefix . "billingmethods as b ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "shippingmethods as s ON b.orders_id = o.id ";
        $sql .= "INNER JOIN " . $db->prefix . "geo_region as gr ON (gr.id = b.state OR gr.id = s.state) ";
        $sql .= "LEFT JOIN " . $db->prefix . "order_discounts as od ON od.orders_id = o.id ";
        
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
        foreach ($p['order_status'] as $os)
        {
            if ($os == -1) continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqlwhere .= " AND (o.order_status_id = " . $os;
            }else
            {
                $sqlwhere .= " OR o.order_status_id = " . $os;
            }
        }
        $sqlwhere .= ")";
        
        $inc = 0;
        foreach ($p['order_type'] as $ot)
        {
            if ($ot == -1) continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqlwhere .= " AND (o.order_type_id = " . $ot;
            }else
            {
                $sqlwhere .= " OR o.order_type_id = " . $ot;
            }
        }
        $sqlwhere .= ")";
        
        if (!empty($p['order-range-num']))
        {
            $operator = '';
            switch($p['order-range-op'])
            {
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
        
        if (!empty($p['order-price-num']))
        {
            $operator = '';
            switch($p['order-price-op'])
            {
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
            $sqlwhere .= " AND o.grand_total" . $operator . $p['order-price-num'];   
        }
        
        if (!empty($p['pnam']))
        {
            $sqlwhere .= " AND p.title LIKE '%" . $p['pnam'] . "%'";       
        }
        
        if (!empty($p['sku']))
        {
            $sqlwhere .= " AND p.model LIKE '%" . $p['sku'] . "%'";       
        }
        
        $inc = 0;
        foreach ($p['discounts'] as $d)
        {
            if ($d == -1) continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqlwhere .= " AND (od.discount_id = " . $d;
            }else
            {
                $sqlwhere .= " OR od.discount_id = " . $d;
            }
        }
        $sqlwhere .= ")";
        
        if (!empty($p['blshpname']))
        {
            $sqlwhere .= " AND (b.firstname LIKE '%" . $p['blshpname'] . "%'";       
            $sqlwhere .= " OR s.firstname LIKE '%" . $p['blshpname'] . "%'";       
            $sqlwhere .= " OR b.lastname LIKE '%" . $p['blshpname'] . "%'";       
            $sqlwhere .= " OR s.lastname LIKE '%" . $p['blshpname'] . "%')";       
        }
        
        if (!empty($p['email']))
        {
            $sqlwhere .= " AND (b.email LIKE '%" . $p['email'] . "%'";       
            $sqlwhere .= " OR s.email LIKE '%" . $p['email'] . "%')";       
        }
        
        if (!empty($p['zip']))
        {
            if ($p['bl-sp-zip'] == 'b') $sqlwhere .= " AND b.zip LIKE '%" . $p['zip'] . "%'";       
            else if ($p['bl-sp-zip'] == 's') $sqlwhere .= " AND s.zip LIKE '%" . $p['zip'] . "%'";       
        }
        
         
        if (isset($p['state'] )) 
        {
            $inc = 0;            
            foreach ($p['state'] as $s)
            {
                if ($s == -1) continue;
                else if ($inc == 0)
                {
                    $inc++;
                    if ($p['bl-sp-state'] == 'b') $sqlwhere .= " AND (b.state = " . $s;
                    else if ($p['bl-sp-state'] == 's') $sqlwhere .= " AND (s.state = " . $s;
                }else
                {
                    if ($p['bl-sp-state'] == 'b') $sqlwhere .= " OR b.state = " . $s;
                    else if ($p['bl-sp-state'] == 's') $sqlwhere .= " OR s.state = " . $s;
                }
            }
            $sqlwhere .= ")";
        }
        
        if (isset($p['payment_method'] )) 
        {
            $inc = 0;
            foreach ($p['payment_method'] as $s)
            {
                if ($s == -1) continue;
                else if ($inc == 0)
                {
                    $inc++;
                    $sqlwhere .= " AND (o.order_status_id = " . $s;
                }else
                {
                    $sqlwhere .= " OR o.order_status_id = " . $s;
                }
            }
            $sqlwhere .= ")";
        }
        
        echo $sql . $sqlwhere . "<br>";
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
        //$where = 1;//$this->aggregateWhereClause();
        $order = 'id';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        //$prod = new product();
       // $order = new order();
        //$items = $prod->find('all', 1, 'id DESC',25);  
        $items = $order->find('all', 1, 'id DESC',25);  
        //$res = $mod->find('all',$sql,'id',25);
        
        //eDebug($items);
        
        $page = new expPaginator(array(
            //'model'=>'product',
            'records'=>$items,
            // 'where'=>$where,
            'sql'=>$sql . $sqlwhere, 
            'limit'=>$limit,
            'order'=>$order,
            'controller'=>$this->baseclassname,
            'action'=>$this->params['action'],
            'columns'=>array('actupon'=>true,'Title'=>'title|controller=store,action=show,showby=id','SKU'=>'model'),
            ));
                    
        assign_to_template(array('page'=>$page));
        // 
        //     
        // assign_to_template(array('page'=>$page)); 
    }
    
    function generateProductReport (){
        global $db;
        //eDebug($this->params);
        $p = $this->params;   
        $sqlids = "SELECT DISTINCT(p.id) from ";
        $sqlcount = "SELECT COUNT(DISTINCT(p.id)) from ";
        $sqlstart = "SELECT DISTINCT(p.id), p.title, p.model, p.base_price, ps.title as status from ";
        $sql = $db->prefix . "product as p ";
        $sql .= "INNER JOIN " . $db->prefix . "product_status as ps ON p.product_status_id = ps.id ";
        $sql .= "INNER JOIN " . $db->prefix . "product_storeCategories as psc ON p.id = psc.product_id ";
        
        $sqlwhere = 'WHERE 1=1 ';
                                             
        $inc = 0; $sqltmp = '';
        foreach ($p['product_status'] as $os)
        {
            if ($os == '') continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqltmp .= " AND (p.product_status_id = " . $os;
            }else
            {
                $sqltmp .= " OR p.product_status_id = " . $os;
            }
            
        } 
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp  .= ")"; 
        
        $inc = 0; $sqltmp = '';
        foreach ($p['product_type'] as $ot)
        {
            if ($ot == '') continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqltmp .= " AND (p.product_type = '" . $ot . "'";
            }else
            {
                $sqltmp .= " OR p.product_type = '" . $ot . "'";
            }
            
        }
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp  .= ")"; 
        
        $inc = 0; $sqltmp = '';
        foreach ($p['storeCategory'] as $ot)
        {
            if ($ot == '') continue;
            else if ($inc == 0)
            {
                $inc++;
                $sqltmp .= " AND (psc.storecategories_id = " . $ot;
            }else
            {
                $sqltmp .= " OR psc.storecategories_id = " . $ot;
            }
              
        }
        if (!empty($sqltmp)) $sqlwhere .= $sqltmp .= ")";
                
        if (!empty($p['product-range-num']))
        {
            $operator = '';
            switch($p['product-range-op'])
            {
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
        
        if (!empty($p['product-price-num']))
        {
            $operator = '';
            switch($p['product-price-op'])
            {
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
            $sqlwhere .= " AND p.base_price" . $operator . $p['product-price-num'];   
        }
        
        if (!empty($p['pnam']))
        {
            $sqlwhere .= " AND p.title LIKE '%" . $p['pnam'] . "%'";       
        }
        
        if (!empty($p['sku']))
        {
            $sqlwhere .= " AND p.model LIKE '%" . $p['sku'] . "%'";       
        }
        
        eDebug( $sqlstart . $sql . $sqlwhere );
        eDebug ($sqlcount . $sql . $sqlwhere );
        exponent_sessions_set('product_export_query', $sqlids . $sql . $sqlwhere);
        
        $order = 'id';
        $limit = empty($this->config['limit']) ? 10 : $this->config['limit'];
        $product = new product();
        $items = $product->find('all', '', 'id', 25);     
        //$page = new expPaginator();   
        //eDebug($page,true);   
        $page = new expPaginator(array(
            'model'=>'product',
            //'records'=>$items,
            // 'where'=>$where,
            'sql'=>$sqlstart . $sql . $sqlwhere, 
            'count_sql'=>$sqlcount . $sql . $sqlwhere, 
            'limit'=>$limit,
            'order'=>$order,
            'controller'=>'store',
            'action'=>$this->params['action'],
            'columns'=>array('actupon'=>true,'ID'=>'id','Product'=>'title|controller=store,action=show,showby=id','SKU'=>'model','Price'=>'base_price'),
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
            'columns'=>array(
                'Customer'=>'lastname',
                'Invoice #'=>'invoice_id', 
                'Total'=>'total',
                'Date Purchased'=>'purchased',
                'Status'=>'order_status_id',
                )
            ));            */
            $action_items = array('batch_export'=>'Export to CSV');
        assign_to_template(array('page'=>$page, 'action_items'=>$action_items));
        // 
        //     
        // assign_to_template(array('page'=>$page)); 
    }
    
    private function parseAndTrim($str)
    {   //“Death from above”? ®
        //echo "1<br>"; eDebug($str);    
        $str = str_replace("’","&rsquo;",$str);
        $str = str_replace("‘","&lsquo;",$str);
        $str = str_replace("®","&#174;",$str);
        $str = str_replace("–","-", $str);
        $str = str_replace("—","&#151;", $str); 
        $str = str_replace("”", "&rdquo;", $str);
        $str = str_replace("“", "&ldquo;", $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace(",","\,",$str); 
        $str = str_replace('\"',"&quot;",$str);
        $str = str_replace('"',"&quot;",$str);
        $str = str_replace("¼","&#188;",$str);
        $str = str_replace("½","&#189;",$str);
        $str = str_replace("¾","&#190;",$str);
        $str = mysql_escape_string(trim(str_replace("™", "&trade;", $str))); 
        //echo "2<br>"; eDebug($str,die);
        return $str;
    }
    
    function batch_export()
    {
        global $db;
        //eDebug($this->params);
        //$sql = "SELECT * INTO OUTFILE '" . BASE . "tmp/export.csv' FIELDS TERMINATED BY ','  FROM exponent_product WHERE 1 LIMIT 10";
        $out = '"id", "parent_id", "child_rank", "title", "model", "warehouse_location", "sef_url", "meta_title", "meta_keywords", "meta_description", "base_price", "special_price", "use_special_price", "active_type", "product_status_id", "category1", "category2", "category3", "category4", "surcharge"' . chr(13) . chr(10); 
        if (isset($this->params['applytoall']) && $this->params['applytoall']==1)
        {
            $sql = exponent_sessions_get('product_export_query');
            //exponent_sessions_set('product_export_query','');
            $prods = $db->selectArraysBySql($sql);
            //eDebug($prods);
        }else{
            foreach ($this->params['act-upon'] as $prod)
            {
                $prods[] = array('id'=>$prod);
            }
        }
        foreach ($prods as $pid)
        {
            $p = new product($pid['id'], true, false);
            //eDebug($p,true);
            $out.= '"' . $p->id . '",';
            $out.= '"' . $p->parent_id . '",';
            $out.= '"' . $p->child_rank . '",';
            $out.= '"' . $this->parseAndTrim($p->title) . '",';
            $out.= '"' . $this->parseAndTrim($p->model) . '",';
            $out.= '"' . $this->parseAndTrim($p->warehouse_location) . '",';
            $out.= '"' . $this->parseAndTrim($p->sef_url) . '",';
            $out.= '"' . $this->parseAndTrim($p->meta_title) . '",';
            $out.= '"' . $this->parseAndTrim($p->meta_keywords) . '",';
            $out.= '"' . $this->parseAndTrim($p->meta_description) . '",';
            $out.= '"' . $p->base_price . '",';
            $out.= '"' . $p->special_price . '",';
            $out.= '"' . $p->use_special_price . '",';
            $out.= '"' . $p->active_type . '",';
            $out.= '"' . $p->product_status_id . '",';
            
            for ($x=0; $x<4; $x++)
            {
                $this->catstring = '';
                if (isset($p->storeCategory[$x])) 
                {                    
                    $out.= '"' . $this->buildCategoryString($p->storeCategory[$x]->id, true) . '",';   
                }
                else $out.= ',';
            }
            $out.= '"' . $p->surcharge. '"' . chr(13) . chr(10); 
                       
        }
        //echo($out);
        $outFile = 'tmp/product_export_' . time() . '.csv';       
        $outHandle = fopen(BASE . $outFile, 'w');
        fwrite($outHandle, $out);        
        fclose($outHandle);
        
        echo "<br/><br/>Download the file here: <a href='" . PATH_RELATIVE . $outFile ."'>Product Export</a>";
        
        /*eDebug(BASE . "tmp/export.csv");
        $db->sql($sql);
        eDebug($db->error());*/
        /*OPTIONALLY ENCLOSED BY '" . '"' . 
        "' ESCAPED BY '\\'
        LINES TERMINATED BY '" . '\\n' .
        "' */
    }
    
    //public $catstring = '';
    
    function buildCategoryString($catID, $reset=false)
    {        
        static $cstr = '';
        if ($reset) $cstr = '';
        if(strlen($cstr) > 0 ) $cstr.="::";
        $cat = new storeCategory($catID);      
        //eDebug($cat);
        if(!empty($cat->parent_id)) $this->buildCategoryString($cat->parent_id);
        $cstr .= $cat->title . "::";
        return substr($cstr, 0, -2);   
    }
    
    function product_report(){
                
        $pts = storeController::getProductTypes();
        $newPts = array();
        foreach ($pts as $pt)
        {
            $newPts[$pt] = $pt;
        }
       
        assign_to_template(array('product_types'=>$newPts));
    }
    
}

?>
