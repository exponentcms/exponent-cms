{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
	<h1>{'New'|gettext} {$model_name}</h1>
{/if}

{form action=update}
	{control type=hidden name=id value=$record->id}
	{control type=text name=title label="Company Name"|gettext value=$record->title focus=1}
    {control type=text name=sef_url label="SEF URL"|gettext value=$record->sef_url description='If you don\'t put in an SEF URL one will be generated based on the title provided. SEF URLs can only contain alpha-numeric characters, hyphens, forward slashes, and underscores.'|gettext}
	{control type=text name=website label="Company Website"|gettext value=$record->website}
	{control type=html name=body label="Company Description"|gettext value=$record->body}
	{control type=files name=logo label="Company Logo"|gettext subtype="logo" accept="image/*" value=$record->expFile folder=$config.upload_folder}
	{control type=files name=additional label="Additional Docs"|gettext subtype="additional" value=$record->expFile folder=$config.upload_folder}
	{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
{/form}
