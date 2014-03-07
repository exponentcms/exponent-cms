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


{css unique="slingbar" link="`$asset_path`css/exp-toolbar.css"}

{/css}

{* define the function to draw out the menus *}
{function name=menu level=0}
{if is_array($data.submenu)}
    <li class="dropdown-submenu">
        <a href="#">{if $data.icon}<i class="fa {$data.icon} fa-fw"></i>{/if} {$data.text}</a>
        <ul class="dropdown-menu">
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
     <li>
         <a id="{$data.id}" href="{$data.url|default:'#'}">{if $data.icon}<i class="fa {$data.icon} fa-fw"></i>{/if} {$data.text}</a>
    </li>
    {/if}
{/if}
{/function}

<div class="exp-skin">  

<nav id="admin-toolbar" class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="{$smarty.const.URL_FULL}">
        {include file="logo.tpl"}
    </a>
  </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        {foreach from=$menu item=topnav name=tbmenu}
        <ul class="nav navbar-nav{if $topnav.alignright} navbar-right{/if}">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{if $topnav.icon}<i class="fa {$topnav.icon} fa-fw"></i>{/if} {$topnav.text} <b class="caret"></b></a>
                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                    {foreach from=$topnav.submenu.itemdata item=subitem name=sbmenu}
                        {menu data=$subitem}
                    {/foreach}
                </ul>
            </li>
        </ul>
        {/foreach}
    </div><!-- /.navbar-collapse -->
</nav>
</div>

{script unique="z-admin" jquery=1 src="`$smarty.const.PATH_RELATIVE`external/bootstrap3/js/dropdown.js"}

{/script}

{script unique="z-admin2" jquery=1 src="`$smarty.const.PATH_RELATIVE`external/bootstrap3/js/collapse.js"}
{literal}
jQuery(document).ready(function($) {

    $('body').css('margin-top', $('#admin-toolbar').height()+10);

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

    $('#reportabug-toolbar').on('click', reportbugwindow);
    $('#manage-db').on('click', adminerwindow);
    $('#docs-toolbar').on('click',docswindow);
    $('#forums-toolbar').on('click',forumswindow);
    $('#filemanager-toolbar').on('click',filepickerwindow);
    $('#fileuploader-toolbar').on('click',fileuploaderwindow);

});
{/literal}
{/script}

