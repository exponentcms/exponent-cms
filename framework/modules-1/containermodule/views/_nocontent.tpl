{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

 <html>
	<head>
		<title>{$smarty.const.SITE_TITLE}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}style.css" />
		<link rel="stylesheet" href="{$smarty.const.THEME_RELATIVE}editor.css" />
		<meta name="Generator" value="Exponent Content Management System" />
	</head>
	
	<body style="margin: 0px; padding: 0px;"> 
		{br}
		<div style="font-size: 2em">{'No Content Selected'|gettext}</div>
		<hr size="1"/>{'To use the content from an existing module, click the "Use Existing Content" link to the left to enter the Site Content Selector.  Then, find your module and click "Use This Module\'s Content."'|gettext}<br />
		<em>{'<strong>Note:</strong> you will only be able to re-use content from modules of the same type'|gettext}</em>
	</body>
</html>