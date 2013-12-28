{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{if $config.which_side == 'left'}
    {$side = '_left'}
      {$class = 'rotate-90 origin-tl fix-rotate-90'}
{else}
    {$side = ''}
    {$class = 'rotate-270 origin-tr fix-rotate-270'}
{/if}
{if $config.view_scope == 'sectional' || $config.view_scope == 'top-sectional'}{$scope=$config.view_scope}{else}{$scope='global'}{/if}

{if empty($config.top_mx)}{$topmx = 'auto'}{else}{$topmx = $config.top_mx}{/if}
{css unique="flyout" link="`$asset_path`css/flyout`$side`.css"}
{literal}
.thetop {
    top : {/literal}{$topmx}{literal}px
}
{/literal}
{/css}

<div class="module container flyout{$side} thetop" style="display: none;">
    {if !empty($moduletitle) && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}
        {$tag = $moduletitle}
        <h4>{$moduletitle}</h4>
    {else}
        {$tag = 'View Panel'|gettext}
    {/if}
    {showmodule module='container' action="showall" view="showall" source="@flyout_sidebar_`$__loc->src`" scope=$scope chrome=true}
</div>
<a class="triggerlogin {$class} thetop" href="#" title="{'Click to open this panel'|gettext}">{$tag}</a>

{script unique="flyoutsidebar" jquery=1}
{literal}
$(document).ready(function(){
	$(".triggerlogin").click(function(){
		$(".flyout{/literal}{$side}{literal}").toggle("fast");
		$(this).toggleClass("active");
        if ($(this).hasClass('active'))  {
            $(this).attr('title','{/literal}{'Click to close this panel'|gettext}{literal}');
        } else {
            $(this).attr('title','{/literal}{'Click to open this panel'|gettext}{literal}');
        }
		return false;
	});
});
{/literal}
{/script}
