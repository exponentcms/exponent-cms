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

{$lessvars = ["menu_width" => "769"]}

{css unique="dropdown-toolbar" lesscss="`$smarty.const.PATH_RELATIVE`framework/modules/navigation/assets/less/dropdown-bootstrap.less" lessvars=$lessvars}

{/css}
{css unique="exp-toolbar" lesscss="`$asset_path`less/exp-toolbar.less"}
    .exp-skin .navbar.navbar-fixed-bottom li.dropdown-submenu ul.dropdown-menu {
        top: auto!important;
        bottom: -6px;
    }
    .exp-skin .navbar.navbar-fixed-bottom .dropdown-menu:before {
        border-bottom: 0px solid transparent !important;
        border-top: 7px solid rgba(0, 0, 0, 0.2);
        top: auto !important; bottom: -7px;
    }
    .exp-skin .navbar.navbar-fixed-bottom .dropdown-menu:after  {
        border-bottom: 0px solid transparent !important;
        border-top: 6px solid white;
        top: auto !important; bottom: -6px;
    }
{/css}

{* define the function to draw out the menus *}
{function name=menu level=0}
{if is_array($data.submenu)}
    <li class="dropdown-submenu">
        <a id="dropdownMenu{$data.submenu.id}" data-toggle="dropdown" href="#">{if $data.icon}<i class="fa {$data.icon} fa-fw"></i>{/if} {$data.text}</a>
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
            <a id="{$data.id}" href="{$data.url|default:'#'}">{if $data.icon}<i class="fa {$data.icon} fa-fw"></i>{/if} {$data.text}</a>
        </li>
    {/if}
    {if $data.divider}
        <li class="divider"> </li>
    {/if}
{/if}
{/function}

<div class="exp-skin">
<header id="admin-toolbar" class="navbar navbar-default navbar-fixed-{if $top}top{else}bottom{/if} navbar-inverse" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse"
          data-target="#admin-navbar-collapse-1">
          <span class="sr-only">{'Toggle navigation'|gettext}</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{$smarty.const.URL_FULL}" aria-label="{'Exponent Logo'|gettext}">
          {exp_include file="logo.tpl"}
      </a>
  </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="admin-navbar-collapse-1">
        {foreach from=$menu item=topnav name=tbmenu}
        <ul class="nav navbar-nav{if $topnav.alignright} navbar-right{/if}">
            <li class="dropdown">
                <a href="#" id="dropdownMenu{$topnav.icon}" class="dropdown-toggle" data-toggle="dropdown">{if $topnav.icon}<i class="fa {$topnav.icon} fa-fw"></i>{/if} {$topnav.text} <b class="caret"></b></a>
                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu{$topnav.icon}">
                    {foreach from=$topnav.submenu.itemdata item=subitem name=sbmenu}
                        {menu data=$subitem}
                    {/foreach}
                </ul>
            </li>
        </ul>
        {/foreach}
    </div><!-- /.navbar-collapse -->
</header>
</div>

{script unique="z-admin2" bootstrap="dropdown,collapse,transition"}
{literal}
    jQuery(document).ready(function($) {
        var adminerwindow = function (){
            var win = window.open('{/literal}{$smarty.const.PATH_RELATIVE}{literal}external/adminer/admin.php?server={/literal}{$smarty.const.DB_HOST}{literal}&username={/literal}{$smarty.const.DB_USER}{literal}&db={/literal}{$smarty.const.DB_NAME}{literal}');
            if (!win) { err(); }
        }

        var docswindow = function (){
            var win = window.open('http://docs.exponentcms.org');
            if (!win) { err(); }
        }

        var forumswindow = function (){
            var win = window.open('http://forums.exponentcms.org');
            if (!win) { err(); }
        }

        var reportbugwindow = function (){
            var win = window.open('http://exponentcms.lighthouseapp.com/projects/61783-exponent-cms/tickets/new');
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

        $('#reportabug-toolbar').on('click', reportbugwindow);
        $('#manage-db').on('click', adminerwindow);
        $('#docs-toolbar').on('click',docswindow);
        $('#forums-toolbar').on('click',forumswindow);
        $('#filemanager-toolbar').on('click',filepickerwindow);
        $('#fileuploader-toolbar').on('click',fileuploaderwindow);
        $('#workflow-toggle').on('click',workflowtoggle);
    });

    $('.exp-skin .dropdown-toggle').click(function(e) {
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
    $('.exp-skin ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
        // Avoid following the href location when clicking
        event.preventDefault();
        if ($(this).hasClass('tick')) {
            $(this).removeClass('tick');
            $(this).parent().removeClass('open');
        } else {
            $(this).addClass('tick');
            // Avoid having the menu to close when clicking
            event.stopPropagation();
            // Re-add .open to parent sub-menu item
            $(this).parent().addClass('open');
            $(this).parent().find("ul").parent().find("li.dropdown").addClass('open');
        }
    });

    $(document).ready(function(){
        if ({/literal}{$top}{literal}) {  // fixed top menu
            $(document.body).css('margin-top', $('#admin-toolbar').height()+10);
            $(document.body).css('margin-bottom', 0);
        } else {  // fixed bottom menu
            $(document.body).css('margin-top', 0);
            $(document.body).css('margin-bottom', $('#admin-toolbar').height()+10);
        }
    });
{/literal}
{/script}