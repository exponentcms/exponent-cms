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
 * Smarty plugin
 *
 * @package    Smarty-Plugins
 * @subpackage Modifier
 */

/**
 * Smarty regex_replace modifier plugin
 * Type:     modifier<br>
 * Name:     regex_replace<br>
 * Purpose:  regular expression search/replace
 *
 * @link     http://smarty.php.net/manual/en/language.modifier.regex.replace.php
 *           regex_replace (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * @param string
 * @param    string|array
 * @param    string|array
 *
 * @return string
 */
/**
 * Turn all URLs in clickable links.
 *
 * @param string $value
 * @param array  $protocols http/https, ftp, mail, twitter
 * @param array  $attributes
 *
 * @internal param string $mode normal or all
 * @return string
 */
function smarty_modifier_linkify(
    $value,
    $protocols = array('http', 'mail'),
    array $attributes = array()
) {
    global $attr, $links, $protocol;

    // Link attributes
    $attr = '';
    foreach ($attributes as $key => $val) {
        $attr = ' ' . $key . '="' . htmlentities($val) . '"';
    }

    $links = array();

    // Extract existing links and tags
    $value = preg_replace_callback(
        '~(<a .*?>.*?</a>|<.*?>)~i',
        "existing_links",
        $value
    );

    // Extract text links for each protocol
    foreach ((array)$protocols as $protocol) {
        global $protocol1;

        $protocol1 = $protocol;

        switch ($protocol) {
            case 'http':
            case 'https':
                $value = preg_replace_callback(
                    '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i',
                    "http_link",
                    $value
                );
                break;
            case 'mail':
                $value = preg_replace_callback(
                    '~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~',
                    "mail_link",
                    $value
                );
                break;
            case 'twitter':
                $value = preg_replace_callback(
                    '~(?<!\w)[@#](\w++)~',
                    "twitter_link",
                    $value
                );
                break;
            default:
                $value = preg_replace_callback(
                    '~' . preg_quote($protocol, '~')
                    . '://([^\s<]+?)(?<![\.,:])~i',
                    "other_link",
                    $value
                );
                break;
        }
    }

    // Insert all link
    return preg_replace_callback(
        '/<(\d+)>/',
        "insert_link",
        $value
    );
}

function existing_links($match) {
    global $links;

    return '<' . array_push($links, $match[1]) . '>';
}

function http_link($match) {
    global $protocol1, $links, $attr;

    if ($match[1]) {
        $protocol1 = $match[1];
    }
    $link = $match[2] ? $match[2] : $match[3];
    return '<'
    . array_push(
        $links,
        "<a $attr href=\"$protocol1://$link\">$link</a>"
    )
    . '>';
}

function mail_link($match) {
    global $links, $attr;

    return '<'
    . array_push(
        $links,
        "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>"
    )
    . '>';
}

function twitter_link($match) {
    global $links, $attr;

    return '<'
    . array_push(
        $links,
        "<a $attr href=\"https://twitter.com/"
        . ($match[0][0] == '@' ? ''
            : 'search/%23')
        . $match[1]
        . "\">{$match[0]}</a>"
    ) . '>';
}

function other_link($match) {
    global $protocol1, $links, $attr;

    return '<'
    . array_push(
        $links,
        "<a $attr href=\"$protocol1://{$match[1]}\">{$match[1]}</a>"
    )
    . '>';
}

function insert_link($match) {
    global $links;

    return $links[$match[1] - 1];
}

