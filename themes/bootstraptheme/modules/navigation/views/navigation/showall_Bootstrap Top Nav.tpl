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

{css unique="bootstrap-top-nav"  link="`$smarty.const.PATH_RELATIVE`framework/modules/navigation/assets/css/dropdown-bootstrap.css"}
{if $smarty.const.MENU_LOCATION == 'static-top'}
    .navbar-spacer {
        height: 0;
    }
{/if}
{/css}

<div class="navigation navbar navbar-{if $smarty.const.MENU_LOCATION}{$smarty.const.MENU_LOCATION}{else}fixed-top{/if}">
    <div class="navbar-inner">
        <div class="container">
            <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="{$smarty.const.URL_FULL}">{$smarty.const.ORGANIZATION_NAME}</a>
            <div class="nav-collapse collapse">
            <ul class="nav{if $smarty.const.MENU_ALIGN == 'right'} pull-right{/if}">
                {getnav type='hierarchy' assign=hierarchy}
                {bootstrap_navbar menu=$hierarchy}
            </ul>
            </div>
        </div>
    </div>
</div>
<div class="navbar-spacer"></div>
<div class="navbar-spacer-bottom"></div>

{script unique="navbar-fix" jquery=1}
{literal}
$('.dropdown-toggle').click(function(e) {
  e.preventDefault();
  setTimeout($.proxy(function() {
    if ('ontouchstart' in document.documentElement) {
      $(this).siblings('.dropdown-backdrop').off().remove();
    }
  }, this), 0);
});
{/literal}
{/script}
