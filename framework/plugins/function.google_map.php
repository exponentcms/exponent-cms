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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {google_map} function plugin
 *
 * Type:     function<br>
 * Name:     google_map<br>
 * Purpose:  embed a google map for passed address id, object, or string
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_google_map($params,&$smarty) {
    global $db;

    if (empty($params['unique'])) die("<strong style='color:red'>".gt("The 'unique' parameter is required for the {google_map} plugin.")."</strong>");

    $address_string = '';
    if (is_numeric($params['address']) || is_object($params['address'])) {
        $addyid = is_numeric($params['address']) ? $params['address'] : $params['address']->id;
        $address = new address($addyid);
        $address_string = $address->address1;
        if (!empty($address->address2)) $address_string .= ' ' . $address->address2;
        $address_string .= ',' . $address->zip . ',' . $address->city . ',';
        if ($address->state == -2) {
            $address_string .= $address->non_us_state;
        } else {
            $state_name = $db->selectValue('geo_region', 'name', 'id='.intval($address->state));
            $address_string .= $state_name;
        }
        if ($address->state == -2 || empty($address->state)) {
            $country_name = $db->selectValue('geo_country', 'name', 'id='.intval($address->country));
            $address_string .= ',' . $country_name;
        }
    } else if (is_string($params['address'])) {
        $address_string = $params['address'];
    }

    $height = !empty($params['height']) ? $params['height'] : '190';
    $html = '<div id="gmap-' . $params['unique'] .'" class="" style="height:' . $height . 'px"></div>';
    $script = "
    if (typeof google !== 'undefined') {
        var geocoder = new google.maps.Geocoder();
        var " . $params['unique'] . "_map;

        $(document).ready(function()
        {
            geocoder.geocode({
                address: '" . $address_string . "'
                }, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK)
                {
                    " . $params['unique'] . "_map = new google.maps.Map(document.getElementById('gmap-" . $params['unique'] . "'), {
                        zoom: 10,
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        center: results[0].geometry.location
                    });
                    var " . $params['unique'] . "_marker = new google.maps.Marker({
                        map: " . $params['unique'] . "_map,
                        position: results[0].geometry.location,
                        url: 'http://maps.google.com?q=" . urlencode($address_string) . "'
                    });
                    google.maps.event.addListener(" . $params['unique'] . "_marker, 'click', function() {
                        window.open(" . $params['unique'] . "_marker.url);
                    });
                }
            });
        });
    }
   ";

    expJavascript::pushToFoot(array(
        "unique"=>'0-gmaps',
        "jquery"=>1,
        "src"=>'http://maps.google.com/maps/api/js'
     ));
    expJavascript::pushToFoot(array(
        "unique"=>'gmap-' . $params['unique'],
        "jquery"=>1,
        "content" => $script,
     ));

    echo $html;
}

?>
