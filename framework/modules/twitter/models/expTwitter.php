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
 * @subpackage Models
 * @package    Modules
 */

class expTwitter extends Twitter {

    /**
     * Default constructor
     *
     * @param string $consumerKey    The consumer key to use.
     * @param string $consumerSecret The consumer secret to use.
     */
	public function __construct($consumerKey='', $consumerSecret='')
    {
        $this->setConsumerKey($consumerKey);
        $this->setConsumerSecret($consumerSecret);
    }

    /**
     * Make the call
     *
     * @param  string $url           The url to call.
     * @param array   $parameters
     * @param bool    $authenticate
     * @param string  $method
     * @param null    $filePath
     * @param bool    $expectJSON
     * @param bool    $returnHeaders
     *
     * @return string
     */
    protected function doCall(
        $url, array $parameters = null, $authenticate = false, $method = 'GET',
        $filePath = null, $expectJSON = true, $returnHeaders = false
    ) {
         global $user;

       // wrap the call in a try/catch to prevent exception fault killing page
        try {
            return parent::doCall(
                $url, $parameters, $authenticate, $method,
                $filePath, $expectJSON, $returnHeaders
            );
        } catch (Exception $e) {
            if ($user->isAdmin()) flash('error', 'Twitter: ' . $e->getMessage());
        }

    }

}