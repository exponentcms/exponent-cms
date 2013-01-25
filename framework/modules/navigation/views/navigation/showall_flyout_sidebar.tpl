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

{if $config.which_side == 'left'}{$side='_left'}{else}{$side=''}{/if}
{if $config.view_scope == 'sectional' || $config.view_scope == 'top-sectional'}{$scope=$config.view_scope}{else}{$scope='global'}{/if}

{css unique="flyout" link="`$asset_path`css/flyout`$side`.css"}

{/css}

<div class="module container flyout{$side}" style="display: none;">
    {showmodule module='container2' action="showall" view="showall" source="@flyout_sidebar" scope=$scope chrome=true}
</div>
<a class="triggerlogin" href="#" title="{'Click to open this panel'|gettext}">{'View Panel'|gettext}</a>

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
