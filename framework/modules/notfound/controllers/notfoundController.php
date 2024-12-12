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
 * @subpackage Controllers
 * @package Modules
 */

class notfoundController extends expController {
    protected $add_permissions = array(
        'showall'=>'Showall',
        'show'=>'Show'
    );

    static function displayname() { return gt("Page Not Found"); }
    static function description() { return gt("This controller handles routing to the appropriate place when pages are not found."); }
    static function hasSources() { return false; }
    static function hasViews() { return false; }
    static function hasContent() { return false; }

    public function handle() {
        global $router;

        $args = array_merge(array('controller'=>'notfound', 'action'=>'page_not_found'), $router->url_parts);
        header("Refresh: 0; url=".$router->makeLink($args), false, 404);
    }

    public function page_not_found() {
        global $router;

//        header(':', true, 404);
        self::send_http_response(404);
        $params = $router->params;
        unset(
            $params['controller'],
            $params['action']
        );
        $terms = empty($params[0]) ? '' : $params[0];
        if (empty($terms) && !empty($params['title'])) $terms = $params['title'];
        expCSS::pushToHead(array(
//	        "unique"=>"search-results",
	        "link"=>$this->asset_path."css/results.css",
	        )
	    );
        // If magic quotes is on and the user uses modifiers like " (quotes) they get escaped. We don't want that in this case.
//        if (get_magic_quotes_gpc()) {
//            $terms = stripslashes($terms);
//        }
        $terms = expString::escape(htmlspecialchars($terms));

        // check for server requested error documents here instead of treating them as a search request
        if ($terms == SITE_404_FILE) {
            self::handle_not_found();
        } elseif ($terms == SITE_403_FILE) {
            self::handle_not_authorized();
        } elseif ($terms == SITE_500_FILE) {
            self::handle_internal_error();
        }

        $search = new search();
		$page = new expPaginator(array(
			'model'=>'search',
			'controller'=>$this->params['controller'],
			'action'=>$this->params['action'],
			'records'=>$search->getSearchResults($terms, false, 0, 30),
			//'sql'=>$sql,
            'limit'=>10,
			'order'=>'score',
			'dir'=>'DESC',
            'page'=>(isset($this->params['page']) ? $this->params['page'] : 1),
        ));

        assign_to_template(array(
            'page'=>$page,
            'terms'=>$terms
        ));
    }

    public static function handle_not_found() {
//        header(':', true, 404);
        self::send_http_response(404);
        echo '<h1>', SITE_404_TITLE, '</h1><br />';
        echo SITE_404_HTML;
    }

    public static function handle_not_authorized() {
//        header(':', true, 403);
        self::send_http_response(403);
        echo SITE_403_HTML;
    }

    public static function handle_internal_error() {
//        header(':', true, 500);
        self::send_http_response(500);
        echo '<h1>', gt('An Internal Server Error was Encountered!'), '</h1>';
    }

    public static function send_http_response($code) {
        if (!function_exists('http_response_code')) {
            function http_response_code($code = NULL) {
                if ($code !== NULL) {
                    switch ($code) {
                        case 100: $text = 'Continue'; break;
                        case 101: $text = 'Switching Protocols'; break;
                        case 200: $text = 'OK'; break;
                        case 201: $text = 'Created'; break;
                        case 202: $text = 'Accepted'; break;
                        case 203: $text = 'Non-Authoritative Information'; break;
                        case 204: $text = 'No Content'; break;
                        case 205: $text = 'Reset Content'; break;
                        case 206: $text = 'Partial Content'; break;
                        case 300: $text = 'Multiple Choices'; break;
                        case 301: $text = 'Moved Permanently'; break;
                        case 302: $text = 'Moved Temporarily'; break;
                        case 303: $text = 'See Other'; break;
                        case 304: $text = 'Not Modified'; break;
                        case 305: $text = 'Use Proxy'; break;
                        case 400: $text = 'Bad Request'; break;
                        case 401: $text = 'Unauthorized'; break;
                        case 402: $text = 'Payment Required'; break;
                        case 403: $text = 'Forbidden'; break;
                        case 404: $text = 'Not Found'; break;
                        case 405: $text = 'Method Not Allowed'; break;
                        case 406: $text = 'Not Acceptable'; break;
                        case 407: $text = 'Proxy Authentication Required'; break;
                        case 408: $text = 'Request Time-out'; break;
                        case 409: $text = 'Conflict'; break;
                        case 410: $text = 'Gone'; break;
                        case 411: $text = 'Length Required'; break;
                        case 412: $text = 'Precondition Failed'; break;
                        case 413: $text = 'Request Entity Too Large'; break;
                        case 414: $text = 'Request-URI Too Large'; break;
                        case 415: $text = 'Unsupported Media Type'; break;
                        case 500: $text = 'Internal Server Error'; break;
                        case 501: $text = 'Not Implemented'; break;
                        case 502: $text = 'Bad Gateway'; break;
                        case 503: $text = 'Service Unavailable'; break;
                        case 504: $text = 'Gateway Time-out'; break;
                        case 505: $text = 'HTTP Version not supported'; break;
                        default:
                            exit('Unknown http status code "' . htmlentities($code) . '"');
                        break;
                    }

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                    header($protocol . ' ' . $code . ' ' . $text);

                    $GLOBALS['http_response_code'] = $code;

                } else {
                    $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
                }
                return $code;
            }
        }

        return http_response_code($code);
    }

}

?>