<?php

##################################################
#
# Copyright (c) 2004-2023 OIC Group, Inc.
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

/** @define "BASE" "../../../.." */
/**
 * @subpackage Controllers
 * @package Modules
 */
class motdController extends expController {
    public $useractions = array(
        'show'=>'Show Todays Message'
    );
    public $remove_configs = array(
        'aggregation',
        'categories',
        'comments',
        'ealerts',
        'facebook',
        'files',
//        'pagination',
        'rss',
        'tags',
        'twitter',
    ); // all options: ('aggregation','categories','comments','ealerts','facebook','files','pagination','rss','tags','twitter',)

    static function displayname() { return gt("Message of the Day"); }
    static function description() { return gt("Display a message for a given day of the year."); }
    static function isSearchable() { return true; }
    public static function canImportData() { return true; }

    function show() {
        expHistory::set('viewable', $this->params);
        if (isset($this->params['month']) && isset($this->params['day'])) {
            $month = (int)$this->params['month'];
            $day = (int)$this->params['day'];
            $now = date('U', strtotime(date('Y') . '-' . $month . '-' . $day));
        } else {
            $now = time();
            $month = date('n', $now);
            $day = date('j', $now);
        }
        $message = $this->motd->find('first', $this->aggregateWhereClause() . ' AND month='.$month.' AND day='.$day);
        if (empty($message->id)) {
            $message = $this->motd->find('first', $this->aggregateWhereClause() . ' AND month=0 AND day='.$day);
            if (empty($message->id) && (!empty($this->config['userand']) && $this->config['userand']==true)) {
                $message = $this->motd->find('first',  $this->aggregateWhereClause(), 'RAND()');
            }
        }

        assign_to_template(array(
            'message'=>$message,
            'now'=>$now
        ));
    }

    function show_random() {
        expHistory::set('viewable', $this->params);
        $message = $this->motd->find('first',  $this->aggregateWhereClause(), 'RAND()');
        assign_to_template(array(
            'message'=>$message,
            'now'=>time()
        ));
    }

    function showall_year() {
        expHistory::set('viewable', $this->params);
        $locsql = $this->aggregateWhereClause();
        $time = time();
        $date = expDateTime::startOfYearTimestamp($time); // get the first month
        $annual = array();
        for ($i = 1; $i <= 12; $i++) {
            $month = expDateTime::startOfMonthTimestamp($date); // reset to first of month for loop

            $annual[$i] = expDateTime::monthlyDaysTimestamp($month);
            $info = getdate($month);
            $timefirst = mktime(0, 0, 0, $info['mon'], 1, $info['year']);
            $now = getdate(time());
            $endofmonth = date('t', $month);
            foreach ($annual[$i] as $weekNum => $week) {
                foreach ($week as $dayNum => $day) {
                    if ($dayNum <= $endofmonth) {
                        $annual[$i][$weekNum][$dayNum]['motd'] = ($annual[$i][$weekNum][$dayNum]['ts'] != -1) ? $this->motd->find("first", $locsql . " AND month = " . $i . " AND day = " . $dayNum) : -1;
                    }
                }
            }
            $annual[$i]['timefirst'] = $timefirst;
            $annual[$i]['currentday'] = $now['mday'];
            $annual[$i]['currentmonth'] = $now['mon'];

            $date = strtotime('+1 month', $date); // advance to next month
        }

        $days = expDateTime::monthlyDaysTimestamp();
        $now = getdate(time());
        $endofmonth = date('t', $month);
        foreach ($days as $weekNum => $week) {
            foreach ($week as $dayNum => $day) {
                if ($dayNum <= $endofmonth) {
                    $days[$weekNum][$dayNum]['motd'] = ($days[$weekNum][$dayNum]['ts'] != -1) ? $this->motd->find("first", $locsql . " AND month = 0 AND day = " . $dayNum) : -1;
                }
            }
        }

        assign_to_template(array(
            "year"     => $annual,
            "days"     => $days,
            "now"      => $time,
        ));
    }

    function showall() {
        expHistory::set('viewable', $this->params);
        if (isset($this->params['order'])) {
            if ($this->params['order'] === 'month') {
                $order = 'month,day';
            } else {
                $order = $this->params['order'];
            }
        } else {
            $order = 'month,day';
        }
        $page = new expPaginator(array(
            'model' => 'motd',
            'where' => $this->aggregateWhereClause(),
            'limit' => (isset($this->config['limit']) && $this->config['limit'] != '') ? $this->config['limit'] : 10,
            'order' => $order,
            'dir' => (isset($this->params['dir']) ? $this->params['dir'] : ''),
            'page' => (isset($this->params['page']) ? $this->params['page'] : 1),
            'controller' => $this->baseclassname,
            'action' => $this->params['action'],
            'src' => $this->loc->src,
            'columns' => array(
                gt('Date') => 'month',
                gt('Message') => 'body'
            ),
        ));

        assign_to_template(array(
            'page' => $page
        ));
    }

    function update() {
        $timestamp = mktime(0, 0, 0, $this->params['month'], 1);
        $endday = expDateTime::endOfMonthDay($timestamp);
        if ($this->params['day'] > $endday) {
            expValidator::failAndReturnToForm(gt('There are only').' '.$endday.' '.gt('days in').' '.$this->motd->months[$this->params['month']], $this->params);
        }
        parent::update();
    }

    function import() {
        $pullable_modules = expModules::listInstalledControllers($this->baseclassname);
        $modules = new expPaginator(array(
            'records' => $pullable_modules,
            'controller' => $this->loc->mod,
            'action' => $this->params['action'],
            'order'   => isset($this->params['order']) ? $this->params['order'] : 'section',
            'dir'     => isset($this->params['dir']) ? $this->params['dir'] : '',
            'page'    => (isset($this->params['page']) ? $this->params['page'] : 1),
            'columns' => array(
                gt('Title') => 'title',
                gt('Page')  => 'section'
            ),
        ));

        assign_to_template(array(
            'modules' => $modules,
        ));
    }

    public function import_select() {
        if (empty($this->params['import_aggregate'])) {
            expValidator::setErrorField('import_aggregate[]');
            expValidator::failAndReturnToForm(gt('You must select a module.'), $this->params);
        }

        //Get the temp directory to put the uploaded file
        $directory = "tmp";

        //Get the file save it to the temp directory
        if ($_FILES["upload"]["error"] == UPLOAD_ERR_OK) {
            //	$file = file::update("upload",$directory,null,time()."_".$_FILES['upload']['name']);
            $file = expFile::fileUpload("upload", false, false, time() . "_" . $_FILES['upload']['name'], $directory.'/');
            if ($file == null) {
                switch ($_FILES["upload"]["error"]) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $this->params['_formError'] = gt('The file you attempted to upload is too large.  Contact your system administrator if this is a problem.');
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->params['_formError'] = gt('The file was only partially uploaded.');
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->params['_formError'] = gt('No file was uploaded.');
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                    case UPLOAD_ERR_CANT_WRITE:
                        $this->params['_formError'] = gt('Server Temp File Error.');
                        break;
                    case UPLOAD_ERR_EXTENSION:
                    default:
                        $this->params['_formError'] = gt('A strange internal error has occurred.  Please contact the Exponent Developers.');
                        break;
                }
                expSession::set("last_POST", $this->params);
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit("");
            }
        }

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $fh = fopen(BASE . $directory . "/" . $file->filename, 'rb');
        $msginfo = array();
        $msgarray = array();
        $linenum = 1;

        while (($filedata = fgetcsv($fh)) != false) {
            if ($linenum >= $this->params["rowstart"]) {
                $msginfo['month'] = "";
                $msginfo['day'] = "";
                $msginfo['message'] = "";

                switch ($this->params["content"]) {
                    case "month_day_message":
                        $month = array_shift($filedata);
                        $month = date_parse($month);
                        $msginfo['month'] = $month['month'];
                    case "day_message":
                        $msginfo['day'] = (int)array_shift($filedata);
                    case "message":
                        $msginfo['message'] = expString::convertUTF(implode(",", $filedata)); // condense remaining part is the message
                }

                $msginfo['linenum'] = $linenum;
                $msgarray[] = $msginfo;
            }
            $linenum++;
        }
        fclose($fh);
        ini_set('auto_detect_line_endings',$line_end);

        assign_to_template(array(
            "msgarray" => $msgarray,
            "params" => $this->params,
            'filename' => $directory . "/" . $file->filename,
            'source' => $this->params['import_aggregate'][0]
        ));
    }

    public function import_add() {
        if (!empty($this->params['filename']) && (strpos($this->params['filename'], 'tmp/') === false || strpos($this->params['folder'], '..') !== false)) {
            header('Location: ' . URL_FULL);
            exit();  // attempt to hack the site
        }

        $line_end = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings',TRUE);
        $fh = fopen(BASE . $this->params["filename"], 'rb');
        $msginfo = array();
        $msginfo['location_data'] = serialize(expCore::makeLocation('motd', $this->params["source"]));
        $linenum = 1;
        $count = 0;

        while (($filedata = fgetcsv($fh)) != false) {
            if ($linenum >= $this->params["rowstart"]) {
                $msginfo['month'] = "";
                $msginfo['day'] = "";
                $msginfo['body'] = "";

                switch ($this->params["content"]) {
                    case "month_day_message":
                        $month = array_shift($filedata);
                        $month = date_parse($month);
                        $msginfo['month'] = $month['month'];
                    case "day_message":
                        $msginfo['day'] = (int)array_shift($filedata);
                    case "message":
                        $msginfo['body'] = expString::convertUTF(implode(",", $filedata)); // condense remaining part is the message
                }

                $newmotd = new motd($msginfo);
                $newmotd->update();
                $count++;

            }
            $linenum++;
        }
        fclose($fh);
        ini_set('auto_detect_line_endings',$line_end);

        unlink(BASE . $this->params["filename"]);
        flash('notice', $count.' '.gt('Messages Imported'));
        expHistory::back();
    }

}

?>