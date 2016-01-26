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

{css unique="bootstrap-top-nav"  lesscss="`$smarty.const.PATH_RELATIVE`framework/modules/navigation/assets/less/dropdown-bootstrap.less"}

{/css}

<!-- navigation bar/menu -->
<div id="topnavbar" class="navigation navbar navbar-{if $smarty.const.MENU_LOCATION}{$smarty.const.MENU_LOCATION}{else}fixed-top{/if}">
    <div class="navbar-inner">
        <div class="container">
            <!-- toggle for collapsed/mobile navbar content -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <!-- menu header -->
            <a class="brand" href="{$smarty.const.URL_FULL}">{$smarty.const.ORGANIZATION_NAME}</a>
            <!-- menu -->
            <div class="nav-collapse collapse">
                <ul class="nav{if $smarty.const.MENU_ALIGN == 'right'} pull-right{/if}">
                    {getnav type='hierarchy' assign=hierarchy}
                    {bootstrap_navbar menu=$hierarchy length=$smarty.const.MENU_LENGTH|default:2}
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="navbar-spacer"></div>
<div class="navbar-spacer-bottom"></div>

{script unique="navbar-fix" bootstrap="dropdown,collapse,transition"}
{literal}
    $('.dropdown-toggle').click(function(e) {
        e.preventDefault();
        setTimeout($.proxy(function() {
            if ('ontouchstart' in document.documentElement) {
                $(this).siblings('.dropdown-backdrop').off().remove();
            }
        }, this), 0);
    });

    $(document).ready(function(){
        function setTopPadding(admin) {

            if ({/literal}{$user->getsToolbar && $smarty.const.SLINGBAR_TOP == 1}{literal}) {
                $adminbar = $('#admintoolbar').height();
                if ($adminbar == 0) $adminbar = 30;
            } else {
                $adminbar = 0;
            }
            if ($(document.body).width() >= {/literal}{$smarty.const.MENU_WIDTH}{literal} - 15) {  // non-collapsed navbar
                if ($('.navbar-fixed-top').length != 0) {  // fixed top menu
                    $(document.body).css('padding-top', $('#topnavbar').height() + 10 + $adminbar);
                } else if ($('.navbar-fixed-bottom').length != 0) {  // fixed bottom menu
                    $(document.body).css('padding-top', $adminbar);
                    $('.navbar-fixed-bottom').css('margin-top', 0);
                    $(document.body).css('padding-bottom', $('#topnavbar').height() - 45);
                } else {  // static top menu
                    $(document.body).css('padding-top', 0);
                }
                if (admin) $('.navbar-fixed-top').css('margin-top', $adminbar);
                $('.navbar-static-top').css('margin-top', $adminbar);
            } else {  // collapsed navbar
                if ($('.navbar-fixed-top').length != 0 || $('.navbar-static-top').length != 0) {  // fixed top or static top menu
                    $(document.body).css('padding-top', 0);
                } else if ($('.navbar-fixed-bottom').length != 0) {  // fixed bottom menu
                    $(document.body).css('padding-top', $adminbar);
                    $(document.body).css('padding-bottom', 0);
                }
                if (admin) $('.navbar-fixed-top').css('margin-top', $adminbar);
                $('.navbar-static-top').css('margin-top', $adminbar);
            }
        };
        setTopPadding();
        $(window).resize(function(){
            setTopPadding(true);
        });
    });
{/literal}
{/script}
