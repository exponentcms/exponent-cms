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

{css unique="flyout" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/containermodule/assets/css/flyout.css"}

{/css}

<div class="module container flyout" style="display: none;">
    {showmodule module='container' view="Default" source="@flyoutsidebar" chrome=true}
</div>
<a class="triggerlogin" href="#">{'View Panel'|gettext}</a>

{script unique="flyoutsidebar" jquery=1}
{literal}
$(document).ready(function(){
	$(".triggerlogin").click(function(){
		$(".flyout").toggle("fast");
		$(this).toggleClass("active");
		return false;
	});
});
{/literal}
{/script}
