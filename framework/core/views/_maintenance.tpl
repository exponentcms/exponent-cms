{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.LANG_CHARSET}"/>
        <meta name="Generator" content="Exponent Content Management System - v{expVersion::getVersion(true)}"/>
        <title>{$smarty.const.SITE_TITLE} :: {'Down for Maintenance.'|gettext}</title>
        <style media="screen" type="text/css">
            html {
                background : #397993;
                text-align : left;
            }
            body {
                font-size   : 15px;
                text-align  : left;
                font-family : "Trebuchet MS", sans-serif;
                color       : #333;
            }
            .box {
                margin                : 15%;
                padding               : 3em;
                font-size             : 10pt;
                font-family           : Arial, sans-serif;
                font-weight           : normal;
                color                 : #333;
                background            : #fffae1;
                border                : 2px solid black;
                -moz-box-shadow       : inset 0 0 8px #dedede;
                -webkit-box-shadow    : inset 0 0 8px #dedede;
                box-shadow            : inset 0 0 8px #dedede;
                -moz-border-radius    : 12px;
                -webkit-border-radius : 12px;
                border-radius         : 12px;
            }
        </style>
    </head>
    <body>
        <div class="box">
            {$smarty.const.MAINTENANCE_MSG_HTML}
            {if $db_down}
                <h3 style="color:red">{'Database is currently Off-line!'|gettext}</h3>
            {/if}
            <h3>{'Administrator Login'|gettext}</h3>
            {chain controller=login action=showlogin view=showlogin_stacked title="Administrators Login"|gettext}
        </div>
        <div style="float:right;">{'Powered by'|gettext} <a style="color:black;" href="http://www.exponentcms.org">ExponentCMS</a></div>
    </body>
</html>
