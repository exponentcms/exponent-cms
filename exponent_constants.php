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
 * BASE Constant
 *
 * The BASE constant is the absolute path on the server filesystem, from the root (/ or C:\)
 * to the Exponent directory.
 */
if (!defined('BASE')) {
    define('BASE', __realpath(dirname(__FILE__)) . '/');
}

/**
 * PATH_RELATIVE Constant
 *
 * The PATH_RELATIVE constant is the web path to the Exponent directory,
 * from the web root.  It is related to the BASE constant, but different.
 */
if (!defined('PATH_RELATIVE')) {
    if (isset($_SERVER['DOCUMENT_ROOT'])) {
        define('PATH_RELATIVE', str_replace(__realpath($_SERVER['DOCUMENT_ROOT']), '', BASE));
    } else {
        // FIXME: PATH_RELATIVE definition will break in certain parts when the server does not offer the Document_root.
        // FIXME: Notable, it breaks in the installer.
        // This triggers on IIS, which has no DOCUMENT_ROOT.
        define('PATH_RELATIVE', __realpath(dirname($_SERVER['SCRIPT_NAME']) . '/'));
    }
}

/**
 * HOSTNAME Constant
 *
 * The HOSTNAME constant is the host name, from the web server.
 */
if (!defined('HOSTNAME')) {
    if (isset($_SERVER['HTTP_HOST'])) {
        define('HOSTNAME', $_SERVER['HTTP_HOST']);
    } elseif (isset($_SERVER['SERVER_NAME'])) {
        define('HOSTNAME', $_SERVER['SERVER_NAME']);
    } else {
        define('HOSTNAME', '');
    }
}

/**
 * URL_BASE Constant
 *
 * The URL_BASE constant is the base URL of the domain hosting the Exponent site.
 * It does not include the PATH_RELATIVE information.  The automatic
 * detection code can figure out if the server is running in SSL mode or not
 */
if (!defined('URL_BASE')) {
    if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
        $base = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    else
        $base = !empty($_SERVER['HTTPS']) ? "https" : "http";
    define('URL_BASE', $base . '://' . HOSTNAME);
}

/**
 * URL_BASE_SECURE Constant
 *
 * The URL_BASE_SECURE constant is the secure base URL of the domain hosting the Exponent site.
 * It does not include the PATH_RELATIVE information.
 */
if (!defined('URL_BASE_SECURE')) {
    define('URL_BASE_SECURE', 'https://' . HOSTNAME);
}

/**
 * URL_FULL Constant
 *
 * The URL_FULL constant is the full URL path to the Exponent directory.  The automatic
 * detection code can figure out if the server is running in SSL mode or not.
 */
if (!defined('URL_FULL')) {
    define('URL_FULL', URL_BASE . PATH_RELATIVE);
}

/**
 * UPLOAD_DIRECTORY Constant
 *
 * This is the directory where file uploads will go
 */
if (!defined('UPLOAD_DIRECTORY')) {
    define('UPLOAD_DIRECTORY', BASE . 'files/');
}

/**
 * UPLOAD_DIRECTORY Constant
 *
 * This is the directory where file uploads will go
 */
if (!defined('UPLOAD_DIRECTORY_RELATIVE')) {
    define('UPLOAD_DIRECTORY_RELATIVE', 'files/');
}

// iconset base
if (!defined('ICON_RELATIVE')) {
    define('ICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/');
}
if (!defined('MIMEICON_RELATIVE')) {
    define('MIMEICON_RELATIVE', PATH_RELATIVE . 'framework/core/assets/images/mimetypes/');
}

if (defined('SCRIPT_EXP_RELATIVE')) {
    define('SCRIPT_RELATIVE', PATH_RELATIVE . SCRIPT_EXP_RELATIVE);
    define('SCRIPT_ABSOLUTE', BASE . SCRIPT_EXP_RELATIVE);
} else {
    ob_start();
    define('SCRIPT_RELATIVE', PATH_RELATIVE);
    define('SCRIPT_ABSOLUTE', BASE);
}

if (!defined('SCRIPT_FILENAME')) {
    define('SCRIPT_FILENAME', 'index.php');
}

/** exdoc
 * Filesystem Error Response: Success
 *
 * @node Subsystems:Files
 */
define('SYS_FILES_SUCCESS', 0);

/** exdoc
 * Filesystem Error Response: Found File at Destination
 *
 * @node Subsystems:Files
 */
define('SYS_FILES_FOUNDFILE', 1);

/** exdoc
 * Filesystem Error Response: Found Directory at Destination
 *
 * @node Subsystems:Files
 */
define('SYS_FILES_FOUNDDIR', 2);

/** exdoc
 * Filesystem Error Response: Destination not writable
 *
 * @node Subsystems:Files
 */
define('SYS_FILES_NOTWRITABLE', 3);

/** exdoc
 * Filesystem Error Response: Destination not readable
 *
 * @node Subsystems:Files
 */
define('SYS_FILES_NOTREADABLE', 4);

/* exdoc
 * Filesystem Error Response: Destination not deletable
 * @node Subsystems:Files
 */
define('SYS_FILES_NOTDELETABLE', 5);

/** exdoc
 * The EQL header string for object dump file formats.
 * This header defines the version of EQL native to
 * the current implementation of the Backup Subsystem.
 *
 * @node Subsystems:Backup
 */
define('EQL_HEADER', 'EQL-Exponent Query Language');

/** exdoc
 * UI Level of Preview - No management links of any kind should be shown.
 *
 * @node Subsystems:Permissions
 */
define('UILEVEL_PREVIEW', 0);

/** exdoc
 * UI Level of Normal - Only normal management links (edit, delete, etc.) should be shown.
 *
 * @node Subsystems:Permissions
 */
define('UILEVEL_NORMAL', 1);

/** exdoc
 * UI Level of Permissions - Permission Management links (user and group perms) should be shown.
 *
 * @node Subsystems:Permissions
 */
define('UILEVEL_PERMISSIONS', 2);

/** exdoc
 * UI Level of Structure - All management links are shown.
 *
 * @node Subsystems:Permissions
 */
define('UILEVEL_STRUCTURE', 3);

define('DATABASE_TABLE_EXISTED', 1);
define('DATABASE_TABLE_INSTALLED', 2);
define('DATABASE_TABLE_FAILED', 3);
define('DATABASE_TABLE_ALTERED', 4);

/**
 * Database Field Type specifier
 * An index for the Exponent Data Definition Language.
 * This index indicates what type of column should be created
 * in the table.
 */
define('DB_FIELD_TYPE', 0);

/**
 * Database Field Length specifier
 * An index for the Exponent Data Definition Language.
 * This index indicates the length of the column.  Currently,
 * this is only applicable to textual field types.
 */
define('DB_FIELD_LEN', 1);

/**
 * Database Field Default specifier
 * An index for the Exponent Data Definition Language.
 * This index indicates the default value of a field in the table.
 */
define('DB_DEFAULT', 2);

/**
 * Database 'Incremental Field' specifier
 * An index for the Exponent Data Definition Language.
 * This index specifies that the field should automatically
 * increment its value.  This is ONLY applicable to ID fields
 * that are marked as PRIMARY.
 *
 * @see DB_PRIMARY
 * @see DB_DEF_ID
 */
define('DB_INCREMENT', 3);

/**
 * Database 'Primary Key' Field specifier
 * An index for the Exponent Data Definition Language.
 * This single unique index specifies that the field should be treated as
 * the single primary key for the table.  There can one or more fields
 * marked as 'primary' to establish a composite primary key in the table.
 *
 * @see DB_DEF_ID
 * @see DB_INCREMENT
 */
define('DB_PRIMARY', 4);

/**
 * Database 'Unique Key' Field specifier
 * An index for the Exponent Data Definition Language.
 * This index specifies that the field should be treated as a
 * unique key for the table.  There can zero or multiple unique keys
 * using single or multiple (composite) fields per table.
 *
 * @see DB_DEF_ID
 * @see DB_INCREMENT
 */
define('DB_UNIQUE', 5);

/**
 * Database 'Index' Field specifier
 * An index or key for the Exponent Data Definition Language.
 * This index specifies that the field should be treated as a
 * key for the table for more efficient lookups.  There can be
 * multiple key fields per table but they will NOT be composite keys.
 *
 * @see DB_DEF_ID
 * @see DB_INCREMENT
 */
define('DB_INDEX', 6);

/**
 * Database 'Full Text' Index Field specifier
 * An index for the Exponent Data Definition Language.
 * This index specifies that the field should be treated as a
 * key for the table where full text searches will be performed.
 * There is only one (composite) Full Text index per table.
 *
 * @see DB_DEF_ID
 * @see DB_INCREMENT
 */
define('DB_FULLTEXT', 7);

/**
 * ??????
 */
//define('DB_DEF_IGNORE',	100);

/**
 * Field Type specifier: Numeric ID
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a numeric ID.
 */
define('DB_DEF_ID', 101);

/**
 * Field Type specifier: Text
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a string of characters.
 * If used, the DB_FIELD_LEN index must also be specified.
 *
 * @see DB_FIELD_TYPE
 * @see DB_FIELD_LEN
 */
define('DB_DEF_STRING', 102);

/**
 * Field Type specifier: Integer
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be an integer.
 */
define('DB_DEF_INTEGER', 103);

/**
 * Field Type specifier: Boolean
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should be a boolean (1 or 0, true or
 * false).
 */
define('DB_DEF_BOOLEAN', 104);

/**
 * Field Type specifier: Timestamp
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should store a UNIX timestamp,
 * in order to portably manage dates and/or times.
 */
define('DB_DEF_TIMESTAMP', 105);

/**
 * Field Type specifier: Decimal
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should store a decimal number.
 */
define('DB_DEF_DECIMAL', 106);

/**
 * Field Type specifier: Datetime
 * A value for the Exponent Data Definition Language.
 * This value, specified for the DB_FIELD_TYPE index,
 * denotes that the field should store a MySQL datetime,
 * in order to portably manage dates and/or times.
 */
define('DB_DEF_DATETIME', 107);

/**
 * Table Alteration Error Message - 200 : Alter Not Needed
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration need not take place.
 */
define('TABLE_ALTER_NOT_NEEDED', 200);

/**
 * Table Alteration Error Message - 201 : Alter Succeeded
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration succeeded.
 */
define('TABLE_ALTER_SUCCEEDED', 201);

/**
 * Table Alteration Error Message - 201 : Alter Succeeded
 * A message constant returned by parts of the Database Subsystem
 * indicating that a table alteration failed.
 */
define('TABLE_ALTER_FAILED', 202);

/**
 * Table Meta Info : Table Comment
 * If specified in a table info array, a comment will be inserted
 * for the table (if the database engine in use supports table comments)
 */
define('DB_TABLE_COMMENT', 301);

/**
 * Form Meta Info : Form Field Type
 * This will specify what field type to use for a form.  Handy for scaffolding
 * when you have special needs for the form's input elements.
 */
define('FORM_FIELD_TYPE', 400);
define('FORM_FIELD_FILTER', 401);
define('FORM_FIELD_ONCLICK', 402);
define('FORM_FIELD_NAME', 403);
define('FORM_FIELD_LABEL', 404);
define('DECIMAL_MONEY', 405);
define('MONEY', 406);

/**
 * External Calendar Type
 * This will specify what type of external calendar feed is referenced
 */
define('ICAL_TYPE', 1);
define('GOOGLE_TYPE', 2);

if (!defined('TEMPLATE_FALLBACK_VIEW')) {
    define('TEMPLATE_FALLBACK_VIEW', BASE . 'framework/core/views/viewnotfound.tpl');
}

// Determines platform (OS), browser and version of the user
// Based on a phpBuilder article:
//   see http://www.phpbuilder.net/columns/tim20000821.php
if (empty($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = '';
}
if (!defined('EXPONENT_USER_OS')) {
    // 1. Platform
    if (stristr($_SERVER['HTTP_USER_AGENT'], 'win')) {
        define('EXPONENT_USER_OS', 'Win');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'mac')) {
        define('EXPONENT_USER_OS', 'Mac');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'linux')) {
        define('EXPONENT_USER_OS', 'Linux');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'unix')) {
        define('EXPONENT_USER_OS', 'Unix');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'os/2')) {
        define('EXPONENT_USER_OS', 'OS/2');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'iphone')) {
        define('EXPONENT_USER_OS', 'iPhone');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'ipad')) {
        define('EXPONENT_USER_OS', 'iPad');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'android')) {
        define('EXPONENT_USER_OS', 'Android');
    } elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'webos')) {
        define('EXPONENT_USER_OS', 'Mobile');
    } else {
        define('EXPONENT_USER_OS', 'Other');
    }
}

if (!defined('EXPONENT_USER_BROWSER')) {
    // 2. browser and version
    // (must check everything else before Mozilla)
    $log_version = array();
    if (preg_match('@Opera(/| )([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[2]);
        define('EXPONENT_USER_BROWSER', 'OPERA');
    } elseif (preg_match('@MSIE ([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'IE');
    } elseif (preg_match('@OmniWeb/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'OMNIWEB');
    } elseif (preg_match('@(Konqueror/)(.*)(;)@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[2]);
        define('EXPONENT_USER_BROWSER', 'KONQUEROR');
    } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)
            && preg_match('@Safari/([0-9]*)@', $_SERVER['HTTP_USER_AGENT'], $log_version2)
            && preg_match('@Chrome/([0-9]*)@', $_SERVER['HTTP_USER_AGENT'], $log_version3)
        ) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1] . '.' . $log_version2[1] . '.' . $log_version3[1]);
        define('EXPONENT_USER_BROWSER', 'CHROME');
    } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)
            && preg_match('@Safari/([0-9]*)@', $_SERVER['HTTP_USER_AGENT'], $log_version2)
        ) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1] . '.' . $log_version2[1]);
        define('EXPONENT_USER_BROWSER', 'SAFARI');
    } elseif (preg_match('@Mozilla/([0-9].[0-9]{1,2})@', $_SERVER['HTTP_USER_AGENT'], $log_version)) {
        define('EXPONENT_USER_BROWSER_VERSION', $log_version[1]);
        define('EXPONENT_USER_BROWSER', 'MOZILLA');
    } else {
        define('EXPONENT_USER_BROWSER_VERSION', 0);
        define('EXPONENT_USER_BROWSER', 'OTHER');
    }
}

if (!defined('JS_RELATIVE')) {
    /** exdoc
     * The relative path to Exponent's core javascript.
     */
    define('JS_RELATIVE', PATH_RELATIVE . 'framework/core/assets/js/');
    /** exdoc
     * The absolute url to Exponent's core javascript.
     */
    define('JS_URL', URL_FULL . 'framework/core/assets/js/');
}

/**
 * YUI 3 Version Constants
 * Changing the version here lets Exponent adjust where to look
 */
if (!defined('YUI3_RELATIVE')) {
    define('YUI3_VERSION', '3.18.1');
    define('YUI3_RELATIVE', PATH_RELATIVE . 'external/yui/' . YUI3_VERSION . '/build/');
    define('YUI3_URL', URL_FULL . 'external/yui/' . YUI3_VERSION . '/build/');
}

/**
 * YUI 2 Version Constants
 * Changing the version here lets Exponent adjust where to look
 */
if (!defined('YUI2_RELATIVE')) {
    define('YUI2_VERSION', '2.9.0');
    define('YUI2_RELATIVE', PATH_RELATIVE . 'external/yui/2in3/dist/' . YUI2_VERSION . '/build/');
    define('YUI2_URL', URL_FULL . 'external/yui/2in3/dist/' . YUI2_VERSION . '/build/');
}

/**
 * jQuery/jQueryUI Version Constants
 * Changing the version here lets Exponent adjust where to look
 */
if (!defined('JQUERY_RELATIVE')) {
    define('JQUERY_VERSION', '1.12.4');
    define('JQUERY2_VERSION', '2.2.4');
//    define('JQUERY3_VERSION', '3.1.1');
    define('JQUERYUI_VERSION', '1.12.1');
    define('JQUERY_RELATIVE', PATH_RELATIVE . 'external/jquery/');
    define('JQUERY_PATH', BASE . 'external/jquery/');
    define('JQUERY_URL', URL_FULL . 'external/jquery/');
    if (!defined('JQUERY_SCRIPT')) {
        define('JQUERY_SCRIPT', JQUERY_RELATIVE . 'js/jquery-' . JQUERY_VERSION . '.min.js');
    } // local jQuery v1.x script
    if (!defined('JQUERY2_SCRIPT')) {
        define('JQUERY2_SCRIPT', JQUERY_RELATIVE . 'js/jquery-' . JQUERY2_VERSION . '.min.js');
    } // local jQuery v2.x script
//    if (!defined('JQUERY3_SCRIPT')) {
//        define('JQUERY3_SCRIPT', JQUERY_RELATIVE . 'js/jquery-' . JQUERY3_VERSION . '.min.js');
//    } // local jQuery v3.x script
    if (!defined('JQUERYUI_SCRIPT')) {
//        define('JQUERYUI_SCRIPT', JQUERY_RELATIVE.'js/jquery-ui-'.JQUERYUI_VERSION.'.custom.min.js');
        define('JQUERYUI_SCRIPT', JQUERY_RELATIVE . 'js/jquery-ui.min.js');
    } // local jQueryUI script
    if (!defined('JQUERYUI_THEME')) {
        define('JQUERYUI_THEME', 'exponent');
    } // jQueryUI theme
    if (!defined('JQUERYUI_CSS')) {
        define('JQUERYUI_CSS', JQUERY_RELATIVE . 'css/' . JQUERYUI_THEME . '/jquery-ui.min.css');
    } // local jQueryUI stylesheet
}

/**
 * Smarty Version Constants
 * Changing the version here lets Exponent adjust where to look
 */
if (!defined('SMARTY_PATH')) {
    define('SMARTY_VERSION', '3.1.27');
    define('SMARTY_PATH', BASE . 'external/smarty-' . SMARTY_VERSION . '/libs/');
    define('SMARTY_DEVELOPMENT', false);
}

/**
 * Swift Mailer Version Constants
 * Changing the version here lets Exponent adjust where to look
 */
if (!defined('SWIFT_PATH')) {
    define('SWIFT_VERSION', '5.4.3');
    define('SWIFT_PATH', BASE . 'external/swiftmailer-' . SWIFT_VERSION . '/lib/');
}

?>