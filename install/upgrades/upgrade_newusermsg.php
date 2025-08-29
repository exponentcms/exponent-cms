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
 * This is the class upgrade_newusermsg
 *
 * @package Installation
 * @subpackage Upgrade
 */
class upgrade_newusermsg extends upgradescript
{
    protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
    protected $to_version = '3.0.2';
    public $priority = 78; // set this to a low priority

    /**
     * name/title of upgrade script
     * @return string
     */
    static function name()
    {
        return "Upgrade New User Message";
    }

    /**
     * generic description of upgrade script
     * @return string
     */
    function description()
    {
        return "The New User Welcome Message may now be styled. Previously it was plain text.";
    }

    /**
     * additional test(s) to see if upgrade script should be run
     * @return bool
     */
    function needed()
    {
        return empty(USER_REGISTRATION_WELCOME_MSG_HTML);
    }

    /**
     * reads in and corrects the modstate table, esp. since it has no index and allows duplicate entries
     *   we will assume that all old school modules have been upgraded at this point
     *
     * @return string
     */
    function upgrade()
    {
        expSettings::change('USER_REGISTRATION_WELCOME_MSG_HTML', USER_REGISTRATION_WELCOME_MSG);
        return gt('New User Welcome Message was Upgraded.');
    }
}

?>
