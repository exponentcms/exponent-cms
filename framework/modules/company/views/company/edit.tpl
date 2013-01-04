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

{if $record->id != ""}
	<h1>{'Editing'|gettext} {$record->title}</h1>
{else}
	<h1>{'New'|gettext} {$modelname}</h1>
{/if}

{form action=update}
	{control type=hidden name=id value=$record->id}
	{control type=text name=title label="Company Name"|gettext value=$record->title}
	{control type=text name=website label="Company Website"|gettext value=$record->website}
	{control type=html name=body label="Company Description"|gettext value=$record->body}
	{control type=files name=logo label="Company Logo"|gettext subtype="logo" value=$record->expFile}
	{control type=files name=additional label="Additional Docs"|gettext subtype="additional" value=$record->expFile}
	{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
{/form}
