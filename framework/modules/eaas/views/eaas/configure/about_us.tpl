{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

<h2>{'About Us'|gettext}</h2>

{control type="files" name="aboutus_image" subtype="aboutus_image" label="Banner Image"|gettext accept="image/*" value=$config.expFile limit='1'}
{control type="editor" label="About Us HTML"|gettext name="aboutus_body" value=$config.aboutus_body}
{if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
    {control type="text" name="upload_folder" label="Quick Add Upload Subfolder"|gettext value=$config.upload_folder}
{else}
    {control type=dropdown name="upload_folder" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$config.upload_folder}
{/if}
