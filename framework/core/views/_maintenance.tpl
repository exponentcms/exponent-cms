<!DOCTYPE HTML>
{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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
<html lang="{substr($smarty.const.LOCALE,0,2)}">
    <head>
        <title>{$smarty.const.SITE_TITLE} :: {'Down for Maintenance.'|gettext}</title>
        {expTheme::head([
            "xhtml"=>false,
            "css_primer"=>[
                YUI3_RELATIVE|cat:"cssreset/cssreset-min.css",
                YUI3_RELATIVE|cat:"cssfonts/cssfonts-min.css",
                YUI3_RELATIVE|cat:"cssgrids/cssgrids-min.css"
            ],
            "viewport"=>[
                "width"=>"device-width",
                "height"=>"device-height",
                "initial_scale"=>1,
                "minimum_scale"=>0.25,
                "user_scalable"=>true
            ],
            "css_core"=>[
                "common"
            ],
            "css_links"=>true,
            "css_theme"=>true
        ])}

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
            .intro-message > h1 {
                margin: 0;
                text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.6);
                font-size: 3em;
            }
            .intro-divider {
                width: 100%;
                border-top: 1px solid #f8f8f8;
                border-bottom: 1px solid dimgrey;
            }
            .intro-message > h2, .intro-message > h3 {
                text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.6);
            }
            .intro-message > h2 {
                font-size: 2em;
                line-height: 1.625;
                color: #7DD6F3;
            }
            .intro-message > h3 {
                font-size: 1.5em;
                line-height: 1.625;
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
                text-align            : center;
            }
            .intro-divider {
                width: 100%;
                border-top: 1px solid #f8f8f8;
                border-bottom: 1px solid dimgrey;
            }
            .required {
            	color : #ff0000;
            }
        </style>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="{$smarty.const.PATH_RELATIVE}external/html5shiv/html5shiv.js"></script>
            <script src="{$smarty.const.PATH_RELATIVE}external/Respond-1.4.2/dest/respond.src.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="box">
            {if file_exists("`$smarty.const.THEME_RELATIVE`images/logo.png")}
                <p></p>
                <img class="img-responsive img-thumbnail center-block" style="max-width: 480px;" src="{$smarty.const.THEME_RELATIVE}images/logo.png" />
            {/if}
            <div class="intro-message">
                <h1>{$smarty.const.ORGANIZATION_NAME}</h1>
                {* NOTE no database, so we can't log on! *}
                {if $db_down}
                    <h2 class="required">{'Our website is currently down for maintenance.'|gettext}</h2>
                    <h3>{'It will return once our technicians have completed repairs.'|gettext}</h3>
                {else}
                    <hr class="intro-divider">
                    {$smarty.const.MAINTENANCE_MSG_HTML}
                    {if $smarty.const.MAINTENANCE_USE_RETURN_TIME && $smarty.const.MAINTENANCE_RETURN_TIME > time()}
                        {$prm = ["count" => $smarty.const.MAINTENANCE_RETURN_TIME, "title" => $smarty.const.MAINTENANCE_RETURN_TEXT]}
                        {showmodule controller=countdown action=show view=show params=$prm}
                    {/if}
                    {if $login}...
                        {showmodule controller=login action=showlogin view=showlogin_stacked moduletitle="Administrators Login"|gettext}
                    {/if}
                {/if}
            </div>
            <div class="intro-footer">
                <div style="float:right;">{'Powered by'|gettext} <a style="color:black;" href="http://www.exponentcms.org">ExponentCMS</a></div>
            </div>
        </div>
        {expTheme::foot()}
    </body>
</html>
