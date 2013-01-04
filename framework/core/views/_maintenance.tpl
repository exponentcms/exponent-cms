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

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.LANG_CHARSET}" />
		<title>{$smarty.const.SITE_TITLE} :: {'Down for Maintenance.'|gettext}</title>
		{css unique="maintenance"}
        {literal}
			div {
				font-size: 10pt;
				font-family: Arial, sans-serif;
				font-weight: normal;
				color: #333;
			}
		{/literal}
        {/css}
	</head>
	<body>
	<div style="border: 1px solid black; margin: 15%; padding: 3em;">
		{$smarty.const.MAINTENANCE_MSG_HTML}
        {if $db_down}
        <h3 style="color:red">{'Database is currently Off-line!'|gettext}</h3>
        {/if}
        <!--a href="login.php">{'Administrator Login'|gettext}</a-->
		<h3>{'Administrator Login'|gettext}</h3>
		{chain controller=login view=showlogin_stacked title="Administrators Login"|gettext}
	</div>
	</body>
</html>
