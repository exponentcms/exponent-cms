<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 *
 * @package    Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty {plural} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     plural<br>
 * Purpose:  pluralize a string
 *
 * @param string $word
 * @param int    $counter
 *
 * @return array
 */

function smarty_modifier_plural($word = '', $counter = 0) {
    # What to append to the word to make it plural?
    $plural_marker = 's';

    # Words ending in [ o ]
    if (preg_match('/o$/', $word)) {
        $plural_marker = 'es';
    }

    # Words ending in [ y ]
    # frequency => frequencies, copy => copies
    if (preg_match('/y$/', $word)) {
        $plural_marker = 'ies';

        # Remove the last letter: [ y ]
        $word = substr($word, 0, strlen($word) - 1);
    }

    # Words having [ oo ] in the second last letters.
    # foot => feet, goose => geese
    if (preg_match('/oo([a-z])?$/', $word, $data)) {
        $plural_marker = 'ee' . $data[1];
        $word = substr($word, 0, strlen($word) - 3);
    }

    $plural = $word . (($counter != 1) ? $plural_marker : '');
    return $plural;
}

?>