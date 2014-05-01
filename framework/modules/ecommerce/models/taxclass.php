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

/**
 * @subpackage Models
 * @package Core
 */
class taxclass extends expRecord {
    public $table = 'tax_class';
        
    public function __construct($params=null, $get_assoc=false, $get_attached=false) {
        parent::__construct($params, $get_assoc, $get_attached);
    }
    
    public function getProductTax($item) {
        global $db;
        if (empty($item->shippingmethod->state)) return false;
        
        // find any zones that match the state we are shipping this item to.
        $zones = $db->selectColumn('tax_geo', 'zone_id', 'region_id='.$item->shippingmethod->state);
        if (empty($zones)) return false;
        
        $rate = $db->selectValue('tax_rate', 'rate', 'zone_id IN ('.implode(',', $zones).') AND class_id='.$item->product->tax_class_id);
        //$item->products_tax = round(($rate * .01) * $item->products_price_adjusted,2); // * $item->quantity ;
        return round(($rate * .01) * $item->products_price_adjusted,2); // * $item->quantity ;
        //$item->save();
        
        //return $item->products_tax;
    }
    
    public static function getCartTaxZones($order) {
        global $db;
        
        $zones = array();
        foreach ($order->orderitem as $item) {
            $sql  = "SELECT tz.name, tr.rate FROM ".DB_TABLE_PREFIX."_tax_geo as tg JOIN ".DB_TABLE_PREFIX."_tax_zone as tz ON tg.zone_id=tz.id ";
            $sql .= "JOIN ".DB_TABLE_PREFIX."_tax_rate as tr ON tr.zone_id=tg.zone_id  where tg.region_id=".$item->shippingmethod->state;
            
            $zone = $db->selectObjectBySql($sql);
            if (!empty($zone)) $zones[$zone->name] = $zone;            
        }
        
        return $zones;
    }
}

?>