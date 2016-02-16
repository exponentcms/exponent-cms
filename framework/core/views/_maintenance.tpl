{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
        <style type="text/css" media="screen">
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
        <!-- MINIFY REPLACE -->
    </head>
    <body>
        <div class="box">
            {$smarty.const.MAINTENANCE_MSG_HTML}
            {if $smarty.const.MAINTENANCE_USE_RETURN_TIME && $smarty.const.MAINTENANCE_RETURN_TIME > time()}
                {*{assocarray}*}
                    {*prm: [*}
                        {*count: $smarty.const.MAINTENANCE_RETURN_TIME*}
                        {*title: $smarty.const.MAINTENANCE_RETURN_TEXT*}
                    {*]*}
                {*{/assocarray}*}
                {$prm = ["count" => $smarty.const.MAINTENANCE_RETURN_TIME, "title" => $smarty.const.MAINTENANCE_RETURN_TEXT]}
                {showmodule controller=countdown action=show view=show_circles params=$prm}
            {/if}
            {if $db_down}
                <h3 style="color:red">{'Database is currently Off-line!'|gettext}</h3>
            {elseif $login}
                {* NOTE no database, so we can't log on! *}
                {showmodule controller=login action=showlogin view=showlogin_stacked moduletitle="Administrators Login"|gettext}
            {/if}
        </div>
        <div style="float:right;">{'Powered by'|gettext} <a style="color:black;" href="http://www.exponentcms.org">ExponentCMS</a></div>
        {expTheme::foot()}  {* NOTE we need to output css & javascript *}
    </body>
</html>
