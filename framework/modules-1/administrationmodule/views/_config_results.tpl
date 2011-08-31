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
{if $success == 1}
<br /><br />{'Configuration Saved!'|gettext} <br /><a class="mngmntlink" href="{$backlink}">{'Continue'|gettext}</a><hr size="1" />
{else}
<div class="error">{'Errors were encountered with your database connection settings'|gettext}:</div>
<div style="padding-left: 15px;">
{$errors}
<br /><br />{'Site configuration changes were not saved.'|gettext}<br /><a class="mngmntlink" href="{$smarty.server.HTTP_REFERER}">{'Reconfigure'|gettext}</a><br /><br />
</div>
{/if}
