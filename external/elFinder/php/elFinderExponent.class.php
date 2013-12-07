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
 * This is the class elFinderExponent
 * elFinder extension for Exponent CMS expFile filesystem.
 *
 * @author     Dave Leffler
 * @package    Connectors
 * @subpackage Connectors
 */
class elFinderExponent extends elFinder
{

    function __construct($opts)
    {
        parent::__construct($opts);
        /* Adding new commands */
        $this->commands['owner'] = array('target' => true, 'content' => false);
        $this->commands['title'] = array('target' => true, 'content' => false);
        $this->commands['alt'] = array('target' => true, 'content' => false);
    }

    protected function owner($args)
    {
        $target = $args['target'];
        $title = $args['content'];
        $error = array(self::ERROR_UNKNOWN, '#' . $target);

        if (($volume = $this->volume($target)) == false
            || ($file = $volume->file($target)) == false
        ) {
            return array('error' => $this->error($error, self::ERROR_FILE_NOT_FOUND));
        }

        $error[1] = $file['name'];

        if ($volume->commandDisabled('owner')) {
            return array('error' => $this->error($error, self::ERROR_ACCESS_DENIED));
        }

        if (($title = $volume->owner($target, $title)) == -1) {
            return array('error' => $this->error($error, $volume->error()));
        }

        return array('owner' => $title);
    }

    protected function title($args)
    {
        $target = $args['target'];
        $title = $args['content'];
        $error = array(self::ERROR_UNKNOWN, '#' . $target);

        if (($volume = $this->volume($target)) == false
            || ($file = $volume->file($target)) == false
        ) {
            return array('error' => $this->error($error, self::ERROR_FILE_NOT_FOUND));
        }

        $error[1] = $file['name'];

        if ($volume->commandDisabled('title')) {
            return array('error' => $this->error($error, self::ERROR_ACCESS_DENIED));
        }

        if (($title = $volume->title($target, $title)) == -1) {
            return array('error' => $this->error($error, $volume->error()));
        }

        return array('title' => $title);
    }

    protected function alt($args)
    {
        $target = $args['target'];
        $alt = $args['content'];
        $error = array(self::ERROR_UNKNOWN, '#' . $target);

        if (($volume = $this->volume($target)) == false
            || ($file = $volume->file($target)) == false
        ) {
            return array('error' => $this->error($error, self::ERROR_FILE_NOT_FOUND));
        }

        $error[1] = $file['name'];

        if ($volume->commandDisabled('alt')) {
            return array('error' => $this->error($error, self::ERROR_ACCESS_DENIED));
        }

        if (($alt = $volume->alt($target, $alt)) == -1) {
            return array('error' => $this->error($error, $volume->error()));
        }

        return array('alt' => $alt);
    }

}

?>