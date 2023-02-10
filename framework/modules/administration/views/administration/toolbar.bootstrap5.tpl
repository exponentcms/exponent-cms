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

{$lessvars = ["btn_size" => "small"]}

{css unique="newui" scssprimer="`$smarty.const.PATH_RELATIVE`external/bootstrap5/scss/newui.scss" lessvars=$lessvars}

{/css}
{css unique="dropdown-toolbar" lesscss="`$smarty.const.PATH_RELATIVE`framework/modules/navigation/assets/less/dropdown-bootstrap.less"}

{/css}
{css unique="exp-toolbar" lesscss="`$asset_path`less/exp-toolbar.less"}
{if $top}
    {if (!$smarty.const.MENU_LOCATION || $smarty.const.MENU_LOCATION == 'fixed-top')}
        @media (min-width: {$smarty.const.MENU_WIDTH}px) {
            body {
                margin-top: 100px;
            }
            #topnavbar.fixed-top {
                margin-top: 50px;
            }
            {* #topnavbar.navbar-spacer {
                height: 74px;
            }*}
        }
        @media screen and (max-width: {$smarty.const.MENU_WIDTH}px) {
            body {
        		margin-top: 100px;
        	}
            #topnavbar.fixed-top {
                margin-top: 50px;
            }
        }
    {elseif $smarty.const.MENU_LOCATION == 'static-top'}
        #topnavbar.navbar.static-top {
            margin-top: 50px;
        }
        #topnavbar.navbar-spacer {
            height: 0;
        }
    {elseif $smarty.const.MENU_LOCATION == 'fixed-bottom'}
        #topnavbar.navbar-spacer {
            height: 50px;
        }
    {/if}
{else}
    {if $smarty.const.MENU_LOCATION == 'fixed-bottom'}
        #topnavbar.fixed-bottom {
            bottom: 50px;
        }
        #topnavbar.navbar-spacer {
            height: 0;
        }
        #topnavbar.navbar-spacer-bottom {
            height: 50px;
        }
        #topnavbar.menu-spacer-bottom {
            height : 75px;
        }
    {/if}
{/if}
    .exp-skin .navbar.fixed-bottom li.dropdown-submenu ul.dropdown-menu {
        top: auto!important;
        bottom: -6px;
    }
    .exp-skin .navbar.fixed-bottom .dropdown-menu:before {
        border-bottom: 0px solid transparent !important;
        border-top: 7px solid rgba(0, 0, 0, 0.2);
        top: auto !important; bottom: -7px;
    }
    .exp-skin .navbar.fixed-bottom .dropdown-menu:after  {
        border-bottom: 0px solid transparent !important;
        border-top: 6px solid white;
        top: auto !important; bottom: -6px;
    }
{/css}

{* define the function to draw out the menus *}
{function name=menu level=0}
    {if is_array($data.submenu)}
        <li class="dropdown-submenu">
            <a id="dropdownMenu{$data.submenu.id}" class="dropdown-item" data-bs-toggle="dropdown" href="#">{if $smarty.const.USE_BOOTSTRAP_ICONS && $data.iconbs}<i class="{$data.iconbs}"></i>{elseif $data.icon5}<i class="{$data.icon5} fa-fw"></i>{/if} {$data.text}</a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu{$data.submenu.id}">
                {foreach from=$data.submenu.itemdata item=mitem name=sbmenutwo}
                    {menu data=$mitem}
                {/foreach}
            </ul>
        </li>
    {else}
        {if $data.info}
            <li role="presentation" class="dropdown-header">
                {$data.text}
            </li>
        {else}
            <li role="menuitem">
                <a id="{$data.id}" href="{$data.url|default:'#'}" class="dropdown-item">{if $smarty.const.USE_BOOTSTRAP_ICONS && $data.iconbs}<i class="{$data.iconbs}"></i>{elseif $data.icon5}<i class="{$data.icon5} fa-fw"></i>{/if} {$data.text}</a>
            </li>
        {/if}
        {if $data.divider}
            <li class="dropdown-divider"> </li>
        {/if}
    {/if}
{/function}

<div class="exp-skin">
    <header id="admin-toolbar" class="navbar navbar-expand-sm navbar-dark bg-info fixed-{if $top}top{else}bottom{/if}" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        {*<div class="navbar-header">*}
            {*<button type="button" class="navbar-toggle navbar-toggler hidden-sm-up" data-bs-toggle="collapse"*}
                    {*data-bs-target="#admin-navbar-collapse-1">*}
                {*<span class="visually-hidden">{'Toggle navigation'|gettext}</span>*}
                {*<span class="icon-bar"></span>*}
                {*<span class="icon-bar"></span>*}
                {*<span class="icon-bar"></span>&#9776;*}
            {*</button>*}
            <a class="navbar-brand" href="{$smarty.const.URL_FULL}" aria-label="{'Exponent Logo'|gettext}">
                {exp_include file="logo.tpl"}
            </a>
            {*<a class="navbar-brand" href="{$smarty.const.URL_FULL}">{$smarty.const.ORGANIZATION_NAME}</a>*}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#admin-navbar-collapse-1" aria-controls="navbarNav" aria-expanded="false" aria-label="{'Toggle navigation'|gettext}">
                Menu
                <span class="navbar-toggler-icon"></span>
            </button>
        {*</div>*}

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="admin-navbar-collapse-1">
            {foreach from=$menu item=topnav name=tbmenu}
                <ul class="nav navbar-nav{if $topnav.alignright} ms-auto{/if}">
                    <li class="dropdown nav-item">
                        <a href="#" id="dropdownMenu{$topnav.icon}" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">{if $smarty.const.USE_BOOTSTRAP_ICONS && $topnav.iconbs}<i class="{$topnav.iconbs}"></i>{elseif $topnav.icon5}<i class="{$topnav.icon5} fa-fw"></i>{/if} {$topnav.text} </a>
                        <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu{$topnav.icon}">
                            {foreach from=$topnav.submenu.itemdata item=subitem name=sbmenu}
                                {menu data=$subitem}
                            {/foreach}
                        </ul>
                    </li>
                </ul>
            {/foreach}
        </div>
        <!-- /.navbar-collapse -->
    </div>
    </header>
</div>

{if $smarty.const.DB_ENGINE === "mysqli"}
    {$db_driver = "server"}
    {$ns = ""}
{else}
    {$db_driver = "mssql"}
    {$dbo = $smarty.const.DB_SCHEMA}
    {$ns = "&ns="|cat:$dbo}
{/if}
{script unique="z-admin2" bootstrap="dropdown,collapse"}
{literal}
    jQuery(document).ready(function($) {
        var err = function () {
            alert("{/literal}{"Your popup blocker has prevented the file manager from opening"|gettext}{literal}");
        }

        var phpwindow = function (){
            var win = window.open('{/literal}{link controller=administration action=phpinfo ajax_action=1}{literal}');
            if (!win) { err(); }
        }

        var adminerwindow = function (){
            var win = window.open(EXPONENT.PATH_RELATIVE+'external/adminer/admin.php?{/literal}{$db_driver}{literal}={/literal}{$smarty.const.DB_HOST|escape:"url"}{literal}&username={/literal}{$smarty.const.DB_USER}{literal}&db={/literal}{$smarty.const.DB_NAME}{literal}{/literal}{$ns}{literal}');
            if (!win) { err(); }
        }

        var docswindow = function (){
            var win = window.open('http://docs.exponentcms.org/help/switch_version/version/{/literal}{expVersion::getVersion(true, false, false)}{literal}');
            if (!win) { err(); }
        }

        var forumswindow = function (){
            var win = window.open('http://forums.exponentcms.org');
            if (!win) { err(); }
        }

        var reportbugwindow = function (){
            var win = window.open('https://github.com/exponentcms/exponent-cms/issues/new');
            if (!win) { err(); }
        }

        var filepickerwindow = function (){
            var win = window.open('{/literal}{link controller=file action=picker ajax_action=1 update=noupdate}{literal}', 'IMAGE_BROWSER','left=0,top=0,scrollbars=yes,width={/literal}{$smarty.const.FM_WIDTH}{literal},height={/literal}{$smarty.const.FM_HEIGHT}{literal},toolbar=no,resizable=yes,status=0');
            if (!win) { err(); }
        }

        var fileuploaderwindow = function (){
            var win = window.open('{/literal}{link controller=file action=uploader ajax_action=1 update=noupdate}{literal}', 'IMAGE_BROWSER','left=0,top=0,scrollbars=yes,width={/literal}{$smarty.const.FM_WIDTH}{literal},height={/literal}{$smarty.const.FM_HEIGHT}{literal},toolbar=no,resizable=yes,status=0');
            if (!win) { err(); }
        }

        var workflowtoggle = function (e){
            if (!confirm('{/literal}{if $smarty.const.ENABLE_WORKFLOW}{'Turn Workflow off (you will lose all revisions)'|gettext}{else}{'Turn Workflow on'|gettext}{/if}{literal}?')) {
                e.preventDefault();
                return false;
            }
        }

        $('#phpinfo-toolbar').on('click', phpwindow);
        $('#reportabug-toolbar').on('click', reportbugwindow);
        $('#manage-db').on('click', adminerwindow);
        $('#docs-toolbar').on('click',docswindow);
        $('#forums-toolbar').on('click',forumswindow);
        $('#filemanager-toolbar').on('click',filepickerwindow);
        $('#fileuploader-toolbar').on('click',fileuploaderwindow);
        $('#workflow-toggle').on('click',workflowtoggle);
    });

    $('#admin-toolbar').find('.exp-skin .dropdown-toggle').click(function(e) {
        e.preventDefault();
        setTimeout($.proxy(function() {
            if ('ontouchstart' in document.documentElement) {
                $(this).siblings('.dropdown-backdrop').off().remove();
            }
        }, this), 0);
    });

    /**
      * NAME: Bootstrap 3 Triple Nested Sub-Menus
      * This script will active Triple level multi drop-down menus in Bootstrap 3.*
      */
    // $('.exp-skin ul.dropdown-menu [data-bs-toggle=dropdown]').on('click', function(event) {
    //     // Avoid following the href location when clicking
    //     event.preventDefault();
    //     if ($(this).hasClass('tick')) {
    //         $(this).removeClass('tick');
    //         $(this).parent().removeClass('show');
    //     } else {
    //         $(this).addClass('tick');
    //         // Avoid having the menu to close when clicking
    //         event.stopPropagation();
    //         // Re-add .open to parent sub-menu item
    //         $(this).parent().addClass('show');
    //         $(this).parent().find("ul").parent().find("li.dropdown").addClass('show');
    //     }
    // });

    /**
    * Auto-adjust (dynamically) top margins based on navbar type and slingbar display
    */
    $(document).ready(function(){
        if ({/literal}{$top}{literal}) {  // fixed top slingbar menu
            if ({/literal}{($smarty.const.MENU_LOCATION == 'fixed-top') + 0}{literal}) {  // fixed top main menu
                $(document.body).css('margin-top', $('#admin-toolbar').outerHeight() + $('#topnavbar').outerHeight() + 15);
            } else if ({/literal}{($smarty.const.MENU_LOCATION == 'fixed-bottom') + 0}{literal}) {  // fixed bottom main menu
                $(document.body).css('margin-top', 15);
            }
            $(document.body).css('margin-bottom', 0);
        } else {  // fixed bottom slingbar menu
            $(document.body).css('margin-top', $('#topnavbar').outerHeight());
            $(document.body).css('margin-bottom', $('#admin-toolbar').outerHeight() + 10);
        }
    });
{/literal}
{/script}
