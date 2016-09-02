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
 * @subpackage Models
 * @package Modules
 */
class taxclass extends expRecord {
    public $table = 'tax_class';
        
    public function __construct($params=null, $get_assoc=false, $get_attached=false) {  // change param default values
        parent::__construct($params, $get_assoc, $get_attached);
    }
    
    public static function getProductTax($item) {
        global $db;

        // do we have the info we need to get taxes?
        if (empty($item->shippingmethod->country) && empty($item->shippingmethod->state))
            return false;
        if (!$db->countObjects('tax_rate'))
            return false;
        $global_config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        if (empty($global_config->config['store']['country']))
            flashAndFlow('error', gt('This store is not yet fully configured with a store address.')."<br>".gt('You Must Enter a Store Address').' <a href="'.expCore::makeLink(array('controller'=>'ecomconfig','action'=>'configure')).'">'.gt('Here').'</a>');

        // find any zones that match the state we are shipping this item to.
        $my_zone = $db->selectValue('tax_geo', 'zone_id', 'country_id='.intval($global_config->config['store']['country']).' AND region_id='.intval($global_config->config['store']['state']));
        $zones = $db->selectColumn('tax_geo', 'zone_id', 'country_id='.intval($item->shippingmethod->country).' AND region_id='.intval($item->shippingmethod->state));
        if (empty($my_zone) && empty($zones)) return false;
        
        // first locate any local origin tax
        $rate = $db->selectValue('tax_rate', 'rate', 'zone_id='.$my_zone.' AND origin_tax=1 AND inactive!=1 AND class_id='.$item->product->tax_class_id);
        if (empty($rate))
            $rate = $db->selectValue('tax_rate', 'rate', 'zone_id IN ('.implode(',', $zones).') AND inactive!=1 AND class_id='.$item->product->tax_class_id);
        //$item->products_tax = round(($rate * .01) * $item->products_price_adjusted,2); // * $item->quantity ;
        return round(($rate * .01) * $item->products_price_adjusted, 2); // * $item->quantity ;
        //$item->save();
        
        //return $item->products_tax;
    }
    
    public static function getCartTaxZones($order) {
        global $db;

        $global_config = new expConfig(expCore::makeLocation("ecomconfig","@globalstoresettings",""));
        if ($db->countObjects('tax_rate') && empty($global_config->config['store']['country']))
            flashAndFlow('error', gt('This store is not yet fully configured with a store address.')."<br>".gt('You Must Enter a Store Address').' <a href="'.expCore::makeLink(array('controller'=>'ecomconfig','action'=>'configure')).'">'.gt('Here').'</a>');

        $zones = array();
        foreach ($order->orderitem as $item) {
            //FIXME we need to ensure any applicable origin tax is at the top of the list
            $sql  = "SELECT tz.name, tr.rate, tr.shipping_taxed, tr.origin_tax FROM ".$db->prefix."tax_geo as tg ";
            $sql .= "JOIN ".$db->prefix."tax_zone as tz ON tg.zone_id=tz.id ";
            $sql .= "JOIN ".$db->prefix."tax_rate as tr ON tr.zone_id=tg.zone_id ";
            $sql .= "WHERE tr.class_id=".$item->product->tax_class_id;
            if (!empty($global_config->config['store']))
                $sql .= " AND (tg.country_id=".intval($global_config->config['store']['country']) . " AND tg.region_id=".intval($global_config->config['store']['state']);
            $sql .= " AND tr.origin_tax=1 AND inactive!=1) OR (tg.country_id=".intval($item->shippingmethod->country)." AND tg.region_id=".intval($item->shippingmethod->state)." AND inactive!=1) ";
            $sql .= "ORDER BY origin_tax DESC";
            $zone = $db->selectObjectBySql($sql);
            if (!empty($zone)) $zones[$zone->name] = $zone;            
        }
        
        return $zones;
    }
    
}

?>