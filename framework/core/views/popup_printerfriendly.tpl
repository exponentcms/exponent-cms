{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
		<title>{$smarty.const.SITE_TITLE}</title>
		<meta http-equiv="Content-Type" content="text/html; charset={$smarty.const.LANG_CHARSET}" />
		<meta name="Generator" value="Exponent Content Management System" />
		<link rel="stylesheet" title="default" href="{$smarty.const.THEME_RELATIVE}style.css" />
		<script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js.php"></script>
		<style type="text/css">
		{literal}
			body {
				background-color: #fff;
				background-image: none;
				margin: 1.5em;
				padding: 0px;
			}
		</style>
		<style type="text/css" media="print">
			div.printer_link {
				display: none;
			}
		</style>
		{/literal}
	</head>
	<body onload="exponentJSinitialize()">
	<div class="printer_link">
		<a href="#" onclick="print(); return false;">{$_TR.print}</a>
		<br />
	</div>
	{$output}
	
	</body>
</html>