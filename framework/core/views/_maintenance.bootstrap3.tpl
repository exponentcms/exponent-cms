<!DOCTYPE html>
{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
    <!--  Maintenance Page Theme by Start Bootstrap and Jackie D'Elia Design -->
    <head>
        <title>{$smarty.const.SITE_TITLE} :: {'Down for Maintenance.'|gettext}</title>
        {expTheme::head([
            "xhtml"=>false,
            "framework"=>"bootstrap3",
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
            "lessvars"=>[
                'menu_height'=>MENU_HEIGHT,
                'menu_width'=>MENU_WIDTH,
                'menu_align_center'=>(MENU_ALIGN == 'center')
            ],
            "css_links"=>true,
            "css_theme"=>true
        ])}

        <!-- Custom CSS -->
        <style media="screen" type="text/css">
            /*!
             * Start Bootstrap - Landing Page Bootstrap Theme (http://startbootstrap.com)
             * modifications by Jackie D'Elia Design
             * https://jackiedelia.com
             * Code licensed under MIT
             * For details, see https://github.com/BlackrockDigital/startbootstrap-landing-page/blob/gh-pages/LICENSE
             */
            .site {
                display: flex;
                min-height: 100vh;
                flex-direction: column;
                background-image: url({$smarty.const.THEME_RELATIVE}images/bg.jpg);
                background-repeat: no-repeat;
                background-attachment: fixed;
                background-position: center bottom;
                -webkit-background-size: auto 100%;
                -moz-background-size: auto 100%;
                background-size: auto 100%;
                -o-background-size: auto 100%;
            }
            .overlay {
                background-color: rgb(0, 0, 0);
                background-color: rgba(0, 0, 0, 0.6);
                -webkit-background-size: auto 100%;
                -moz-background-size: auto 100%;
                background-size: auto 100%;
                -o-background-size: auto 100%;
            }
            body, h1, h2, h3, h4, h5, h6 {
                font-family: "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-weight: 400;
            }
            .container {
                text-align: center;
                color: #f8f8f8;
                display: flex;
                min-height: 100vh;
                flex-direction: column;
            }
            .intro-message {
                position: relative;
                margin: auto;
                padding: 10%;

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
            .intro-content {
                position: relative;
                /*padding-top: 5%;*/
                /*padding-bottom: 5%;*/
            }
            .intro-content .tel {
                color: #7DD6F3;
                font-size: 2em;
                font-weight: 400;
                text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.6);
            }
            .intro-content .address {
                color: #fff;
                font-size: 1.6em;
                font-weight: 400;
                text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.6);
            }
            .intro-footer {
                font-size: 1em;
                text-align: center;
            }
            .intro-footer .text-muted {
                color: #f5f5f5;
                font-weight: normal;
            }
            .network-name {
                text-transform: uppercase;
                font-size: 14px;
                font-weight: 400;
                letter-spacing: 2px;
            }
            footer {
                padding: 50px 0;
                background-color: #f8f8f8;
            }
            .intro-social-buttons {
                display: flex;
                justify-content: space-around;
                flex-direction: column;
                padding: 0;
                /*margin: 20px;*/
            }
            ol.intro-social-buttons, ul.intro-social-buttons {
                list-style-type: none;
            }
            ul.intro-social-buttons > li {
                margin: 10px;
            }
            ul.intro-social-buttons > li:last-child {} .btn-default {
                color: grey;
            }
            @media (min-width: 769px) {
                .site,
                .overlay {
                    -webkit-background-size: cover;
                    -moz-background-size: cover;
                    background-size: cover;
                    -o-background-size: cover;
                }
                .intro-divider {
                    /*width: 80%;*/
                    border-top: 1px solid #f8f8f8;
                    border-bottom: 1px solid dimgrey;
                }
                .intro-message > h1 {
                    font-size: 5em;
                }
                .intro-social-buttons {
                    flex-direction: row;
                }
                ul.intro-social-buttons > li {
                    margin-bottom: 20px;
                    padding: 0;
                }
                .intro-content .tel {
                    font-size: 3em;
                }
                .intro-content .address {
                    font-size: 2em;
                }
            }
            .required {
            	color : #ff0000;
            }
        </style>

        <!-- Custom Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="{$smarty.const.PATH_RELATIVE}external/html5shiv/html5shiv.js"></script>
            <script src="{$smarty.const.PATH_RELATIVE}external/Respond-1.4.2/dest/respond.src.js"></script>
        <![endif]-->
    </head>
    <body class="site">
        <div class="overlay">
            <div class="container">
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
                    <div class="copyright text-white small" style="float:right;">
                        {'Powered by'|gettext} <a class="text-white" href="http://www.exponentcms.org">ExponentCMS</a>
                    </div>
                </div>
            </div>
        </div>
        {expTheme::foot()}
    </body>
</html>