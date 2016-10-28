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

{css unique="z-dropdown-bootstrap" lesscss="`$asset_path`less/dropdown-bootstrap.less"}

{/css}
{css unique="mega" lesscss="`$asset_path`less/yamm.less"}

{/css}

<nav id="topnavbar" class="navigation navbar yamm navbar-default {if $smarty.const.MENU_LOCATION}navbar-{$smarty.const.MENU_LOCATION}{/if}" role="navigation">
    <div class="">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">{'Toggle navigation'|gettext}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- menu header -->
            <a class="navbar-brand" href="{$smarty.const.URL_FULL}">{$smarty.const.ORGANIZATION_NAME}</a>
        </div>
        <!-- menu -->
        <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
            <ul class="nav navbar-nav{if $smarty.const.MENU_ALIGN == 'right'} navbar-right pull-right{/if}">
                {getnav type='hierarchy' assign=hierarchy}
                {bootstrap_navbar menu=$hierarchy length=$smarty.const.MENU_LENGTH|default:2}
            </ul>
        </div>
    </div>
</nav>
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

    $(document).on('click', '.yamm .dropdown-menu', function(e) {
        e.stopPropagation()
    });

    /**
      * NAME: Bootstrap 3 Triple Nested Sub-Menus
      * This script will active Triple level multi drop-down menus in Bootstrap 3.*
      */
    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
        // Avoid following the href location when clicking
        event.preventDefault();
        if ($(this).hasClass('tick')) {
            $(this).removeClass('tick');
            $(this).parent().removeClass('open');
            if ($(this).hasClass('exp-trigger')) {
                // Avoid having the menu to close when clicking
                event.stopPropagation();
            }
        } else {
            $(this).addClass('tick');
            // Avoid having the menu to close when clicking
            event.stopPropagation();
            // Re-add .open to parent sub-menu item
            $(this).parent().addClass('open');
            $(this).parent().find("ul").parent().find("li.dropdown").addClass('open');
        }
    });

    /**
    * Auto-adjust (dynamically) top margins based on navbar type and slingbar display
    */
    $(document).ready(function(){
    function setTopPadding() {
    if ({/literal}{($user->getsToolbar == 1 && $smarty.const.SLINGBAR_TOP == 1)?1:0}{literal}) {
                $adminbar = $('#admin-toolbar').height();
                if ($adminbar == 0) $adminbar = 50;
            } else {
                $adminbar = 0;
            }
            if (!$adminbar) {
                $bump = 15;
            } else {
                $bump = 15;
            }
            if ($(document.body).width() >= {/literal}{$smarty.const.MENU_WIDTH}{literal} - 15) {  // non-collapsed navbar
                if ($('#topnavbar.navbar-fixed-top').length != 0) {  // fixed top menu
//                    $(document.body).css('padding-top', $('#topnavbar').height() + 10 + $adminbar);
                    $(document.body).css('margin-top', $('#topnavbar').height() + $adminbar + $bump);
                    $('#topnavbar.navbar-fixed-top').css('margin-top', $adminbar);
                } else if ($('#topnavbar.navbar-static-top').length != 0) {  // static top menu
                    $(document.body).css('padding-top', 0);
                    $('#topnavbar.navbar-static-top').css('margin-top', $adminbar);
                } else if ($('#topnavbar.navbar-fixed-bottom').length != 0) {  // fixed bottom menu
                    $(document.body).css('padding-top', $adminbar + $bump);
                    $(document.body).css('padding-bottom', $('#topnavbar').height() - 45);
                    $('#topnavbar.navbar-fixed-bottom').css('margin-top', 0);
                }
            } else {  // collapsed navbar
                if ($('#topnavbar.navbar-fixed-top').length != 0) {  // fixed top menu
//                    $(document.body).css('padding-top', $adminbar + $('#topnavbar').height());
                    $(document.body).css('margin-top', $('#topnavbar').height() + $adminbar + $bump);
                    $('#topnavbar.navbar-fixed-top').css('margin-top', $adminbar);
                } else if ($('#topnavbar.navbar-static-top').length != 0) {  // static top menu
//                    $(document.body).css('padding-top', $adminbar);
                    $('#topnavbar.navbar-static-top').css('margin-top', $adminbar);
                } else if ($('#topnavbar.navbar-fixed-bottom').length != 0) {  // fixed bottom menu
                    $(document.body).css('padding-top', $adminbar + $bump);
                    $(document.body).css('padding-bottom', 0);
                }
            }
        };
        setTopPadding();
        $(window).resize(function(){
            setTopPadding();
        });
    });
{/literal}
{/script}
