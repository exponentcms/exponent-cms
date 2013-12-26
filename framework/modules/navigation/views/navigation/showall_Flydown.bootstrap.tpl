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

{css unique="z-dropdown-bootstrap" lesscss="`$asset_path`less/dropdown-bootstrap.less"}

{/css}

<div class="nav-collapse collapse">
    <ul class="nav{if $smarty.const.MENU_ALIGN == 'right'} pull-right{/if}">
        {getnav type='hierarchy' assign=hierarchy}
        {bootstrap_navbar menu=$hierarchy}
    </ul>
</div>

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
