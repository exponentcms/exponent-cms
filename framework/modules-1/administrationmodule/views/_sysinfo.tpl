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
{if $override_style == 1}
<div class="form_title">{$_TR.form_title}</div>
<div class="form_header">{$_TR.form_header}
<br /><br />
<a href="{link action=sysinfo_download}">{$_TR.export}</a>
</div>
<style type="text/css">
{literal}
div.center {
	text-align:center;
}

.p {
	text-align: left;
}

.e {
	background-color: #ccccff;
	font-weight: bold;
	color: #000000;
}

.h {
	background-color: #9999cc;
	font-weight: bold;
	color: #000000;
}
.v {
	background-color: #cccccc;
	color: #000000;
}

.center table {
	margin-left: auto;
	margin-right: auto;
	text-align: left;
}

{/literal}
</style>
{/if}
{$phpinfo}
<br />

<div class="center">

<table border="0" cellpadding="3" width="600">
<tr class="h"><td>
<h1 class="p">{$_TR.exponent_cms}</h1>
</td></tr>
</table><br />
<table border="0" cellpadding="3" width="600">
<tr><td class="e">{$_TR.software_version}</td><td class="v">{$smarty.const.EXPONENT_VERSION_MAJOR}.{$smarty.const.EXPONENT_VERSION_MINOR}.{$smarty.const.EXPONENT_VERSION_REVISION}{if $smarty.const.EXPONENT_VERSION_TYPE != ''}-{$smarty.const.EXPONENT_VERSION_TYPE}{$smarty.const.EXPONENT_VERSION_ITERATION}{/if}</td></tr>
<tr><td class="e">{$_TR.build_date} </td><td class="v">{if $smarty.const.EXPONENT_VERSION_BUILDDATE == "%%BUILDDATE%%"}<i>{$_TR.devel_version}</i>{else}{$smarty.const.EXPONENT_VERSION_BUILDDATE|format_date:"%a %d %Y %H:%M:%S"}{/if}</td></tr>
<tr><td class="e">{$_TR.path_relative}</td><td class="v">{$smarty.const.PATH_RELATIVE}</td></tr>
<tr><td class="e">{$_TR.base}</td><td class="v">{$smarty.const.BASE}</td></tr>
</table><br />

<h1>{$_TR.installed_modules}</h1>
{foreach from=$modules item=mod key=class}
<h2><a name="module_{$class}">{$class}</a></h2>
<table border="0" cellpadding="3" width="600">
<tr><td class="e">{$_TR.name}</td><td class="v">{$mod.name}</td></tr>
<tr><td class="e">{$_TR.author}</td><td class="v">{$mod.author}</td></tr>
</table><br />
{/foreach}

<h1>{$_TR.installed_subsystems}</h1>

{foreach from=$subsystems item=sys key=class}
<h2><a name="subsystem_{$class}">{$class}</a></h2>
<table border="0" cellpadding="3" width="600">
<tr><td class="e">{$_TR.name}</td><td class="v">{$sys.name}</td></tr>
<tr><td class="e">{$_TR.author}</td><td class="v">{$sys.author}</td></tr>
<tr><td class="e">{$_TR.version}</td><td class="v">{$sys.version}</td></tr>
</table><br />
{/foreach}
</div>