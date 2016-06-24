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
 * @subpackage Upgrade
 * @package Installation
 */

/**
 * This is the class fix_help_sefurls
 */
class fix_help_sefurls extends upgradescript
{
    protected $from_version = '0.0.0';  // version number lower than first released version, 2.0.0
//	protected $to_version = '2.2.1';
    public $optional = true;
    public $priority = 52;

    /**
     * name/title of upgrade script
     * @return string
     */
    static function name()
    {
        return "Validate and fix help document sef url's";
    }

    /**
     * generic description of upgrade script
     * @return string
     */
    function description()
    {
        return "In the past, some help doc sef_url's were unnecessarily given a unique suffix to prevent duplication.  This script corrects that issue.";
    }

    /**
     * additional test(s) to see if upgrade script should be run
     * @return bool
     */
    function needed()
    {
        $help = new help();
        return $help->find('count', "sef_url REGEXP '\([^[:space:]]\{1,\}\)[[:digit:]]\{1,10\}-0\.[[:digit:]]\{1,8\}'");  // are there any help docs?
    }

    /**
     * Attempts to correct help doc sef_url's which have been given microtime suffix
     *
     * @return bool
     */
    function upgrade()
    {
        $bad = '';
        $fixed = 0;
        $bad = 0;
        $bad_help = array();
        $hv = new help_version();
        $help = new help();
        foreach ($hv->findValue('all', 'id') as $version_id) {
            $helpdocs = $help->find('all', "help_version_id=" . $version_id . " AND sef_url REGEXP '\([^[:space:]]\{1,\}\)[[:digit:]]\{1,10\}-0\.[[:digit:]]\{1,8\}'");
            foreach ($helpdocs as $helpdoc) {
                $matches = array();
                $found = preg_match('/(\S+)-\d{10}-0\.\d{8}/', $helpdoc->sef_url, $matches);
                if ($found && !empty($matches[1])) {
                    $bad_help[$bad+1]['sef_url'] = $helpdoc->sef_url;
                    $opts = array('grouping_sql' => " AND help_version_id='" . $helpdoc->help_version_id . "'");
                    $helpdoc->sef_url = $matches[1];  // new stripped sef url
                    if (is_bool(expValidator::uniqueness_of('sef_url', $helpdoc, $opts))) {
                        $helpdoc->update();
                        $fixed++;
                    } else {
                        $bad++;  // leave help doc sef_url as is because it's root was a duplicate
                        $bad_help[$bad]['id'] = $helpdoc->id;
                        $bad_help[$bad]['help_verstion'] = $helpdoc->help_version->version;
                    }
                }
            }
        }

        eLog($bad_help);
        return ($fixed ? $fixed : gt('No')) . ' ' . gt('unnecessarily long help doc sef url\'s were found and corrected.') . ' ' . ($bad ? $bad . ' ' . gt('long help doc sef url\'s still required.') : '');
    }

}

?>
